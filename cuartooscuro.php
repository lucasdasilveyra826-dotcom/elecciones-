<?php
// ============================================================
// CUARTO OSCURO DIGITAL
// Muestra solamente al ultimo alumno autorizado y aun no votado.
// ============================================================

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
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    
    foreach ($extensionesPermitidas as $ext) {
        $rutaRelativa = 'imagen/logollano.' . $ext;
        if (file_exists(__DIR__ . '/' . $rutaRelativa)) {
            return $rutaRelativa;
        }
    }

    return null;
}

function resolverLogoLista($nombreLista, $logoUrlBd)
{
    // Si viene la ruta o nombre en la BD, la procesamos
    if (!empty($logoUrlBd)) {
        $logoUrl = trim($logoUrlBd);
        if (!preg_match('~^(https?://|imagen/)~i', $logoUrl)) {
            $logoUrl = 'imagen/' . $logoUrl;
        }
        if (file_exists(__DIR__ . '/' . $logoUrl)) {
            return $logoUrl;
        }
    }

    // Mapeo automático de seguridad basado en el nombre de la lista
    $nombreNormalizado = mb_strtoupper(trim($nombreLista), 'UTF-8');
    
    if (strpos($nombreNormalizado, 'AURORA') !== false) {
        $archivos = ['imagen/logoaurora.jpeg', 'imagen/logoaurora.jpg', 'imagen/logoaurora.png'];
        foreach ($archivos as $arc) {
            if (file_exists(__DIR__ . '/' . $arc)) return $arc;
        }
    }
    
    if (strpos($nombreNormalizado, 'UNIÓN LLANO') !== false || strpos($nombreNormalizado, 'UNION LLANO') !== false) {
        $archivos = ['imagen/logounionxllano.jpeg', 'imagen/logounionxllano.jpg', 'imagen/logounionxllano.png'];
        foreach ($archivos as $arc) {
            if (file_exists(__DIR__ . '/' . $arc)) return $arc;
        }
    }

    if (strpos($nombreNormalizado, 'O.C.H.O') !== false || strpos($nombreNormalizado, 'OCHO') !== false) {
        $archivos = ['imagen/logoocho.jpeg', 'imagen/logoocho.jpg', 'imagen/logoocho.png'];
        foreach ($archivos as $arc) {
            if (file_exists(__DIR__ . '/' . $arc)) return $arc;
        }
    }

    return null;
}

function obtenerInstanciaActiva($conexion)
{
    $sql = "SELECT id_instancia, descripcion, anio
            FROM instancia_electoral
            WHERE activa = 1
            ORDER BY id_instancia DESC
            LIMIT 1";
    $resultado = $conexion->query($sql);
    return $resultado->fetch_assoc() ?: null;
}

function obtenerVotanteAutorizado($conexion, $idInstancia)
{
    $sql = "SELECT p.id_padron,
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
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $idInstancia);
    $stmt->execute();
    $votante = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $votante;
}

function obtenerListas($conexion, $idInstancia)
{
    $listas = [];

    $stmtListas = $conexion->prepare(
        "SELECT id_lista, numero_lista, nombre_lista, logo_url
         FROM lista
         WHERE id_instancia = ?
         ORDER BY numero_lista ASC"
    );
    $stmtListas->bind_param('i', $idInstancia);
    $stmtListas->execute();
    $resultadoListas = $stmtListas->get_result();

    $stmtCandidatos = $conexion->prepare(
        "SELECT c.nombre_cargo, a.apellido, a.nombre
         FROM cargos_por_lista cpl
         INNER JOIN cargos c ON c.id_cargo = cpl.id_cargo
         INNER JOIN alumnos a ON a.id_alumno = cpl.id_alumno
         WHERE cpl.id_lista = ?
         ORDER BY c.id_cargo ASC"
    );

    while ($lista = $resultadoListas->fetch_assoc()) {
        $idLista = (int) $lista['id_lista'];
        $stmtCandidatos->bind_param('i', $idLista);
        $stmtCandidatos->execute();
        $resultadoCandidatos = $stmtCandidatos->get_result();
        $lista['candidatos'] = [];

        while ($candidato = $resultadoCandidatos->fetch_assoc()) {
            $lista['candidatos'][] = $candidato;
        }

        // Asignación resuelta de la imagen del logo
        $lista['logo_resuelto'] = resolverLogoLista($lista['nombre_lista'], $lista['logo_url']);

        $listas[] = $lista;
    }

    $stmtCandidatos->close();
    $stmtListas->close();
    return $listas;
}

$logoEscuela = obtenerLogoEscuela();
$instanciaActiva = obtenerInstanciaActiva($conexion);

// Respuesta breve consultada automaticamente por JavaScript.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['accion'] ?? '') === 'estado') {
    header('Content-Type: application/json; charset=utf-8');

    if (!$instanciaActiva) {
        echo json_encode([
            'instancia_activa' => false,
            'autorizado' => false,
            'clave' => null
        ]);
        exit;
    }

    $votanteEstado = obtenerVotanteAutorizado(
        $conexion,
        (int) $instanciaActiva['id_instancia']
    );

    echo json_encode([
        'instancia_activa' => true,
        'autorizado' => $votanteEstado !== null,
        'clave' => $votanteEstado
            ? $votanteEstado['id_padron'] . '|' . $votanteEstado['fecha_hora_autorizacion']
            : null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$votoRegistrado = false;
$mensajeError = '';

// Registra el voto y consume la autorizacion dentro de una transaccion.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'confirmar_voto') {
    $idPadron = filter_input(INPUT_POST, 'id_padron', FILTER_VALIDATE_INT);
    $idLista = filter_input(INPUT_POST, 'id_lista', FILTER_VALIDATE_INT);

    try {
        if (!$instanciaActiva || !$idPadron || $idLista === false || $idLista === null || $idLista < 0) {
            throw new RuntimeException('La solicitud de voto no es valida.');
        }

        $idInstancia = (int) $instanciaActiva['id_instancia'];
        $conexion->begin_transaction();

        // Bloquea el permiso para impedir votos dobles por doble clic o recarga.
        $stmtPermiso = $conexion->prepare(
            "SELECT id_padron
             FROM padron_electoral
             WHERE id_padron = ?
               AND id_instancia = ?
               AND autorizado = 1
               AND estado_voto = 0
             FOR UPDATE"
        );
        $stmtPermiso->bind_param('ii', $idPadron, $idInstancia);
        $stmtPermiso->execute();
        $permiso = $stmtPermiso->get_result()->fetch_assoc();
        $stmtPermiso->close();

        if (!$permiso) {
            throw new RuntimeException('La autorizacion ya no esta disponible.');
        }

        // Si no es voto en blanco, comprueba que la lista pertenezca a esta eleccion.
        if ($idLista > 0) {
            $stmtLista = $conexion->prepare(
                "SELECT id_lista
                 FROM lista
                 WHERE id_lista = ? AND id_instancia = ?
                 LIMIT 1"
            );
            $stmtLista->bind_param('ii', $idLista, $idInstancia);
            $stmtLista->execute();
            $listaValida = $stmtLista->get_result()->fetch_assoc();
            $stmtLista->close();

            if (!$listaValida) {
                throw new RuntimeException('La lista seleccionada no pertenece a la eleccion activa.');
            }

            $stmtVoto = $conexion->prepare(
                "INSERT INTO votos (id_instancia, id_lista, fecha_hora)
                 VALUES (?, ?, NOW())"
            );
            $stmtVoto->bind_param('ii', $idInstancia, $idLista);
        } else {
            $stmtVoto = $conexion->prepare(
                "INSERT INTO votos (id_instancia, id_lista, fecha_hora)
                 VALUES (?, NULL, NOW())"
            );
            $stmtVoto->bind_param('i', $idInstancia);
        }

        $stmtVoto->execute();
        $stmtVoto->close();

        $stmtPadron = $conexion->prepare(
            "UPDATE padron_electoral
             SET estado_voto = 1,
                 autorizado = 0,
                 fecha_hora_voto = NOW()
             WHERE id_padron = ?
               AND id_instancia = ?
               AND autorizado = 1
               AND estado_voto = 0"
        );
        $stmtPadron->bind_param('ii', $idPadron, $idInstancia);
        $stmtPadron->execute();

        if ($stmtPadron->affected_rows !== 1) {
            $stmtPadron->close();
            throw new RuntimeException('No se pudo cerrar la autorizacion del alumno.');
        }

        $stmtPadron->close();
        $conexion->commit();
        $votoRegistrado = true;
    } catch (Throwable $e) {
        try {
            $conexion->rollback();
        } catch (Throwable $ignorado) {
        }
        $mensajeError = $e->getMessage();
    }
}

$votante = null;
$listas = [];

if (!$votoRegistrado && $instanciaActiva) {
    $votante = obtenerVotanteAutorizado(
        $conexion,
        (int) $instanciaActiva['id_instancia']
    );

    if ($votante) {
        $listas = obtenerListas($conexion, (int) $instanciaActiva['id_instancia']);
    }
}

$claveInicial = $votante
    ? $votante['id_padron'] . '|' . $votante['fecha_hora_autorizacion']
    : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuarto oscuro digital</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background: #f1f5f9;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
        }
        header {
            padding: 6px 18px;
            background: #16243d;
            color: white;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .18);
        }
        .encabezado {
            max-width: 1180px;
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
        .linea-eleccion {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .insignia {
            border-radius: 999px;
            background: #2563eb;
            padding: 5px 9px;
            font-size: .69rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        h1 { margin: 0; overflow: hidden; font-size: .82rem; text-overflow: ellipsis; white-space: nowrap; }
        main {
            width: 100%;
            max-width: 1260px;
            height: calc(100vh - 60px);
            margin: auto;
            padding: 9px 14px 8px;
            display: flex;
            flex-direction: column;
        }
        .panel-estado {
            max-width: 680px;
            margin: auto;
            padding: 50px 30px;
            border-radius: 18px;
            background: white;
            text-align: center;
            box-shadow: 0 16px 36px rgba(15, 23, 42, .09);
        }
        .panel-estado h2 { margin: 14px 0 8px; font-size: 1.9rem; }
        .panel-estado p { margin: 0; color: #64748b; font-size: 1.05rem; }
        .espera {
            width: 54px;
            height: 54px;
            margin: auto;
            border: 6px solid #dbeafe;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: girar 1s linear infinite;
        }
        @keyframes girar { to { transform: rotate(360deg); } }
        .tilde {
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            margin: auto;
            border-radius: 50%;
            background: #dcfce7;
            color: #15803d;
            font-size: 2.2rem;
            font-weight: 700;
        }
        .error { border-top: 6px solid #dc2626; }
        .error h2 { color: #b91c1c; }
        .votante {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 7px;
            padding: 8px 14px;
            border-left: 6px solid #2563eb;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(15, 23, 42, .06);
        }
        .votante > div { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .votante h2 { margin: 0; font-size: 1.08rem; }
        .votante p { margin: 0; color: #475569; font-size: .84rem; }
        .habilitado {
            padding: 6px 10px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .instruccion { margin: 0 0 5px; text-align: center; color: #475569; font-size: .83rem; }
        .carrusel {
            position: relative;
            flex: 1;
            min-height: 0;
            display: flex;
            align-items: stretch;
        }
        .grilla {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: stretch;
            gap: 18px;
            padding: 5px max(58px, calc(50% - 175px)) 7px;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
        }
        .grilla::-webkit-scrollbar { display: none; }
        .lista {
            flex: 0 0 min(350px, calc(100vw - 140px));
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 14px;
            border: 3px solid transparent;
            border-radius: 13px;
            background: white;
            text-align: center;
            box-shadow: 0 7px 20px rgba(15, 23, 42, .08);
            opacity: .45;
            transform: scale(.93);
            transition: opacity .18s, transform .18s, border-color .18s, box-shadow .18s;
            scroll-snap-align: center;
            outline: none;
        }
        .lista.activa {
            border-color: #2563eb;
            opacity: 1;
            transform: scale(1);
            box-shadow: 0 12px 30px rgba(37, 99, 235, .2);
        }
        .logo, .logo-vacio {
            width: 56px;
            height: 56px;
            margin-bottom: 6px;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            object-fit: cover;
            background: #f8fafc;
        }
        .logo-vacio { display: grid; place-items: center; color: #94a3b8; font-weight: 700; }
        .numero { color: #2563eb; font-size: .72rem; font-weight: 800; text-transform: uppercase; }
        .lista h3 { margin: 3px 0 7px; font-size: 1.05rem; }
        .candidatos {
            width: 100%;
            flex: 1;
            min-height: 0;
            padding: 7px 10px;
            border-radius: 8px;
            background: #f8fafc;
            text-align: left;
            overflow-y: auto;
        }
        .candidato { margin: 0 0 6px; line-height: 1.15; }
        .candidato:last-child { margin-bottom: 0; }
        .cargo { display: block; color: #64748b; font-size: .62rem; font-weight: 800; text-transform: uppercase; }
        .nombre { color: #1e293b; font-size: .78rem; }
        .sin-candidatos { color: #64748b; text-align: center; font-size: .82rem; }
        .boton-votar {
            width: 100%;
            margin-top: 7px;
            padding: 9px;
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
        }
        .boton-votar:hover { background: #1d4ed8; }
        .lista-blanco { border: 2px dashed #94a3b8; }
        .lista-blanco.activa { border: 3px solid #2563eb; }
        .lista-blanco .boton-votar { background: #475569; }
        .flecha-carrusel {
            position: absolute;
            top: 50%;
            z-index: 5;
            width: 42px;
            height: 58px;
            border: 0;
            border-radius: 11px;
            background: #16243d;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            transform: translateY(-50%);
            box-shadow: 0 6px 18px rgba(15, 23, 42, .22);
        }
        .flecha-carrusel:hover { background: #2563eb; }
        .flecha-izquierda { left: 5px; }
        .flecha-derecha { right: 5px; }
        .indicador {
            height: 20px;
            margin-top: 2px;
            color: #475569;
            text-align: center;
            font-size: .78rem;
            font-weight: 700;
        }
        .modal-fondo {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: none;
            place-items: center;
            padding: 20px;
            background: rgba(15, 23, 42, .72);
        }
        .modal-fondo.visible { display: grid; }
        .modal {
            width: min(470px, 100%);
            padding: 30px;
            border-radius: 16px;
            background: white;
            text-align: center;
            box-shadow: 0 24px 70px rgba(0, 0, 0, .25);
        }
        .modal h3 { margin: 0 0 10px; font-size: 1.45rem; }
        .modal p { color: #475569; line-height: 1.45; }
        #opcion-elegida { color: #1d4ed8; font-size: 1.08rem; }
        .modal .ayuda-modal { margin-bottom: 0; color: #64748b; font-size: .82rem; font-weight: 700; }
        .acciones { display: flex; gap: 12px; margin-top: 24px; }
        .acciones button { flex: 1; padding: 12px; border: 0; border-radius: 9px; color: white; font-weight: 700; cursor: pointer; }
        .acciones button.seleccionado,
        .acciones button:focus {
            outline: 4px solid #0f172a;
            outline-offset: 3px;
            transform: scale(1.03);
        }
        .confirmar { background: #16a34a; }
        .cancelar { background: #dc2626; }
        @media (max-width: 620px) {
            .votante { align-items: flex-start; }
            .votante > div { gap: 6px 12px; }
            .habilitado { display: none; }
            .acciones { flex-direction: column; }
            .flecha-carrusel { width: 36px; }
        }
        @media (max-height: 590px) {
            .logo, .logo-vacio { width: 42px; height: 42px; }
            .lista { padding: 8px 12px; }
            .candidato { margin-bottom: 3px; }
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
                <span class="insignia">Cuarto oscuro</span>
                <h1>
                    <?php if ($instanciaActiva): ?>
                        <?= htmlspecialchars($instanciaActiva['descripcion']) ?> — <?= (int) $instanciaActiva['anio'] ?>
                    <?php else: ?>
                        Sin elección activa
                    <?php endif; ?>
                </h1>
            </div>
        </div>
    </div>
</header>

<main>
    <?php if ($votoRegistrado): ?>
        <section class="panel-estado">
            <div class="tilde">✓</div>
            <h2>¡Gracias por votar!</h2>
            <p>El voto fue registrado correctamente.</p>
            <p style="margin-top:10px">La pantalla quedará lista para el próximo votante.</p>
        </section>

    <?php elseif ($mensajeError !== ''): ?>
        <section class="panel-estado error">
            <h2>No se pudo registrar el voto</h2>
            <p><?= htmlspecialchars($mensajeError) ?></p>
            <p style="margin-top:10px">La pantalla se actualizará automáticamente.</p>
        </section>

    <?php elseif (!$instanciaActiva): ?>
        <section class="panel-estado">
            <h2>No hay una elección activa</h2>
            <p>Active una instancia electoral para comenzar.</p>
        </section>

    <?php elseif (!$votante): ?>
        <section class="panel-estado">
            <div class="espera" aria-hidden="true"></div>
            <h2>Esperando votante</h2>
            <p>El presidente de mesa debe verificar el DNI y autorizar la votación.</p>
        </section>

    <?php else: ?>
        <section class="votante">
            <div>
                <h2><?= htmlspecialchars($votante['apellido'] . ', ' . $votante['nombre']) ?></h2>
                <p><strong>DNI:</strong> <?= htmlspecialchars($votante['dni']) ?></p>
                <p><strong>Curso:</strong> <?= (int) $votante['curso'] ?>° <?= (int) $votante['division'] ?>ª</p>
            </div>
            <span class="habilitado">Votación autorizada</span>
        </section>

        <p class="instruccion">Use ← y → para cambiar de lista. Presione Enter para elegir y Enter nuevamente para confirmar.</p>

        <div class="carrusel">
            <button type="button" class="flecha-carrusel flecha-izquierda" id="anterior" aria-label="Lista anterior">‹</button>
            <section class="grilla" id="carrusel-listas">
            <?php foreach ($listas as $lista): ?>
                <?php $textoLista = 'Lista N° ' . $lista['numero_lista'] . ' — ' . $lista['nombre_lista']; ?>
                <article
                    class="lista opcion-voto"
                    tabindex="-1"
                    data-id-lista="<?= (int) $lista['id_lista'] ?>"
                    data-lista="<?= htmlspecialchars($textoLista, ENT_QUOTES) ?>"
                >
                    <?php if (!empty($lista['logo_resuelto'])): ?>
                        <img class="logo" src="<?= htmlspecialchars($lista['logo_resuelto']) ?>" alt="Logo de <?= htmlspecialchars($lista['nombre_lista']) ?>">
                    <?php else: ?>
                        <div class="logo-vacio">LOGO</div>
                    <?php endif; ?>

                    <span class="numero">Lista N° <?= htmlspecialchars($lista['numero_lista']) ?></span>
                    <h3><?= htmlspecialchars($lista['nombre_lista']) ?></h3>

                    <div class="candidatos">
                        <?php if (!empty($lista['candidatos'])): ?>
                            <?php foreach ($lista['candidatos'] as $candidato): ?>
                                <p class="candidato">
                                    <span class="cargo"><?= htmlspecialchars($candidato['nombre_cargo']) ?></span>
                                    <span class="nombre"><?= htmlspecialchars($candidato['apellido'] . ', ' . $candidato['nombre']) ?></span>
                                </p>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="sin-candidatos">No hay candidatos cargados.</p>
                        <?php endif; ?>
                    </div>

                    <button
                        type="button"
                        class="boton-votar"
                    >Votar esta lista</button>
                </article>
            <?php endforeach; ?>

            <article class="lista lista-blanco opcion-voto" tabindex="-1" data-id-lista="0" data-lista="Voto en blanco">
                <div class="logo-vacio">—</div>
                <span class="numero">Opción electoral</span>
                <h3>Voto en blanco</h3>
                <div class="candidatos">
                    <p class="sin-candidatos">No selecciona ninguna de las listas presentadas.</p>
                </div>
                <button type="button" class="boton-votar">Votar en blanco</button>
            </article>
            </section>
            <button type="button" class="flecha-carrusel flecha-derecha" id="siguiente" aria-label="Lista siguiente">›</button>
        </div>
        <div class="indicador" id="indicador-carrusel" aria-live="polite"></div>
    <?php endif; ?>
</main>

<?php if ($votante && !$votoRegistrado): ?>
    <div class="modal-fondo" id="modal-confirmacion" role="dialog" aria-modal="true" aria-labelledby="titulo-modal">
        <div class="modal">
            <h3 id="titulo-modal">Confirmar voto</h3>
            <p>Está por votar a:</p>
            <p><strong id="opcion-elegida"></strong></p>
            <p>Una vez confirmado, el voto no podrá modificarse. ¿Está seguro/a?</p>
            <p class="ayuda-modal">Use ← y → para elegir. Presione Enter para aceptar.</p>

            <form id="form-voto" action="cuartooscuro.php" method="POST">
                <input type="hidden" name="accion" value="confirmar_voto">
                <input type="hidden" name="id_padron" value="<?= (int) $votante['id_padron'] ?>">
                <input type="hidden" name="id_lista" id="id-lista" value="">
                <div class="acciones">
                    <button type="submit" class="confirmar" id="confirmar-voto">Sí, confirmar voto</button>
                    <button type="button" class="cancelar" id="cancelar-voto">No, volver</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
    const claveInicial = <?= json_encode($claveInicial, JSON_UNESCAPED_UNICODE) ?>;
    const votoRegistrado = <?= $votoRegistrado ? 'true' : 'false' ?>;
    const huboError = <?= $mensajeError !== '' ? 'true' : 'false' ?>;

    if (votoRegistrado || huboError) {
        window.setTimeout(() => window.location.replace('cuartooscuro.php'), 4000);
    } else {
        window.setInterval(async () => {
            try {
                const respuesta = await fetch('cuartooscuro.php?accion=estado&t=' + Date.now(), {
                    cache: 'no-store'
                });
                const estado = await respuesta.json();

                if (estado.clave !== claveInicial) {
                    window.location.reload();
                }
            } catch (error) {
                // Si la red se corta, vuelve a intentar en el siguiente intervalo.
            }
        }, 1500);
    }

    const modal = document.getElementById('modal-confirmacion');
    const campoLista = document.getElementById('id-lista');
    const opcionElegida = document.getElementById('opcion-elegida');
    const formularioVoto = document.getElementById('form-voto');
    const botonConfirmar = document.getElementById('confirmar-voto');
    const botonCancelar = document.getElementById('cancelar-voto');
    const botonesConfirmacion = [botonConfirmar, botonCancelar].filter(Boolean);
    const indicador = document.getElementById('indicador-carrusel');
    const opciones = Array.from(document.querySelectorAll('.opcion-voto'));
    let indiceActivo = 0;
    let indiceConfirmacion = 0;
    let votoEnviandose = false;

    function actualizarCarrusel(nuevoIndice, animar = true) {
        if (opciones.length === 0) return;

        indiceActivo = (nuevoIndice + opciones.length) % opciones.length;

        opciones.forEach((opcion, indice) => {
            const activa = indice === indiceActivo;
            opcion.classList.toggle('activa', activa);
            opcion.setAttribute('aria-selected', activa ? 'true' : 'false');
        });

        const opcionActiva = opciones[indiceActivo];
        opcionActiva.focus({ preventScroll: true });
        opcionActiva.scrollIntoView({
            behavior: animar ? 'smooth' : 'auto',
            block: 'nearest',
            inline: 'center'
        });

        if (indicador) {
            indicador.textContent = (indiceActivo + 1) + ' de ' + opciones.length + ' — ' + opcionActiva.dataset.lista;
        }
    }

    function abrirConfirmacion(opcion) {
        if (!modal || !campoLista || !opcionElegida) return;

        campoLista.value = opcion.dataset.idLista;
        opcionElegida.textContent = opcion.dataset.lista;
        modal.classList.add('visible');
        indiceConfirmacion = 0;
        window.setTimeout(() => actualizarConfirmacion(0), 30);
    }

    function actualizarConfirmacion(nuevoIndice) {
        if (botonesConfirmacion.length === 0) return;

        indiceConfirmacion = (nuevoIndice + botonesConfirmacion.length) % botonesConfirmacion.length;

        botonesConfirmacion.forEach((boton, indice) => {
            boton.classList.toggle('seleccionado', indice === indiceConfirmacion);
        });

        botonesConfirmacion[indiceConfirmacion].focus();
    }

    function cerrarConfirmacion() {
        if (!modal) return;
        modal.classList.remove('visible');
        botonesConfirmacion.forEach((boton) => boton.classList.remove('seleccionado'));
        opciones[indiceActivo]?.focus({ preventScroll: true });
    }

    opciones.forEach((opcion, indice) => {
        opcion.addEventListener('click', (evento) => {
            actualizarCarrusel(indice);

            if (evento.target.closest('.boton-votar')) {
                abrirConfirmacion(opcion);
            }
        });
    });

    document.getElementById('anterior')?.addEventListener('click', () => {
        actualizarCarrusel(indiceActivo - 1);
    });

    document.getElementById('siguiente')?.addEventListener('click', () => {
        actualizarCarrusel(indiceActivo + 1);
    });

    document.addEventListener('keydown', (evento) => {
        if (opciones.length === 0) return;

        const modalAbierto = modal?.classList.contains('visible');

        if (modalAbierto) {
            if (evento.key === 'Escape') {
                evento.preventDefault();
                cerrarConfirmacion();
            } else if (evento.key === 'ArrowRight') {
                evento.preventDefault();
                actualizarConfirmacion(indiceConfirmacion + 1);
            } else if (evento.key === 'ArrowLeft') {
                evento.preventDefault();
                actualizarConfirmacion(indiceConfirmacion - 1);
            } else if (evento.key === 'Enter' && !evento.repeat && !votoEnviandose) {
                evento.preventDefault();

                if (indiceConfirmacion === 0) {
                    formularioVoto?.requestSubmit();
                } else {
                    cerrarConfirmacion();
                }
            }
            return;
        }

        if (evento.key === 'ArrowRight') {
            evento.preventDefault();
            actualizarCarrusel(indiceActivo + 1);
        } else if (evento.key === 'ArrowLeft') {
            evento.preventDefault();
            actualizarCarrusel(indiceActivo - 1);
        } else if (evento.key === 'Enter' && !evento.repeat) {
            evento.preventDefault();
            abrirConfirmacion(opciones[indiceActivo]);
        }
    });

    botonConfirmar?.addEventListener('focus', () => {
        indiceConfirmacion = 0;
        actualizarConfirmacion(0);
    });

    botonCancelar?.addEventListener('focus', () => {
        indiceConfirmacion = 1;
        actualizarConfirmacion(1);
    });

    botonCancelar?.addEventListener('click', cerrarConfirmacion);

    formularioVoto?.addEventListener('submit', (evento) => {
        if (votoEnviandose) {
            evento.preventDefault();
            return;
        }

        votoEnviandose = true;
        botonConfirmar.disabled = true;
        botonConfirmar.textContent = 'Registrando...';
    });

    if (opciones.length > 0) {
        window.requestAnimationFrame(() => actualizarCarrusel(0, false));
    }
</script>
</body>
</html>