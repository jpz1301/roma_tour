<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = intval($_GET['id'] ?? 0);

$mantenimiento = pg_fetch_assoc(
    pg_query($conexion, "SELECT * FROM mantenimiento WHERE id=$id")
);

if (!$mantenimiento) {
    echo "Mantenimiento no encontrado";
    exit();
}

$vehiculos = pg_query($conexion, "SELECT id_vehiculo, placa FROM vehiculos ORDER BY placa");

// Obtener conductores para el select
$conductores = pg_query($conexion, "SELECT id_conductor, nombre FROM conductores ORDER BY nombre");

// Configurar includes
$titulo = 'Editar Mantenimiento | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Editar Mantenimiento';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTÓN VOLVER -->
<div class="container mb-3">
    <a href="listar_mantenimiento.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver a Mantenimientos
    </a>
</div>

<div class="container mb-5">

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
    <h4><i class="bi bi-pencil-fill"></i> Editar Mantenimiento #<?= $id ?></h4>
</div>
<div class="card-body">

<form method="POST" action="actualizar_mantenimiento.php">
<input type="hidden" name="id" value="<?= $id ?>">

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Fecha</label>
        <input type="date" name="fecha" value="<?= $mantenimiento['fecha'] ?>" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Vehículo</label>
        <select name="vehiculo_id" class="form-select" required>
            <option value="">Seleccione</option>
            <?php while($v = pg_fetch_assoc($vehiculos)): ?>
                <option value="<?= $v['id_vehiculo'] ?>" <?= $v['id_vehiculo']==$mantenimiento['vehiculo_id']?'selected':'' ?>>
                    <?= htmlspecialchars($v['placa']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- RESPONSABLE CON SELECT DE CONDUCTORES -->
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Responsable (Conductor)</label>
        <select name="responsable_id" class="form-select" required>
            <option value="">-- Seleccionar conductor --</option>
            <?php 
            // Obtener el nombre del responsable actual (texto)
            $nombre_actual = $mantenimiento['responsable'];
            // Recorrer conductores y preseleccionar si el nombre coincide
            while($conductor = pg_fetch_assoc($conductores)):
                $selected = ($conductor['nombre'] == $nombre_actual) ? 'selected' : '';
            ?>
                <option value="<?= $conductor['id_conductor'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($conductor['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Mecánico</label>
        <input type="text" name="mecanico" class="form-control" value="<?= htmlspecialchars($mantenimiento['mecanico_id'] ?? '') ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Taller</label>
        <input type="text" name="taller" class="form-control" value="<?= htmlspecialchars($mantenimiento['taller_id'] ?? '') ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Tipo</label>
        <select name="tipo" class="form-select" required>
            <option value="Preventivo" <?= $mantenimiento['tipo']=='Preventivo'?'selected':'' ?>>🛠 Preventivo</option>
            <option value="Correctivo" <?= $mantenimiento['tipo']=='Correctivo'?'selected':'' ?>>⚠ Correctivo</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">Problema</label>
        <input type="text" name="problema" value="<?= htmlspecialchars($mantenimiento['problema']) ?>" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Costo (S/)</label>
        <input type="number" step="0.01" name="costo" value="<?= $mantenimiento['costo'] ?>" class="form-control">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($mantenimiento['observaciones'] ?? '') ?></textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar</button>
    <a href="listar_mantenimiento.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
</div>

</form>
</div></div>
</div>

<?php include("../../includes/footer.php"); ?>