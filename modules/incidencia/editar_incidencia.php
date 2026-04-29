<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = intval($_GET['id'] ?? 0);

if ($id == 0) {
    die("ID no válido");
}

$sql = "SELECT * FROM incidencias WHERE id_incidencia = $1";
$result = pg_query_params($conexion, $sql, [$id]);
$data = pg_fetch_assoc($result);

if (!$data) {
    echo "Incidencia no encontrada";
    exit();
}

$vehiculos = pg_query($conexion, "SELECT placa FROM vehiculos ORDER BY placa ASC");
$conductores = pg_query($conexion, "SELECT nombre FROM conductores ORDER BY nombre ASC");

// Configurar includes
$titulo = 'Editar Incidencia | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Editar Incidencia';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTÓN VOLVER -->
<div class="container mb-3">
    <a href="incidencias.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver a Incidencias
    </a>
</div>

<div class="container mb-5">

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
    <h4><i class="bi bi-pencil-fill"></i> Editar Incidencia #<?= $data['id_incidencia'] ?></h4>
</div>
<div class="card-body">

<form method="POST" action="actualizar_incidencia.php">
<input type="hidden" name="id" value="<?= $data['id_incidencia'] ?>">

<div class="row">

    <!-- TIPO SERVICIO -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Tipo de Servicio</label>
        <select name="tipo_servicio" id="tipo_servicio" class="form-select">
            <option value="propio" <?= ($data['tipo_servicio'] ?? '') == 'propio' ? 'selected' : '' ?>>Propio</option>
            <option value="tercerizado" <?= ($data['tipo_servicio'] ?? '') == 'tercerizado' ? 'selected' : '' ?>>Tercerizado</option>
        </select>
    </div>

    <!-- PROVEEDOR -->
    <div class="col-md-6 mb-3" id="box_proveedor" style="<?= ($data['tipo_servicio'] ?? '') != 'tercerizado' ? 'display:none;' : '' ?>">
        <label class="form-label fw-semibold">Proveedor</label>
        <input type="text" name="proveedor" class="form-control" value="<?= htmlspecialchars($data['proveedor'] ?? '') ?>" placeholder="Ej: Transportes SAC">
    </div>

    <!-- TIPO UNIDAD -->
    <div class="col-md-6 mb-3" id="box_tipo_unidad" style="<?= ($data['tipo_servicio'] ?? '') != 'tercerizado' ? 'display:none;' : '' ?>">
        <label class="form-label fw-semibold">Tipo de Unidad</label>
        <select name="tipo_unidad" class="form-select">
            <option value="">Seleccione</option>
            <?php
            $tipos = ['Bus'=>'🚌 Bus', 'Van'=>'🚐 Van', 'Minivan'=>'🚐 Minivan', 'Auto'=>'🚗 Auto', 'Camioneta'=>'🚙 Camioneta'];
            foreach($tipos as $k => $v):
                $sel = ($data['tipo_unidad'] ?? '') == $k ? 'selected' : '';
                echo "<option value='$k' $sel>$v</option>";
            endforeach;
            ?>
        </select>
    </div>

    <!-- VEHÍCULO -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Vehículo</label>
        <select name="placa" class="form-select select2" required>
            <?php while($v = pg_fetch_assoc($vehiculos)): ?>
                <option value="<?= htmlspecialchars($v['placa']) ?>" <?= ($v['placa'] == $data['placa']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($v['placa']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- FECHA -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Fecha</label>
        <input type="date" name="fecha" class="form-control" value="<?= $data['fecha'] ?>" required>
    </div>

    <!-- CONDUCTOR -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Conductor</label>
        <select name="conductor" class="form-select select2" required>
            <?php while($c = pg_fetch_assoc($conductores)): ?>
                <option value="<?= htmlspecialchars($c['nombre']) ?>" <?= ($c['nombre'] == $data['conductor']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- TIPO -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Tipo de Incidencia</label>
        <select name="tipo" class="form-select" required>
            <option value="">Seleccione</option>
            <option value="Leve" <?= ($data['tipo'] ?? '') == 'Leve' ? 'selected' : '' ?>>🟡 Leve</option>
            <option value="Media" <?= ($data['tipo'] ?? '') == 'Media' ? 'selected' : '' ?>>🟠 Media</option>
            <option value="Grave" <?= ($data['tipo'] ?? '') == 'Grave' ? 'selected' : '' ?>>🔴 Grave</option>
        </select>
    </div>

    <!-- COSTO -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Costo (S/)</label>
        <input type="number" step="0.01" name="costo" class="form-control" value="<?= $data['costo'] ?? '' ?>" placeholder="Ej: 150.00">
    </div>

    <!-- DESCRIPCIÓN -->
    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">Descripción</label>
        <textarea name="incidencia" class="form-control" rows="4" required><?= htmlspecialchars($data['incidencia'] ?? '') ?></textarea>
    </div>

</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar</button>
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
    
    // Mostrar/ocultar campos según tipo de servicio
    $('#tipo_servicio').change(function(){
        if($(this).val() === 'tercerizado'){
            $('#box_proveedor, #box_tipo_unidad').show();
        } else {
            $('#box_proveedor, #box_tipo_unidad').hide();
        }
    });
});
</script>

<?php include("../../includes/footer.php"); ?>