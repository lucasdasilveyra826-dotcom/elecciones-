<?php
// ============================================================
// PANTALLA DEL PRESIDENTE DE MESA
// Busca al alumno por DNI y habilita una sola votacion a la vez.
// ============================================================

session_start();
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistemavoto';

try {
    $conexion = new mysqli($host, $user, $pass, $db);
    $conexion->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    exit('No se pudo conectar con la base de datos.');
}

function obtenerLogoEscuela()
{
    $extensionesPermitidas = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'];
    $archivos = glob(__DIR__ . '/imagen/logollano.*') ?: [];

    foreach ($archivos as $archivo) {
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

        if (in_array($extension, $extensionesPermitidas, true)) {
            return 'imagen/' . rawurlencode(basename($archivo));
        }
    }

    return null;
}

if (empty($_SESSION['csrf_presidente'])) {
    $_SESSION['csrf_presidente'] = bin2hex(random_bytes(32));
}

function obtenerInstanciaActiva($conexion)
{
    $resultado = $conexion->query(
        "SELECT id_instancia, descripcion, anio
         FROM instancia_electoral
         WHERE activa = 1
         ORDER BY id_instancia DESC
         LIMIT 1"
    );
    return $resultado->fetch_assoc() ?: null;
}

function obtenerAutorizadoActual($conexion, $idInstancia)
{
    $stmt = $conexion->prepare(
        "SELECT p.id_padron,
                p.fecha_hora_autorizacion,
                a.dni,
                a.nombre,
                a.apellido,
                a.curso,
                a.division
         FROM padron_electoral p
         INNER JOIN alumnos a ON a.id_alumno = p.id_alumno
         WHERE p.id_instancia = ?
           AND p.autorizado = 1
           AND p.estado_voto = 0
         ORDER BY p.fecha_hora_autorizacion DESC, p.id_padron DESC
         LIMIT 1"
    );
    $stmt->bind_param('i', $idInstancia);
    $stmt->execute();
    $autorizado = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $autorizado;
}

function buscarEnPadronPorDni($conexion, $idInstancia, $dni)
{
    $stmt = $conexion->prepare(
        "SELECT p.id_padron,
                p.estado_voto,
                p.autorizado,
                p.fecha_hora_voto,
                p.fecha_hora_autorizacion,
                a.dni,
                a.nombre,
                a.apellido,
                a.curso,
                a.division
         FROM alumnos a
         INNER JOIN padron_electoral p ON p.id_alumno = a.id_alumno
         WHERE a.dni = ? AND p.id_instancia = ?
         LIMIT 1"
    );
    $stmt->bind_param('si', $dni, $idInstancia);
    $stmt->execute();
    $alumno = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $alumno;
}

$logoEscuela = obtenerLogoEscuela();
$instanciaActiva = obtenerInstanciaActiva($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['accion'] ?? '') === 'estado') {
    header('Content-Type: application/json; charset=utf-8');

    if (!$instanciaActiva) {
        echo json_encode(['clave' => null, 'instancia_activa' => false]);
        exit;
    }

    $actual = obtenerAutorizadoActual($conexion, (int) $instanciaActiva['id_instancia']);
    echo json_encode([
        'instancia_activa' => true,
        'clave' => $actual
            ? $actual['id_padron'] . '|' . $actual['fecha_hora_autorizacion']
            : null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$mensaje = '';
$tipoMensaje = '';
$alumnoBuscado = null;
$dniIngresado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenRecibido = $_POST['csrf'] ?? '';

    if (!hash_equals($_SESSION['csrf_presidente'], $tokenRecibido)) {
        $mensaje = 'La sesion del formulario vencio. Recargue la pagina.';
        $tipoMensaje = 'error';
    } elseif (!$instanciaActiva) {
        $mensaje = 'No existe una instancia electoral activa.';
        $tipoMensaje = 'error';
    } else {
        $accion = $_POST['accion'] ?? '';
        $idInstancia = (int) $instanciaActiva['id_instancia'];

        if ($accion === 'buscar') {
            $dniIngresado = preg_replace('/\D+/', '', $_POST['dni'] ?? '');

            if ($dniIngresado === '') {
                $mensaje = 'Ingrese un DNI.';
                $tipoMensaje = 'error';
            } else {
                $alumnoBuscado = buscarEnPadronPorDni($conexion, $idInstancia, $dniIngresado);

                if (!$alumnoBuscado) {
                    $mensaje = 'El DNI no pertenece al padron de la eleccion activa.';
                    $tipoMensaje = 'error';
                }
            }
        }

        if ($accion === 'autorizar') {
            $idPadron = filter_input(INPUT_POST, 'id_padron', FILTER_VALIDATE_INT);

            try {
                if (!$idPadron) {
                    throw new RuntimeException('El registro del padron no es valido.');
                }

                $conexion->begin_transaction();

                $stmtAlumno = $conexion->prepare(
                    "SELECT p.id_padron,
                            p.estado_voto,
                            p.autorizado,
                            a.dni,
                            a.nombre,
                            a.apellido
                     FROM padron_electoral p
                     INNER JOIN alumnos a ON a.id_alumno = p.id_alumno
                     WHERE p.id_padron = ? AND p.id_instancia = ?
                     LIMIT 1
                     FOR UPDATE"
                );
                $stmtAlumno->bind_param('ii', $idPadron, $idInstancia);
                $stmtAlumno->execute();
                $alumno = $stmtAlumno->get_result()->fetch_assoc();
                $stmtAlumno->close();

                if (!$alumno) {
                    throw new RuntimeException('El alumno no pertenece al padron activo.');
                }

                if ((int) $alumno['estado_voto'] === 1) {
                    throw new RuntimeException('El alumno ya emitio su voto.');
                }

                $stmtOtro = $conexion->prepare(
                    "SELECT p.id_padron, a.apellido, a.nombre
                     FROM padron_electoral p
                     INNER JOIN alumnos a ON a.id_alumno = p.id_alumno
                     WHERE p.id_instancia = ?
                       AND p.autorizado = 1
                       AND p.estado_voto = 0
                     ORDER BY p.fecha_hora_autorizacion DESC, p.id_padron DESC
                     LIMIT 1
                     FOR UPDATE"
                );
                $stmtOtro->bind_param('i', $idInstancia);
                $stmtOtro->execute();
                $otro = $stmtOtro->get_result()->fetch_assoc();
                $stmtOtro->close();

                if ($otro && (int) $otro['id_padron'] !== (int) $idPadron) {
                    throw new RuntimeException(
                        'Todavia esta autorizado: ' . $otro['apellido'] . ', ' . $otro['nombre'] .
                        '. Debe votar o cancelarse su autorizacion.'
                    );
                }

                if (!$otro) {
                    $stmtAutorizar = $conexion->prepare(
                        "UPDATE padron_electoral
                         SET autorizado = 1,
                             fecha_hora_autorizacion = NOW()
                         WHERE id_padron = ?
                           AND id_instancia = ?
                           AND estado_voto = 0"
                    );
                    $stmtAutorizar->bind_param('ii', $idPadron, $idInstancia);
                    $stmtAutorizar->execute();

                    if ($stmtAutorizar->affected_rows !== 1) {
                        $stmtAutorizar->close();
                        throw new RuntimeException('No se pudo autorizar al alumno.');
                    }
                    $stmtAutorizar->close();
                }

                $conexion->commit();
                $mensaje = 'Votacion autorizada para ' . $alumno['apellido'] . ', ' . $alumno['nombre'] . '.';
                $tipoMensaje = 'exito';
            } catch (Throwable $e) {
                try {
                    $conexion->rollback();
                } catch (Throwable $ignorado) {
                }
                $mensaje = $e->getMessage();
                $tipoMensaje = 'error';
            }
        }

        if ($accion === 'cancelar') {
            $idPadron = filter_input(INPUT_POST, 'id_padron', FILTER_VALIDATE_INT);

            if (!$idPadron) {
                $mensaje = 'La autorizacion no es valida.';
                $tipoMensaje = 'error';
            } else {
                $stmtCancelar = $conexion->prepare(
                    "UPDATE padron_electoral
                     SET autorizado = 0
                     WHERE id_padron = ?
                       AND id_instancia = ?
                       AND autorizado = 1
                       AND estado_voto = 0"
                );
                $stmtCancelar->bind_param('ii', $idPadron, $idInstancia);
                $stmtCancelar->execute();

                if ($stmtCancelar->affected_rows === 1) {
                    $mensaje = 'La autorizacion fue cancelada.';
                    $tipoMensaje = 'exito';
                } else {
                    $mensaje = 'La autorizacion ya no estaba activa.';
                    $tipoMensaje = 'error';
                }
                $stmtCancelar->close();
            }
        }
    }
}

$autorizadoActual = $instanciaActiva
    ? obtenerAutorizadoActual($conexion, (int) $instanciaActiva['id_instancia'])
    : null;

$claveInicial = $autorizadoActual
    ? $autorizadoActual['id_padron'] . '|' . $autorizadoActual['fecha_hora_autorizacion']
    : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presidente de mesa</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background: #eef2f7;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
        }
        header { padding: 6px 18px; background: #16243d; color: white; }
        .encabezado {
            max-width: 1100px;
            margin: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-escuela,
        .logo-escuela-vacio {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            border-radius: 7px;
            object-fit: contain;
            background: white;
        }
        .logo-escuela-vacio {
            display: grid;
            place-items: center;
            padding: 3px;
            color: #475569;
            font-size: .48rem;
            font-weight: 800;
            line-height: 1.05;
            text-align: center;
        }
        .datos-encabezado { min-width: 0; flex: 1; }
        .nombre-escuela {
            margin-bottom: 4px;
            overflow: hidden;
            color: white;
            font-size: .92rem;
            font-weight: 800;
            letter-spacing: .2px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .linea-eleccion { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .insignia {
            flex: 0 0 auto;
            padding: 5px 9px;
            border-radius: 999px;
            background: #2563eb;
            font-size: .69rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        header p { margin: 0; overflow: hidden; color: #cbd5e1; font-size: .82rem; text-overflow: ellipsis; white-space: nowrap; }
        main {
            width: 100%;
            max-width: 1100px;
            height: calc(100vh - 60px);
            margin: auto;
            padding: 12px 16px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            align-content: start;
        }
        .tarjeta {
            margin: 0;
            padding: 14px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .07);
        }
        .tarjeta h2 { margin: 0 0 9px; font-size: 1.05rem; }
        .tarjeta > p { margin: 5px 0; font-size: .9rem; }
        .resultado { grid-column: 1 / -1; }
        .campo-busqueda { display: flex; gap: 8px; }
        input[type="text"] {
            flex: 1;
            min-width: 0;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
        }
        button {
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            color: white;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
        }
        .buscar { background: #2563eb; }
        .autorizar { background: #16a34a; }
        .cancelar { background: #dc2626; }
        .mensaje { grid-column: 1 / -1; margin: 0; padding: 10px 14px; border-radius: 9px; font-size: .9rem; font-weight: 700; }
        .mensaje.exito { background: #dcfce7; color: #166534; }
        .mensaje.error { background: #fee2e2; color: #991b1b; }
        .ficha {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 13px;
            border: 1px solid #dbe3ee;
            border-radius: 9px;
            background: #f8fafc;
        }
        .ficha h3 { margin: 0 0 4px; font-size: 1.02rem; }
        .ficha p { margin: 2px 0; color: #475569; font-size: .88rem; }
        .estado {
            display: inline-block;
            margin-top: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 800;
        }
        .estado.pendiente { background: #dbeafe; color: #1d4ed8; }
        .estado.votado { background: #e2e8f0; color: #475569; }
        .estado.activo { background: #dcfce7; color: #166534; }
        .actual { border-left: 6px solid #16a34a; }
        .sin-activo { border-left: 6px solid #94a3b8; color: #64748b; }
        .nota { grid-column: 1 / -1; margin: 0; color: #64748b; font-size: .8rem; line-height: 1.3; }
        @media (max-width: 620px) {
            body { height: auto; min-height: 100vh; overflow: auto; }
            main { height: auto; display: block; }
            .tarjeta, .mensaje { margin-bottom: 10px; }
            .campo-busqueda, .ficha { align-items: stretch; flex-direction: column; }
            button { width: 100%; }
        }
        @media (max-height: 560px) {
            body { height: auto; min-height: 100vh; overflow: auto; }
            main { height: auto; }
        }
    </style>
</head>
<body>
<header>
    <div class="encabezado">
        <?php if ($logoEscuela): ?>
            <img class="logo-escuela" src="<?= htmlspecialchars($logoEscuela) ?>" alt="Logo de la escuela">
        <?php else: ?>
            <div class="logo-escuela-vacio">LOGO<br>ESCUELA</div>
        <?php endif; ?>

        <div class="datos-encabezado">
            <div class="nombre-escuela">ESCUELA TÉCNICA “CARMEN MOLINA DE LLANO”</div>
            <div class="linea-eleccion">
                <span class="insignia">Presidente de mesa</span>
                <p>
                    <?php if ($instanciaActiva): ?>
                        <?= htmlspecialchars($instanciaActiva['descripcion']) ?> — <?= (int) $instanciaActiva['anio'] ?>
                    <?php else: ?>
                        Sin elección activa
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</header>

<main>
    <?php if ($mensaje !== ''): ?>
        <div class="mensaje <?= htmlspecialchars($tipoMensaje) ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <section class="tarjeta <?= $autorizadoActual ? 'actual' : 'sin-activo' ?>">
        <h2>Estado del cuarto oscuro</h2>
        <?php if ($autorizadoActual): ?>
            <div class="ficha">
                <div>
                    <h3><?= htmlspecialchars($autorizadoActual['apellido'] . ', ' . $autorizadoActual['nombre']) ?></h3>
                    <p><strong>DNI:</strong> <?= htmlspecialchars($autorizadoActual['dni']) ?></p>
                    <span class="estado activo">Autorizado — esperando voto</span>
                </div>
                <form method="POST" action="presidente_mesa.php">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_presidente']) ?>">
                    <input type="hidden" name="accion" value="cancelar">
                    <input type="hidden" name="id_padron" value="<?= (int) $autorizadoActual['id_padron'] ?>">
                    <button type="submit" class="cancelar">Cancelar autorización</button>
                </form>
            </div>
        <?php else: ?>
            <p>No hay ningún alumno autorizado. El cuarto oscuro muestra “Esperando votante”.</p>
        <?php endif; ?>
    </section>

    <section class="tarjeta">
        <h2>1. Verificar DNI</h2>
        <form method="POST" action="presidente_mesa.php" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_presidente']) ?>">
            <input type="hidden" name="accion" value="buscar">
            <div class="campo-busqueda">
                <input
                    type="text"
                    name="dni"
                    value="<?= htmlspecialchars($dniIngresado) ?>"
                    inputmode="numeric"
                    pattern="[0-9]+"
                    placeholder="Ingrese el DNI sin puntos"
                    required
                    autofocus
                >
                <button type="submit" class="buscar">Buscar alumno</button>
            </div>
        </form>
    </section>

    <?php if ($alumnoBuscado): ?>
        <section class="tarjeta resultado">
            <h2>2. Resultado de la verificación</h2>
            <div class="ficha">
                <div>
                    <h3><?= htmlspecialchars($alumnoBuscado['apellido'] . ', ' . $alumnoBuscado['nombre']) ?></h3>
                    <p><strong>DNI:</strong> <?= htmlspecialchars($alumnoBuscado['dni']) ?></p>
                    <p><strong>Curso:</strong> <?= (int) $alumnoBuscado['curso'] ?>° <?= (int) $alumnoBuscado['division'] ?>ª</p>

                    <?php if ((int) $alumnoBuscado['estado_voto'] === 1): ?>
                        <span class="estado votado">Ya votó</span>
                    <?php elseif ((int) $alumnoBuscado['autorizado'] === 1): ?>
                        <span class="estado activo">Ya está autorizado</span>
                    <?php else: ?>
                        <span class="estado pendiente">Habilitado para votar</span>
                    <?php endif; ?>
                </div>

                <?php if ((int) $alumnoBuscado['estado_voto'] === 0 && (int) $alumnoBuscado['autorizado'] === 0): ?>
                    <form method="POST" action="presidente_mesa.php">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_presidente']) ?>">
                        <input type="hidden" name="accion" value="autorizar">
                        <input type="hidden" name="id_padron" value="<?= (int) $alumnoBuscado['id_padron'] ?>">
                        <button type="submit" class="autorizar">Autorizar votación</button>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <p class="nota">
        Se permite una sola autorización activa. Cuando el alumno confirma su voto,
        el permiso se consume automáticamente y esta pantalla vuelve a quedar libre.
    </p>
</main>

<script>
    const claveInicial = <?= json_encode($claveInicial, JSON_UNESCAPED_UNICODE) ?>;

    window.setInterval(async () => {
        try {
            const respuesta = await fetch('presidente_mesa.php?accion=estado&t=' + Date.now(), {
                cache: 'no-store'
            });
            const estado = await respuesta.json();

            if (estado.clave !== claveInicial) {
                window.location.reload();
            }
        } catch (error) {
            // Se vuelve a intentar automaticamente.
        }
    }, 2000);
</script>
</body>
</html>
