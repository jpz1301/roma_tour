<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = intval($_GET['id']);

$sql = "SELECT * FROM conductores WHERE id_conductor = $id";
$result = pg_query($conexion, $sql);
$datos = pg_fetch_assoc($result);

if (!$datos) {
    echo "Conductor no encontrado";
    exit();
}

// Configurar includes
$titulo = 'Editar Conductor | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Editar Conductor';

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

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
    <h4><i class="bi bi-pencil-fill"></i> Editar Conductor: <?= htmlspecialchars($datos['nombre']) ?></h4>
</div>
<div class="card-body">

<form action="guardar.php" method="POST">

<input type="hidden" name="id" value="<?= $datos['id_conductor'] ?>">

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Nombre Completo</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">DNI</label>
        <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($datos['dni'] ?? '') ?>" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Licencia</label>
        <input type="text" name="licencia" class="form-control" value="<?= htmlspecialchars($datos['licencia'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select">
            <option value="Activo" <?= ($datos['estado']??'')=='Activo'?'selected':'' ?>>Activo</option>
            <option value="Inactivo" <?= ($datos['estado']??'')=='Inactivo'?'selected':'' ?>>Inactivo</option>
        </select>
    </div>
</div>

<hr>
<h5 class="fw-bold">📋 Información Adicional</h5>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Fecha de Ingreso</label>
        <input type="date" name="fecha_ingreso" class="form-control" value="<?= $datos['fecha_ingreso'] ?? '' ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Días Libres</label>
        <input type="number" name="dias_libres" class="form-control" value="<?= $datos['dias_libres'] ?? '' ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Días de Salidas</label>
        <input type="number" name="dias_salidas" class="form-control" value="<?= $datos['dias_salidas'] ?? '' ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Teléfono de Emergencia</label>
        <input type="text" name="telefono_emergencia" class="form-control" value="<?= htmlspecialchars($datos['telefono_emergencia'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Tipo de Contrato</label>
        <input type="text" name="tipo_contrato" class="form-control" value="<?= htmlspecialchars($datos['tipo_contrato'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Vacaciones (días)</label>
        <input type="number" name="vacaciones" class="form-control" value="<?= $datos['vacaciones'] ?? '' ?>">
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar</button>
    <a href="conductores.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
</div>

</form>
</div></div>
</div>

<?php include("../../includes/footer.php"); ?>