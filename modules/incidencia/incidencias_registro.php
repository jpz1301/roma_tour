<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$vehiculos = pg_query($conexion, "SELECT placa FROM vehiculos ORDER BY placa ASC");
$conductores = pg_query($conexion, "SELECT nombre FROM conductores ORDER BY nombre ASC");

// Configurar includes
$titulo = 'Registrar Incidencia | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Registrar Incidencia';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + VER LISTA -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="incidencias.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Incidencias
        </a>
        <a href="incidencias.php" class="btn btn-outline-danger">
            <i class="bi bi-list-ul"></i> Ver Incidencias
        </a>
    </div>
</div>

<div class="container mb-5">

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #dc3545, #b02a37);">
    <h4><i class="bi bi-exclamation-triangle-fill"></i> Registrar Incidencia</h4>
</div>
<div class="card-body">

<form method="POST" action="guardar_incidencias.php">

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Tipo de Servicio</label>
        <select name="tipo_servicio" id="tipo_servicio" class="form-select">
            <option value="propio">Propio</option>
            <option value="tercerizado">Tercerizado</option>
        </select>
    </div>

    <div class="col-md-6 mb-3" id="box_proveedor" style="display:none;">
        <label class="form-label fw-semibold">Proveedor</label>
        <input type="text" name="proveedor" class="form-control" placeholder="Ej: Transportes SAC">
    </div>

    <div class="col-md-6 mb-3" id="box_tipo_unidad" style="display:none;">
        <label class="form-label fw-semibold">Tipo de Unidad</label>
        <select name="tipo_unidad" class="form-select">
            <option value="">Seleccione</option>
            <option value="Bus">🚌 Bus</option>
            <option value="Van">🚐 Van</option>
            <option value="Minivan">🚐 Minivan</option>
            <option value="Auto">🚗 Auto</option>
            <option value="Camioneta">🚙 Camioneta</option>
        </select>
    </div>

    <div class="col-md-6 mb-3" id="box_vehiculo">
        <label class="form-label fw-semibold">Vehículo</label>
        <select name="placa" class="form-select">
            <option value="">Seleccione</option>
            <?php while($v = pg_fetch_assoc($vehiculos)): ?>
                <option value="<?= htmlspecialchars($v['placa']) ?>"><?= htmlspecialchars($v['placa']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-6 mb-3" id="box_placa_manual" style="display:none;">
        <label class="form-label fw-semibold">Placa (Proveedor)</label>
        <input type="text" name="placa_manual" class="form-control" placeholder="Ej: ABC-123">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Fecha</label>
        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Conductor</label>
        <select name="conductor" class="form-select select2" required>
            <option value="">Buscar conductor...</option>
            <?php while($c = pg_fetch_assoc($conductores)): ?>
                <option value="<?= htmlspecialchars($c['nombre']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Tipo de Incidencia</label>
        <select name="tipo" class="form-select" required>
            <option value="">Seleccione tipo</option>
            <option value="Leve">🟡 Leve</option>
            <option value="Media">🟠 Media</option>
            <option value="Grave">🔴 Grave</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Costo (S/)</label>
        <input type="number" step="0.01" name="costo" class="form-control" placeholder="Ej: 150.00">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">Descripción de la Incidencia</label>
        <textarea name="incidencia" class="form-control" rows="4" placeholder="Describe lo ocurrido..." required></textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-danger"><i class="bi bi-save"></i> Guardar Incidencia</button>
    <a href="incidencias.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
</div>

</form>
</div></div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    $('.select2').select2({ width: '100%' });
});

document.addEventListener("DOMContentLoaded", function(){
    const tipo = document.getElementById("tipo_servicio");
    const proveedor = document.getElementById("box_proveedor");
    const vehiculo = document.getElementById("box_vehiculo");
    const manual = document.getElementById("box_placa_manual");
    const tipoUnidad = document.getElementById("box_tipo_unidad");

    function cambiar(){
        if(tipo.value === "tercerizado"){
            proveedor.style.display = "block";
            manual.style.display = "block";
            tipoUnidad.style.display = "block";
            vehiculo.style.display = "none";
        } else {
            proveedor.style.display = "none";
            manual.style.display = "none";
            tipoUnidad.style.display = "none";
            vehiculo.style.display = "block";
        }
    }

    tipo.addEventListener("change", cambiar);
    cambiar();
});
</script>

<?php include("../../includes/footer.php"); ?>