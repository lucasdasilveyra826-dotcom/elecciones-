<?php
// ============================================================
// DASHBOARD ESTADISTICO DEL SISTEMA DE ELECCIONES
// Resultados por lista, sistema D'Hondt y participacion escolar.
// ============================================================

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema_voto';

try {
    $conexion = new mysqli($host, $user, $pass, $db);
    $conexion->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    exit('No se pudo conectar con la base de datos.');
}

function escapar($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
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

function porcentaje($cantidad, $total)
{
    return $total > 0 ? round(($cantidad * 100) / $total, 1) : 0;
}

function crearGradienteCircular($elementos, $total)
{
    if ($total <= 0) {
        return 'conic-gradient(#e2e8f0 0% 100%)';
    }

    $segmentos = [];
    $inicio = 0.0;

    foreach ($elementos as $elemento) {
        $cantidad = (int) $elemento['cantidad'];

        if ($cantidad <= 0) {
            continue;
        }

        $fin = $inicio + (($cantidad * 100) / $total);
        $segmentos[] = $elemento['color'] . ' ' .
            number_format($inicio, 4, '.', '') . '% ' .
            number_format($fin, 4, '.', '') . '%';
        $inicio = $fin;
    }

    if (empty($segmentos)) {
        return 'conic-gradient(#e2e8f0 0% 100%)';
    }

    return 'conic-gradient(' . implode(', ', $segmentos) . ')';
}

function calcularDhondt($listas, $cantidadCargos)
{
    $cocientes = [];

    foreach ($listas as $lista) {
        $votos = (int) $lista['votos'];

        if ($votos <= 0) {
            continue;
        }

        for ($divisor = 1; $divisor <= $cantidadCargos; $divisor++) {
            $cocientes[] = [
                'id_lista' => (int) $lista['id_lista'],
                'numero_lista' => (int) $lista['numero_lista'],
                'nombre_lista' => $lista['nombre_lista'],
                'color' => $lista['color'],
                'votos' => $votos,
                'divisor' => $divisor,
                'cociente' => $votos / $divisor
            ];
        }
    }

    usort($cocientes, function ($a, $b) {
        if (abs($a['cociente'] - $b['cociente']) > 0.0000001) {
            return $b['cociente'] <=> $a['cociente'];
        }

        if ($a['votos'] !== $b['votos']) {
            return $b['votos'] <=> $a['votos'];
        }

        if ($a['numero_lista'] !== $b['numero_lista']) {
            return $a['numero_lista'] <=> $b['numero_lista'];
        }

        return $a['divisor'] <=> $b['divisor'];
    });

    $asignaciones = array_slice($cocientes, 0, $cantidadCargos);

    foreach ($asignaciones as $indice => &$asignacion) {
        $asignacion['orden'] = $indice + 1;
    }
    unset($asignacion);

    return $asignaciones;
}

$colores = ['#2563eb', '#e11d48', '#16a34a', '#f59e0b', '#7c3aed', '#0891b2'];
$logoEscuela = obtenerLogoEscuela();
$instanciaActiva = obtenerInstanciaActiva($conexion);

$listas = [];
$votosBlanco = 0;
$totalVotos = 0;
$totalPadron = 0;
$totalVotaron = 0;
$totalNoVotaron = 0;
$porcentajeGeneral = 0;
$grupos = [];
$grupoSeleccionado = '';
$estadisticaGrupo = null;
$asignacionesDhondt = [];
$resumenDhondt = [];
$cargos = [];
$candidatosPorListaCargo = [];
$elementosTortaListas = [];
$gradienteListas = 'conic-gradient(#e2e8f0 0% 100%)';

if ($instanciaActiva) {
    $idInstancia = (int) $instanciaActiva['id_instancia'];

    // Cantidad de votos obtenidos por cada lista.
    $stmtListas = $conexion->prepare(
        "SELECT l.id_lista,
                l.numero_lista,
                l.nombre_lista,
                l.logo_url,
                COUNT(v.id_voto) AS votos
         FROM lista l
         LEFT JOIN votos v
           ON v.id_lista = l.id_lista
          AND v.id_instancia = l.id_instancia
         WHERE l.id_instancia = ?
         GROUP BY l.id_lista, l.numero_lista, l.nombre_lista, l.logo_url
         ORDER BY l.numero_lista ASC"
    );
    $stmtListas->bind_param('i', $idInstancia);
    $stmtListas->execute();
    $resultadoListas = $stmtListas->get_result();

    $indiceColor = 0;
    while ($lista = $resultadoListas->fetch_assoc()) {
        $lista['votos'] = (int) $lista['votos'];
        $lista['color'] = $colores[$indiceColor % count($colores)];
        $listas[] = $lista;
        $indiceColor++;
    }
    $stmtListas->close();

    // Voto en blanco: id_lista permanece en NULL.
    $stmtBlanco = $conexion->prepare(
        "SELECT COUNT(*) AS cantidad
         FROM votos
         WHERE id_instancia = ? AND id_lista IS NULL"
    );
    $stmtBlanco->bind_param('i', $idInstancia);
    $stmtBlanco->execute();
    $votosBlanco = (int) $stmtBlanco->get_result()->fetch_assoc()['cantidad'];
    $stmtBlanco->close();

    foreach ($listas as $lista) {
        $totalVotos += (int) $lista['votos'];
        $elementosTortaListas[] = [
            'cantidad' => (int) $lista['votos'],
            'color' => $lista['color']
        ];
    }
    $totalVotos += $votosBlanco;
    $elementosTortaListas[] = ['cantidad' => $votosBlanco, 'color' => '#94a3b8'];
    $gradienteListas = crearGradienteCircular($elementosTortaListas, $totalVotos);

    // Participacion general segun el padron electoral.
    $stmtParticipacion = $conexion->prepare(
        "SELECT COUNT(*) AS total,
                COALESCE(SUM(estado_voto = 1), 0) AS votaron
         FROM padron_electoral
         WHERE id_instancia = ?"
    );
    $stmtParticipacion->bind_param('i', $idInstancia);
    $stmtParticipacion->execute();
    $participacion = $stmtParticipacion->get_result()->fetch_assoc();
    $stmtParticipacion->close();

    $totalPadron = (int) $participacion['total'];
    $totalVotaron = (int) $participacion['votaron'];
    $totalNoVotaron = max(0, $totalPadron - $totalVotaron);
    $porcentajeGeneral = porcentaje($totalVotaron, $totalPadron);

    // Participacion agrupada por curso y division.
    $stmtGrupos = $conexion->prepare(
        "SELECT a.curso,
                a.division,
                COUNT(*) AS total,
                COALESCE(SUM(p.estado_voto = 1), 0) AS votaron
         FROM padron_electoral p
         INNER JOIN alumnos a ON a.id_alumno = p.id_alumno
         WHERE p.id_instancia = ?
         GROUP BY a.curso, a.division
         ORDER BY a.curso ASC, a.division ASC"
    );
    $stmtGrupos->bind_param('i', $idInstancia);
    $stmtGrupos->execute();
    $resultadoGrupos = $stmtGrupos->get_result();

    while ($grupo = $resultadoGrupos->fetch_assoc()) {
        $grupo['total'] = (int) $grupo['total'];
        $grupo['votaron'] = (int) $grupo['votaron'];
        $grupo['no_votaron'] = max(0, $grupo['total'] - $grupo['votaron']);
        $grupo['porcentaje'] = porcentaje($grupo['votaron'], $grupo['total']);
        $grupo['clave'] = $grupo['curso'] . '|' . $grupo['division'];
        $grupos[] = $grupo;
    }
    $stmtGrupos->close();

    $grupoSolicitado = isset($_GET['grupo']) && is_string($_GET['grupo'])
        ? $_GET['grupo']
        : '';
    $grupoSeleccionado = !empty($grupos) ? $grupos[0]['clave'] : '';

    foreach ($grupos as $grupo) {
        if (hash_equals($grupo['clave'], $grupoSolicitado)) {
            $grupoSeleccionado = $grupoSolicitado;
            break;
        }
    }

    foreach ($grupos as $grupo) {
        if ($grupo['clave'] === $grupoSeleccionado) {
            $estadisticaGrupo = $grupo;
            break;
        }
    }

    // D'Hondt reparte 7 lugares usando solamente votos validos de listas.
    $asignacionesDhondt = calcularDhondt($listas, 7);

    foreach ($listas as $lista) {
        $resumenDhondt[$lista['id_lista']] = [
            'numero_lista' => $lista['numero_lista'],
            'nombre_lista' => $lista['nombre_lista'],
            'color' => $lista['color'],
            'votos' => $lista['votos'],
            'cargos' => 0
        ];
    }

    foreach ($asignacionesDhondt as $asignacion) {
        $resumenDhondt[$asignacion['id_lista']]['cargos']++;
    }

    // Nombres de los siete cargos y candidatos de cada lista.
    $resultadoCargos = $conexion->query(
        "SELECT id_cargo, nombre_cargo
         FROM cargos
         ORDER BY id_cargo ASC
         LIMIT 7"
    );
    while ($cargo = $resultadoCargos->fetch_assoc()) {
        $cargos[] = $cargo;
    }

    $stmtCandidatos = $conexion->prepare(
        "SELECT cpl.id_lista,
                c.id_cargo,
                a.apellido,
                a.nombre
         FROM cargos_por_lista cpl
         INNER JOIN lista l ON l.id_lista = cpl.id_lista
         INNER JOIN cargos c ON c.id_cargo = cpl.id_cargo
         INNER JOIN alumnos a ON a.id_alumno = cpl.id_alumno
         WHERE l.id_instancia = ?"
    );
    $stmtCandidatos->bind_param('i', $idInstancia);
    $stmtCandidatos->execute();
    $resultadoCandidatos = $stmtCandidatos->get_result();

    while ($candidato = $resultadoCandidatos->fetch_assoc()) {
        $candidatosPorListaCargo[$candidato['id_lista']][$candidato['id_cargo']] =
            $candidato['apellido'] . ', ' . $candidato['nombre'];
    }
    $stmtCandidatos->close();
}

$panelSolicitado = isset($_GET['panel']) && is_string($_GET['panel'])
    ? $_GET['panel']
    : 'listas';
$panelesPermitidos = ['listas', 'dhondt', 'general', 'cursos'];
$panelInicial = in_array($panelSolicitado, $panelesPermitidos, true)
    ? $panelSolicitado
    : 'listas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas electorales</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: #eef2f7;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
        }
        header {
            padding: 8px 18px;
            background: #16243d;
            color: white;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .2);
        }
        .encabezado {
            max-width: 1180px;
            margin: auto;
            display: flex;
            align-items: center;
            gap: 11px;
        }
        .logo-escuela, .logo-escuela-vacio {
            width: 50px;
            height: 50px;
            flex: 0 0 50px;
            border-radius: 8px;
            object-fit: contain;
            background: white;
        }
        .logo-escuela-vacio {
            display: grid;
            place-items: center;
            padding: 4px;
            color: #475569;
            font-size: .5rem;
            font-weight: 800;
            line-height: 1.05;
            text-align: center;
        }
        .datos-encabezado { min-width: 0; flex: 1; }
        .nombre-escuela {
            margin-bottom: 4px;
            overflow: hidden;
            font-size: 1rem;
            font-weight: 800;
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
        header p { margin: 0; overflow: hidden; color: #cbd5e1; font-size: .84rem; text-overflow: ellipsis; white-space: nowrap; }
        main { width: 100%; max-width: 1180px; margin: auto; padding: 18px 16px 45px; }
        .barra-superior {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 13px;
        }
        .barra-superior h1 { margin: 0; font-size: 1.35rem; }
        .actualizacion { color: #64748b; font-size: .78rem; }
        .btn-actualizar {
            padding: 8px 12px;
            border: 0;
            border-radius: 8px;
            background: #475569;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }
        .resumen {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 11px;
            margin-bottom: 14px;
        }
        .dato {
            padding: 13px 15px;
            border-radius: 11px;
            background: white;
            box-shadow: 0 5px 15px rgba(15, 23, 42, .06);
        }
        .dato span { display: block; color: #64748b; font-size: .72rem; font-weight: 800; text-transform: uppercase; }
        .dato strong { display: block; margin-top: 4px; color: #0f172a; font-size: 1.6rem; }
        .pestanas {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 14px;
        }
        .pestana {
            padding: 11px 9px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
            color: #334155;
            font-weight: 800;
            cursor: pointer;
        }
        .pestana.activa { border-color: #2563eb; background: #2563eb; color: white; }
        .panel { display: none; }
        .panel.activo { display: block; }
        .tarjeta {
            padding: 20px;
            border-radius: 14px;
            background: white;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
        }
        .tarjeta h2 { margin: 0 0 5px; font-size: 1.25rem; }
        .subtitulo { margin: 0 0 18px; color: #64748b; font-size: .88rem; }
        .disposicion-grafico {
            display: grid;
            grid-template-columns: minmax(260px, .8fr) minmax(300px, 1.2fr);
            gap: 28px;
            align-items: center;
        }
        .torta {
            position: relative;
            width: min(280px, 72vw);
            aspect-ratio: 1;
            margin: auto;
            border-radius: 50%;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .08), 0 10px 25px rgba(15, 23, 42, .12);
        }
        .torta::after {
            content: '';
            position: absolute;
            inset: 29%;
            border-radius: 50%;
            background: white;
        }
        .centro-torta {
            position: absolute;
            inset: 31%;
            z-index: 2;
            display: grid;
            place-items: center;
            align-content: center;
            border-radius: 50%;
            text-align: center;
        }
        .centro-torta strong { font-size: 1.8rem; }
        .centro-torta span { color: #64748b; font-size: .72rem; font-weight: 700; }
        .leyenda { display: grid; gap: 9px; }
        .fila-leyenda {
            display: grid;
            grid-template-columns: 13px 1fr auto auto;
            gap: 9px;
            align-items: center;
            padding: 10px 12px;
            border-radius: 9px;
            background: #f8fafc;
        }
        .color { width: 13px; height: 13px; border-radius: 4px; }
        .fila-leyenda .nombre { font-weight: 700; }
        .fila-leyenda .cantidad { color: #475569; font-size: .85rem; }
        .fila-leyenda .porcentaje { min-width: 56px; color: #0f172a; font-weight: 800; text-align: right; }
        .sin-datos { padding: 35px 20px; color: #64748b; text-align: center; }
        .bancas {
            display: grid;
            grid-template-columns: repeat(7, minmax(82px, 1fr));
            gap: 8px;
            margin: 20px 0;
        }
        .banca {
            min-height: 88px;
            display: grid;
            place-items: center;
            align-content: center;
            padding: 8px;
            border-radius: 10px;
            color: white;
            text-align: center;
        }
        .banca strong { font-size: 1.2rem; }
        .banca span { margin-top: 4px; font-size: .7rem; font-weight: 800; }
        .resumen-dhondt { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 18px; }
        .lista-dhondt { padding: 13px; border-left: 6px solid; border-radius: 9px; background: #f8fafc; }
        .lista-dhondt strong { display: block; }
        .lista-dhondt span { color: #64748b; font-size: .82rem; }
        .lista-dhondt b { float: right; font-size: 1.35rem; }
        .tabla-contenedor { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .86rem; }
        th, td { padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; color: #475569; font-size: .72rem; text-transform: uppercase; }
        .etiqueta-lista { display: inline-block; padding: 5px 8px; border-radius: 6px; color: white; font-size: .75rem; font-weight: 800; }
        .aviso { margin-top: 13px; padding: 10px 12px; border-radius: 8px; background: #fff7ed; color: #9a3412; font-size: .8rem; }
        .grafico-general { display: grid; grid-template-columns: minmax(260px, .8fr) minmax(300px, 1.2fr); gap: 28px; align-items: center; }
        .comparacion { display: grid; gap: 14px; }
        .comparacion-item { padding: 16px; border-radius: 11px; background: #f8fafc; }
        .comparacion-item .linea { display: flex; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
        .comparacion-item strong { font-size: 1.35rem; }
        .barra { height: 13px; overflow: hidden; border-radius: 999px; background: #e2e8f0; }
        .barra > div { height: 100%; border-radius: inherit; }
        .barra.segmentada { display: flex; }
        .barra.segmentada > div { border-radius: 0; }
        .selector {
            display: flex;
            align-items: end;
            gap: 10px;
            margin-bottom: 18px;
        }
        .selector label { display: block; margin-bottom: 5px; color: #475569; font-size: .77rem; font-weight: 800; text-transform: uppercase; }
        select {
            min-width: 230px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
            font-size: .95rem;
        }
        .grupo-destacado { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 18px; }
        .grupo-dato { padding: 14px; border-radius: 10px; background: #f8fafc; text-align: center; }
        .grupo-dato strong { display: block; font-size: 1.45rem; }
        .grupo-dato span { color: #64748b; font-size: .75rem; font-weight: 700; }
        .barra-curso { min-width: 160px; }
        .barra-curso small { display: block; margin-top: 4px; color: #64748b; }
        .estado-alto { color: #166534; font-weight: 800; }
        .estado-medio { color: #a16207; font-weight: 800; }
        .estado-bajo { color: #b91c1c; font-weight: 800; }
        .panel-vacio { padding: 55px 25px; border-radius: 14px; background: white; text-align: center; }
        .privado { margin-top: 14px; color: #64748b; font-size: .76rem; text-align: center; }
        @media (max-width: 850px) {
            .resumen { grid-template-columns: repeat(2, 1fr); }
            .pestanas { grid-template-columns: repeat(2, 1fr); }
            .disposicion-grafico, .grafico-general { grid-template-columns: 1fr; }
            .bancas { grid-template-columns: repeat(4, 1fr); }
            .resumen-dhondt { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) {
            .barra-superior { align-items: flex-start; flex-direction: column; }
            .resumen { grid-template-columns: 1fr 1fr; }
            .pestanas { grid-template-columns: 1fr; }
            .bancas { grid-template-columns: repeat(2, 1fr); }
            .selector { align-items: stretch; flex-direction: column; }
            select { width: 100%; min-width: 0; }
            .grupo-destacado { grid-template-columns: 1fr; }
            .nombre-escuela { font-size: .8rem; }
        }
    </style>
</head>
<body>
<header>
    <div class="encabezado">
        <?php if ($logoEscuela): ?>
            <img class="logo-escuela" src="<?= escapar($logoEscuela) ?>" alt="Logo de la escuela">
        <?php else: ?>
            <div class="logo-escuela-vacio">LOGO<br>ESCUELA</div>
        <?php endif; ?>

        <div class="datos-encabezado">
            <div class="nombre-escuela">ESCUELA TÉCNICA “CARMEN MOLINA DE LLANO”</div>
            <div class="linea-eleccion">
                <span class="insignia">Estadísticas</span>
                <p>
                    <?php if ($instanciaActiva): ?>
                        <?= escapar($instanciaActiva['descripcion']) ?> — <?= (int) $instanciaActiva['anio'] ?>
                    <?php else: ?>
                        Sin elección activa
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="barra-superior">
        <div>
            <h1>Dashboard electoral</h1>
            <span class="actualizacion">Datos consultados a las <?= date('H:i:s') ?></span>
        </div>
        <button type="button" class="btn-actualizar" onclick="window.location.reload()">Actualizar datos</button>
    </div>

    <?php if (!$instanciaActiva): ?>
        <section class="panel-vacio">
            <h2>No hay una elección activa</h2>
            <p>Active una instancia electoral para visualizar las estadísticas.</p>
        </section>
    <?php else: ?>
        <section class="resumen">
            <article class="dato"><span>Alumnos habilitados</span><strong><?= $totalPadron ?></strong></article>
            <article class="dato"><span>Ya votaron</span><strong><?= $totalVotaron ?></strong></article>
            <article class="dato"><span>Faltan votar</span><strong><?= $totalNoVotaron ?></strong></article>
            <article class="dato"><span>Participación</span><strong><?= number_format($porcentajeGeneral, 1, ',', '.') ?>%</strong></article>
        </section>

        <nav class="pestanas" aria-label="Gráficos estadísticos">
            <button type="button" class="pestana" data-panel="listas">Votos por lista</button>
            <button type="button" class="pestana" data-panel="dhondt">Sistema D’Hondt</button>
            <button type="button" class="pestana" data-panel="general">Participación general</button>
            <button type="button" class="pestana" data-panel="cursos">Por curso y división</button>
        </nav>

        <section class="panel" id="panel-listas">
            <article class="tarjeta">
                <h2>Porcentaje de votos por lista</h2>
                <p class="subtitulo">Incluye el voto en blanco dentro del total de votos emitidos.</p>

                <?php if ($totalVotos > 0): ?>
                    <div class="disposicion-grafico">
                        <div class="torta" style="background: <?= escapar($gradienteListas) ?>">
                            <div class="centro-torta"><strong><?= $totalVotos ?></strong><span>votos emitidos</span></div>
                        </div>

                        <div class="leyenda">
                            <?php foreach ($listas as $lista): ?>
                                <div class="fila-leyenda">
                                    <span class="color" style="background: <?= escapar($lista['color']) ?>"></span>
                                    <span class="nombre">Lista N° <?= (int) $lista['numero_lista'] ?> — <?= escapar($lista['nombre_lista']) ?></span>
                                    <span class="cantidad"><?= (int) $lista['votos'] ?> votos</span>
                                    <span class="porcentaje"><?= number_format(porcentaje($lista['votos'], $totalVotos), 1, ',', '.') ?>%</span>
                                </div>
                            <?php endforeach; ?>

                            <div class="fila-leyenda">
                                <span class="color" style="background:#94a3b8"></span>
                                <span class="nombre">Voto en blanco</span>
                                <span class="cantidad"><?= $votosBlanco ?> votos</span>
                                <span class="porcentaje"><?= number_format(porcentaje($votosBlanco, $totalVotos), 1, ',', '.') ?>%</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="sin-datos">Todavía no hay votos registrados.</div>
                <?php endif; ?>
            </article>
        </section>

        <section class="panel" id="panel-dhondt">
            <article class="tarjeta">
                <h2>Distribución de 7 cargos mediante D’Hondt</h2>
                <p class="subtitulo">Los votos en blanco no participan del reparto.</p>

                <?php if (!empty($asignacionesDhondt)): ?>
                    <div class="bancas">
                        <?php foreach ($asignacionesDhondt as $asignacion): ?>
                            <div class="banca" style="background:<?= escapar($asignacion['color']) ?>">
                                <strong><?= (int) $asignacion['orden'] ?></strong>
                                <span>LISTA <?= (int) $asignacion['numero_lista'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="resumen-dhondt">
                        <?php foreach ($resumenDhondt as $resumenLista): ?>
                            <div class="lista-dhondt" style="border-color:<?= escapar($resumenLista['color']) ?>">
                                <b><?= (int) $resumenLista['cargos'] ?></b>
                                <strong>Lista N° <?= (int) $resumenLista['numero_lista'] ?></strong>
                                <span><?= escapar($resumenLista['nombre_lista']) ?> · <?= (int) $resumenLista['votos'] ?> votos</span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="tabla-contenedor">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cargo</th>
                                    <th>Lista adjudicada</th>
                                    <th>Candidato</th>
                                    <th>Cálculo D’Hondt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asignacionesDhondt as $indice => $asignacion): ?>
                                    <?php
                                        $cargo = $cargos[$indice] ?? [
                                            'id_cargo' => $indice + 1,
                                            'nombre_cargo' => 'Cargo ' . ($indice + 1)
                                        ];
                                        $nombreCandidato = $candidatosPorListaCargo[$asignacion['id_lista']][$cargo['id_cargo']]
                                            ?? 'Candidato no cargado';
                                    ?>
                                    <tr>
                                        <td><strong><?= escapar($cargo['nombre_cargo']) ?></strong></td>
                                        <td><span class="etiqueta-lista" style="background:<?= escapar($asignacion['color']) ?>">Lista <?= (int) $asignacion['numero_lista'] ?></span></td>
                                        <td><?= escapar($nombreCandidato) ?></td>
                                        <td><?= (int) $asignacion['votos'] ?> ÷ <?= (int) $asignacion['divisor'] ?> = <?= number_format($asignacion['cociente'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="aviso">La asociación entre el orden de adjudicación y los nombres de los cargos es una proyección. Conviene verificar que coincida con el reglamento electoral de la institución.</div>
                <?php else: ?>
                    <div class="sin-datos">Se necesitan votos válidos para calcular el reparto D’Hondt.</div>
                <?php endif; ?>
            </article>
        </section>

        <section class="panel" id="panel-general">
            <article class="tarjeta">
                <h2>Participación general del padrón</h2>
                <p class="subtitulo">Porcentaje de alumnos que ya emitieron su voto.</p>

                <div class="grafico-general">
                    <div class="torta" style="background:conic-gradient(#16a34a 0% <?= $porcentajeGeneral ?>%, #e2e8f0 <?= $porcentajeGeneral ?>% 100%)">
                        <div class="centro-torta"><strong><?= number_format($porcentajeGeneral, 1, ',', '.') ?>%</strong><span>participación</span></div>
                    </div>

                    <div class="comparacion">
                        <div class="comparacion-item">
                            <div class="linea"><span>Alumnos que votaron</span><strong style="color:#15803d"><?= $totalVotaron ?></strong></div>
                            <div class="barra"><div style="width:<?= $porcentajeGeneral ?>%;background:#16a34a"></div></div>
                        </div>
                        <div class="comparacion-item">
                            <div class="linea"><span>Alumnos que todavía no votaron</span><strong style="color:#b91c1c"><?= $totalNoVotaron ?></strong></div>
                            <div class="barra"><div style="width:<?= 100 - $porcentajeGeneral ?>%;background:#ef4444"></div></div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="panel" id="panel-cursos">
            <article class="tarjeta">
                <h2>Participación por curso y división</h2>
                <p class="subtitulo">Permite identificar rápidamente qué grupos todavía deben acercarse a votar.</p>

                <?php if (!empty($grupos) && $estadisticaGrupo): ?>
                    <form class="selector" method="GET" action="estadisticas.php">
                        <input type="hidden" name="panel" value="cursos">
                        <div>
                            <label for="grupo">Curso y división</label>
                            <select name="grupo" id="grupo" onchange="this.form.submit()">
                                <?php foreach ($grupos as $grupo): ?>
                                    <option value="<?= escapar($grupo['clave']) ?>" <?= $grupo['clave'] === $grupoSeleccionado ? 'selected' : '' ?>>
                                        <?= (int) $grupo['curso'] ?>° <?= (int) $grupo['division'] ?>ª — <?= number_format($grupo['porcentaje'], 1, ',', '.') ?>% votó
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>

                    <div class="grupo-destacado">
                        <div class="grupo-dato"><strong><?= (int) $estadisticaGrupo['total'] ?></strong><span>Total del curso</span></div>
                        <div class="grupo-dato"><strong style="color:#15803d"><?= (int) $estadisticaGrupo['votaron'] ?></strong><span>Ya votaron</span></div>
                        <div class="grupo-dato"><strong style="color:#b91c1c"><?= (int) $estadisticaGrupo['no_votaron'] ?></strong><span>Faltan votar</span></div>
                    </div>

                    <div class="grafico-general" style="margin-bottom:22px">
                        <div class="torta" style="background:conic-gradient(#16a34a 0% <?= $estadisticaGrupo['porcentaje'] ?>%, #ef4444 <?= $estadisticaGrupo['porcentaje'] ?>% 100%)">
                            <div class="centro-torta">
                                <strong><?= number_format($estadisticaGrupo['porcentaje'], 1, ',', '.') ?>%</strong>
                                <span><?= (int) $estadisticaGrupo['curso'] ?>° <?= (int) $estadisticaGrupo['division'] ?>ª</span>
                            </div>
                        </div>

                        <div class="comparacion">
                            <div class="comparacion-item">
                                <div class="linea"><span>Votaron</span><strong style="color:#15803d"><?= (int) $estadisticaGrupo['votaron'] ?></strong></div>
                                <div class="barra"><div style="width:<?= $estadisticaGrupo['porcentaje'] ?>%;background:#16a34a"></div></div>
                            </div>
                            <div class="comparacion-item">
                                <div class="linea"><span>No votaron</span><strong style="color:#b91c1c"><?= (int) $estadisticaGrupo['no_votaron'] ?></strong></div>
                                <div class="barra"><div style="width:<?= 100 - $estadisticaGrupo['porcentaje'] ?>%;background:#ef4444"></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="tabla-contenedor">
                        <table>
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Total</th>
                                    <th>Votaron</th>
                                    <th>No votaron</th>
                                    <th>Participación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grupos as $grupo): ?>
                                    <?php
                                        $claseEstado = $grupo['porcentaje'] >= 75
                                            ? 'estado-alto'
                                            : ($grupo['porcentaje'] >= 50 ? 'estado-medio' : 'estado-bajo');
                                    ?>
                                    <tr>
                                        <td><strong><?= (int) $grupo['curso'] ?>° <?= (int) $grupo['division'] ?>ª</strong></td>
                                        <td><?= (int) $grupo['total'] ?></td>
                                        <td><?= (int) $grupo['votaron'] ?></td>
                                        <td><?= (int) $grupo['no_votaron'] ?></td>
                                        <td class="barra-curso">
                                            <div class="barra segmentada">
                                                <div style="width:<?= $grupo['porcentaje'] ?>%;background:#16a34a"></div>
                                                <div style="width:<?= 100 - $grupo['porcentaje'] ?>%;background:#ef4444"></div>
                                            </div>
                                            <small class="<?= $claseEstado ?>">
                                                Votó <?= number_format($grupo['porcentaje'], 1, ',', '.') ?>% ·
                                                No votó <?= number_format(100 - $grupo['porcentaje'], 1, ',', '.') ?>%
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="sin-datos">No hay cursos cargados en el padrón.</div>
                <?php endif; ?>
            </article>
        </section>

        <p class="privado">Panel de uso interno para las autoridades de mesa. Los resultados por lista no deberían mostrarse públicamente mientras la elección esté abierta.</p>
    <?php endif; ?>
</main>

<script>
    const panelInicial = <?= json_encode($panelInicial, JSON_UNESCAPED_UNICODE) ?>;
    const botonesPanel = Array.from(document.querySelectorAll('[data-panel]'));
    const paneles = Array.from(document.querySelectorAll('.panel'));

    function mostrarPanel(nombre) {
        botonesPanel.forEach((boton) => {
            boton.classList.toggle('activa', boton.dataset.panel === nombre);
        });

        paneles.forEach((panel) => {
            panel.classList.toggle('activo', panel.id === 'panel-' + nombre);
        });

        try {
            window.localStorage.setItem('panelEstadisticas', nombre);
        } catch (error) {
        }
    }

    botonesPanel.forEach((boton) => {
        boton.addEventListener('click', () => mostrarPanel(boton.dataset.panel));
    });

    let panelGuardado = '';
    try {
        panelGuardado = window.localStorage.getItem('panelEstadisticas') || '';
    } catch (error) {
    }

    const panelValido = botonesPanel.some((boton) => boton.dataset.panel === panelGuardado);
    mostrarPanel(panelInicial === 'cursos' ? 'cursos' : (panelValido ? panelGuardado : panelInicial));
</script>
</body>
</html>
