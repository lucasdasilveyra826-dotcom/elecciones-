<?php
// ==========================================
// 1. CONFIGURACIÓN Y CONEXIÓN A LA BBDD
// ==========================================
header('Content-Type: text/html; charset=utf-8');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistema_voto";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

// ==========================================
// 2. OBTENER INSTANCIA ELECTORAL ACTIVA
// ==========================================
$sql_instancia = "SELECT id_instancia, descripcion, anio FROM instancia_electoral WHERE activa = 1 LIMIT 1";
$res_instancia = $conexion->query($sql_instancia);
$instancia_activa = $res_instancia ? $res_instancia->fetch_assoc() : null;

// ==========================================
// 3. PROCESAR LA EMISIÓN DEL VOTO
// ==========================================
$voto_registrado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirmar_voto') {
    $id_lista = intval($_POST['id_lista']);
    $id_instancia = $instancia_activa ? $instancia_activa['id_instancia'] : 0;

    if ($id_lista > 0) {
        $stmt_voto = $conexion->prepare("INSERT INTO votos (id_instancia, id_lista, fecha_hora) VALUES (?, ?, NOW())");
        $stmt_voto->bind_param("ii", $id_instancia, $id_lista);
    } else {
        $stmt_voto = $conexion->prepare("INSERT INTO votos (id_instancia, id_lista, fecha_hora) VALUES (?, NULL, NOW())");
        $stmt_voto->bind_param("i", $id_instancia);
    }
    
    if ($stmt_voto->execute()) {
        $voto_registrado = true;
    }
}

// ==========================================
// 4. OBTENER LISTAS Y SUS CANDIDATOS
// ==========================================
$listas = [];
if ($instancia_activa) {
    $id_instancia = $instancia_activa['id_instancia'];
    
    $sql_listas = "SELECT id_lista, numero_lista, nombre_lista, logo_url FROM lista WHERE id_instancia = ? ORDER BY numero_lista ASC";
    $stmt_l = $conexion->prepare($sql_listas);
    $stmt_l->bind_param("i", $id_instancia);
    $stmt_l->execute();
    $res_l = $stmt_l->get_result();

    while ($l = $res_l->fetch_assoc()) {
        $id_lista = $l['id_lista'];
        
        $sql_cand = "
            SELECT cpl.id_cargo, c.nombre_cargo, a.nombre, a.apellido 
            FROM cargos_por_lista cpl
            INNER JOIN cargos c ON cpl.id_cargo = c.id_cargo
            INNER JOIN alumnos a ON cpl.id_alumno = a.id_alumno
            WHERE cpl.id_lista = ? AND cpl.id_cargo IN (1, 2, 3)
            ORDER BY cpl.id_cargo ASC
        ";
        $stmt_c = $conexion->prepare($sql_cand);
        $stmt_c->bind_param("i", $id_lista);
        $stmt_c->execute();
        $res_c = $stmt_c->get_result();

        $candidatos = [
            1 => 'No asignado',
            2 => 'No asignado',
            3 => 'No asignado'
        ];

        while ($cand = $res_c->fetch_assoc()) {
            $candidatos[$cand['id_cargo']] = $cand['apellido'] . ', ' . $cand['nombre'];
        }

        $l['candidatos'] = $candidatos;
        $listas[] = $l;
        $stmt_c->close();
    }
    $stmt_l->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegí a tu candidato/a</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header-instancia {
            background-color: #1e293b;
            color: #ffffff;
            padding: 20px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-container h1 {
            font-size: 1.4rem;
            font-weight: 500;
        }

        .badge {
            background-color: #2563eb;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 12px;
        }

        .main-container {
            flex: 1;
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .lista-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .lista-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.12);
        }

        .lista-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 50%;
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            margin-bottom: 16px;
            padding: 5px;
        }

        .lista-num {
            font-size: 0.85rem;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .lista-nombre {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin: 4px 0 16px 0;
        }

        .candidatos-list {
            width: 100%;
            text-align: left;
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            flex: 1;
        }

        .candidato-item {
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #334155;
        }

        .candidato-item:last-child {
            margin-bottom: 0;
        }

        .candidato-cargo {
            font-weight: 700;
            color: #475569;
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .btn-votar {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 12px 0;
            width: 100%;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-votar:hover {
            background-color: #1d4ed8;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        }

        .modal-card h3 {
            font-size: 1.3rem;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .modal-card p {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 24px;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-aceptar {
            background-color: #16a34a;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 12px;
            flex: 1;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-cancelar {
            background-color: #ef4444;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 12px;
            flex: 1;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
        }

        .thanks-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 50px 30px;
            text-align: center;
            max-width: 600px;
            margin: 40px auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-top: 6px solid #16a34a;
        }

        .thanks-box h2 {
            font-size: 2rem;
            color: #166534;
            margin-bottom: 12px;
        }

        .thanks-box p {
            color: #475569;
            font-size: 1.1rem;
            margin-bottom: 24px;
        }

        .btn-reiniciar {
            background-color: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

    <header class="header-instancia">
        <div class="header-container">
            <span class="badge">Elección Activa</span>
            <h1>
                <?php 
                    if ($instancia_activa) {
                        echo htmlspecialchars($instancia_activa['descripcion']) . " — " . htmlspecialchars($instancia_activa['anio']);
                    } else {
                        echo "Sin Instancia Electoral Activa";
                    }
                ?>
            </h1>
        </div>
    </header>

    <main class="main-container">

        <?php if ($voto_registrado): ?>
            <div class="thanks-box">
                <h2>¡Gracias por participar!</h2>
                <p>Tu voto ha sido registrado correctamente en la urna digital.</p>
                <a href="cuarto_oscuro.php" class="btn-reiniciar">Finalizar y Continuar</a>
            </div>

        <?php else: ?>

            <div class="cards-grid">
                <?php if (!empty($listas)): ?>
                    <?php foreach ($listas as $lista): ?>
                        <div class="lista-card">
                            <img src="<?php echo !empty($lista['logo_url']) ? htmlspecialchars($lista['logo_url']) : 'https://via.placeholder.com/90?text=LOGO'; ?>" alt="Logo Lista" class="lista-logo">
                            <span class="lista-num">Lista N° <?php echo htmlspecialchars($lista['numero_lista']); ?></span>
                            <h3 class="lista-nombre"><?php echo htmlspecialchars($lista['nombre_lista']); ?></h3>

                            <div class="candidatos-list">
                                <div class="candidato-item">
                                    <span class="candidato-cargo">Presidente</span>
                                    <?php echo htmlspecialchars($lista['candidatos'][1]); ?>
                                </div>
                                <div class="candidato-item">
                                    <span class="candidato-cargo">Secretario General</span>
                                    <?php echo htmlspecialchars($lista['candidatos'][2]); ?>
                                </div>
                                <div class="candidato-item">
                                    <span class="candidato-cargo">Secretario de Finanzas</span>
                                    <?php echo htmlspecialchars($lista['candidatos'][3]); ?>
                                </div>
                            </div>

                            <button class="btn-votar" onclick="abrirModal(<?php echo $lista['id_lista']; ?>, '<?php echo htmlspecialchars(addslashes($lista['nombre_lista'])); ?>')">Votar</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center;">No hay listas cargadas en la base de datos.</p>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </main>

    <div class="modal-overlay" id="modalConfirmacion">
        <div class="modal-card">
            <h3>Confirmación de Voto</h3>
            <p>¿Estás seguro/a de tu decisión?</p>
            <p><strong id="modal-nombre-lista" style="color: #2563eb;"></strong></p>

            <form action="cuarto_oscuro.php" method="POST">
                <input type="hidden" name="action" value="confirmar_voto">
                <input type="hidden" name="id_lista" id="modal-id-lista" value="0">

                <div class="modal-buttons">
                    <button type="submit" class="btn-aceptar">Aceptar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(idLista, nombreLista) {
            document.getElementById('modal-id-lista').value = idLista;
            document.getElementById('modal-nombre-lista').innerText = nombreLista;
            document.getElementById('modalConfirmacion').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modalConfirmacion').style.display = 'none';
        }
    </script>
</body>
</html>