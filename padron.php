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
    die("Error de conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

$mensaje = "";
$tipo_mensaje = "";

// ==========================================
// 2. PROCESAR ACCIONES (CREAR, EDITAR, ELIMINAR)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // AGREGAR O EDITAR
    if ($action === 'guardar') {
        $id_padron = intval($_POST['id_padron'] ?? 0);
        $id_instancia = intval($_POST['id_instancia']);
        $id_alumno = intval($_POST['id_alumno']);
        $estado_voto = intval($_POST['estado_voto']);

        if ($id_padron > 0) {
            // Actualizar registro existente
            $stmt = $conexion->prepare("UPDATE padron_electoral SET id_instancia = ?, id_alumno = ?, estado_voto = ? WHERE id_padron = ?");
            $stmt->bind_param("iiii", $id_instancia, $id_alumno, $estado_voto, $id_padron);
            if ($stmt->execute()) {
                $mensaje = "Registro actualizado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al actualizar el registro: " . $conexion->error;
                $tipo_mensaje = "danger";
            }
        } else {
            // Crear nuevo registro
            $stmt = $conexion->prepare("INSERT INTO padron_electoral (id_instancia, id_alumno, estado_voto, fecha_hora_voto) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iii", $id_instancia, $id_alumno, $estado_voto);
            if ($stmt->execute()) {
                $mensaje = "Alumno asignado al padrón correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al crear el registro: " . $conexion->error;
                $tipo_mensaje = "danger";
            }
        }
    }

    // ELIMINAR
    if ($action === 'eliminar') {
        $id_padron = intval($_POST['id_padron']);
        $stmt = $conexion->prepare("DELETE FROM padron_electoral WHERE id_padron = ?");
        $stmt->bind_param("i", $id_padron);
        if ($stmt->execute()) {
            $mensaje = "Registro eliminado del padrón.";
            $tipo_mensaje = "warning";
        } else {
            $mensaje = "Error al eliminar el registro.";
            $tipo_mensaje = "danger";
        }
    }
}

// ==========================================
// 3. OBTENER DATOS PARA EL LISTADO Y COMBOS
// ==========================================
// Obtener listado de Padrón Electoral
$sql_padron = "
    SELECT p.id_padron, p.id_instancia, p.id_alumno, p.estado_voto, p.fecha_hora_voto,
           i.descripcion AS instancia_nombre, i.anio,
           a.nombre, a.apellido, a.dni, a.curso, a.division
    FROM padron_electoral p
    INNER JOIN instancia_electoral i ON p.id_instancia = i.id_instancia
    INNER JOIN alumnos a ON p.id_alumno = a.id_alumno
    ORDER BY p.id_padron DESC
";
$res_padron = $conexion->query($sql_padron);

// Obtener Instancias para el select
$res_instancias = $conexion->query("SELECT id_instancia, descripcion, anio FROM instancia_electoral ORDER BY id_instancia DESC");

// Obtener Alumnos para el select
$res_alumnos = $conexion->query("SELECT id_alumno, apellido, nombre, dni, curso, division FROM alumnos ORDER BY apellido ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Padrón Electoral</title>
    <!-- Bootstrap 5 CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        .header-app {
            background-color: #1e293b;
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }
        .table hover tbody tr:hover {
            background-color: #f8fafc;
        }
        .badge-voto-1 {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-voto-0 {
            background-color: #fef3c7;
            color: #b45309;
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <header class="header-app mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><i class="fa-solid fa-address-book me-2"></i>Gestión de Padrón Electoral</h1>
                <small class="text-white-50">Administración de registros de votantes por instancia</small>
            </div>
            <button class="btn btn-primary fw-medium" onclick="abrirModalNuevo()">
                <i class="fa-solid fa-user-plus me-2"></i>Agregar Registro
            </button>
        </div>
    </header>

    <div class="container mb-5">

        <!-- Mensajes de Alerta -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i><?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tabla principal -->
        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Instancia Electoral</th>
                            <th>Alumno</th>
                            <th>DNI</th>
                            <th>Curso / Div</th>
                            <th>Estado de Voto</th>
                            <th>Fecha/Hora Voto</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_padron && $res_padron->num_rows > 0): ?>
                            <?php while ($row = $res_padron->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id_padron']; ?></strong></td>
                                    <td>
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($row['instancia_nombre']); ?></span>
                                        <small class="d-block text-muted">(<?php echo $row['anio']; ?>)</small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['apellido'] . ', ' . $row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['dni']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['curso'] . '° ' . $row['division'] . 'ª'); ?></span></td>
                                    <td>
                                        <?php if ($row['estado_voto'] == 1): ?>
                                            <span class="badge badge-voto-1 px-3 py-2 rounded-pill"><i class="fa-solid fa-check me-1"></i> Habilitado / Votó</span>
                                        <?php else: ?>
                                            <span class="badge badge-voto-0 px-3 py-2 rounded-pill"><i class="fa-solid fa-clock me-1"></i> Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?php echo $row['fecha_hora_voto'] ? $row['fecha_hora_voto'] : '-'; ?></small></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                onclick='abrirModalEditar(<?php echo json_encode($row); ?>)'>
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmarEliminar(<?php echo $row['id_padron']; ?>, '<?php echo htmlspecialchars($row['apellido'] . ' ' . $row['nombre']); ?>')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No se encontraron registros en el padrón electoral.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Formulario (Crear / Editar) -->
    <div class="modal fade" id="modalPadron" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalPadronLabel">Agregar Registro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="padron_crud.php" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="action" value="guardar">
                        <input type="hidden" name="id_padron" id="id_padron" value="0">

                        <div class="mb-3">
                            <label for="id_instancia" class="form-label fw-semibold">Instancia Electoral</label>
                            <select name="id_instancia" id="id_instancia" class="form-select" required>
                                <option value="">Seleccione una instancia...</option>
                                <?php 
                                $res_instancias->data_seek(0);
                                while ($i = $res_instancias->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $i['id_instancia']; ?>">
                                        <?php echo htmlspecialchars($i['descripcion'] . ' (' . $i['anio'] . ')'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_alumno" class="form-label fw-semibold">Alumno Votante</label>
                            <select name="id_alumno" id="id_alumno" class="form-select" required>
                                <option value="">Seleccione un alumno...</option>
                                <?php 
                                $res_alumnos->data_seek(0);
                                while ($a = $res_alumnos->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $a['id_alumno']; ?>">
                                        <?php echo htmlspecialchars($a['apellido'] . ', ' . $a['nombre'] . ' (DNI: ' . $a['dni'] . ' - ' . $a['curso'] . '° ' . $a['division'] . 'ª)'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="estado_voto" class="form-label fw-semibold">Estado de Habilitación / Voto</label>
                            <select name="estado_voto" id="estado_voto" class="form-select" required>
                                <option value="0">Pendiente (0)</option>
                                <option value="1">Habilitado / Emitido (1)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para eliminar -->
    <form id="formEliminar" action="padron_crud.php" method="POST" style="display: none;">
        <input type="hidden" name="action" value="eliminar">
        <input type="hidden" name="id_padron" id="eliminar_id_padron" value="0">
    </form>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const modalPadron = new bootstrap.Modal(document.getElementById('modalPadron'));

        function abrirModalNuevo() {
            document.getElementById('modalPadronLabel').innerText = 'Agregar Registro al Padrón';
            document.getElementById('id_padron').value = '0';
            document.getElementById('id_instancia').value = '';
            document.getElementById('id_alumno').value = '';
            document.getElementById('estado_voto').value = '0';
            modalPadron.show();
        }

        function abrirModalEditar(datos) {
            document.getElementById('modalPadronLabel').innerText = 'Editar Registro de Padrón #' + datos.id_padron;
            document.getElementById('id_padron').value = datos.id_padron;
            document.getElementById('id_instancia').value = datos.id_instancia;
            document.getElementById('id_alumno').value = datos.id_alumno;
            document.getElementById('estado_voto').value = datos.estado_voto;
            modalPadron.show();
        }

        function confirmarEliminar(idPadron, nombreAlumno) {
            if (confirm(`¿Estás seguro de quitar a "${nombreAlumno}" del padrón electoral?`)) {
                document.getElementById('eliminar_id_padron').value = idPadron;
                document.getElementById('formEliminar').submit();
            }
        }
    </script>
</body>
</html>