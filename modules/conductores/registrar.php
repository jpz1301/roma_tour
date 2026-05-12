<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $licencia = $_POST['licencia'];
    $estado = $_POST['estado'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $dias_libres = $_POST['dias_libres'];
    $dias_salidas = $_POST['dias_salidas'];
    $direccion = $_POST['direccion'];
    $telefono_emergencia = $_POST['telefono_emergencia'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $vacaciones = $_POST['vacaciones'];

    // CORRECCIÓN: Convertir vacíos a NULL para campos numéricos y fecha
    $dias_libres = ($dias_libres !== '' && $dias_libres !== null) ? intval($dias_libres) : null;
    $dias_salidas = ($dias_salidas !== '' && $dias_salidas !== null) ? intval($dias_salidas) : null;
    $vacaciones = ($vacaciones !== '' && $vacaciones !== null) ? intval($vacaciones) : null;
    $fecha_ingreso = ($fecha_ingreso !== '' && $fecha_ingreso !== null) ? $fecha_ingreso : null;

    $sql = "INSERT INTO conductores 
            (nombre, dni, telefono, licencia, estado,
             fecha_ingreso, dias_libres, dias_salidas, direccion, telefono_emergencia, tipo_contrato, vacaciones) 
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12)";

    $result = pg_query_params($conexion, $sql, [
        $nombre, $dni, $telefono, $licencia, $estado,
        $fecha_ingreso, $dias_libres, $dias_salidas, $direccion, $telefono_emergencia, $tipo_contrato, $vacaciones
    ]);

    if ($result) {
        header("Location: conductores.php");
        exit();
    } else {
        $error = "Error al guardar: " . pg_last_error($conexion);
    }
}

// Configurar includes
$titulo = 'Registrar Conductor | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Registrar Conductor';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTÓN VOLVER -->
<div class="container mb-3">
    <a href="conductores.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver a Conductores
    </a>
</div>

<div class="container mb-5">

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?></div>
<?php endif; ?>

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #198754, #0f5132);">
    <h4><i class="bi bi-person-plus-fill"></i> Registrar Nuevo Conductor</h4>
</div>
<div class="card-body">

<form method="POST">

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Nombre Completo</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">DNI</label>
        <input type="text" name="dni" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Teléfono</label>
        <input type="text" name="telefono" class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Licencia</label>
        <input type="text" name="licencia" class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select">
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
        </select>
    </div>
</div>

<hr>
<h5 class="fw-bold">📋 Información Adicional</h5>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Fecha de Ingreso</label>
        <input type="date" name="fecha_ingreso" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Días Libres</label>
        <input type="number" name="dias_libres" class="form-control" placeholder="0">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Días de Salidas</label>
        <input type="number" name="dias_salidas" class="form-control" placeholder="0">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Dirección</label>
        <input type="text" name="direccion" class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Teléfono de Emergencia</label>
        <input type="text" name="telefono_emergencia" class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Tipo de Contrato</label>
        <input type="text" name="tipo_contrato" class="form-control" placeholder="Ej: Plazo fijo, Indefinido...">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Vacaciones (días)</label>
        <input type="number" name="vacaciones" class="form-control" placeholder="0">
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Guardar Conductor</button>
    <a href="conductores.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
</div>

</form>
</div></div>
</div>

<?php include("../../includes/footer.php"); ?>