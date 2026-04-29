<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = intval($_GET['id'] ?? 0);

$sql = "SELECT * FROM programacion_servicio WHERE id = $id";
$res = pg_query($conexion, $sql);
$datos = pg_fetch_assoc($res);

if (!$datos) {
    echo "Servicio no encontrado";
    exit();
}

// Configurar includes
$titulo = 'Editar Servicio | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Editar Servicio';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTÓN VOLVER -->
<div class="container mb-3">
    <a href="listar_programacion.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver a Servicios
    </a>
</div>

<div class="container mb-5">

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
    <h4><i class="bi bi-pencil-fill"></i> Editar Servicio: <?= htmlspecialchars($datos['codigo']) ?></h4>
</div>
<div class="card-body">

<form id="formEditarProgramacion">
<input type="hidden" name="id" value="<?= $id ?>">

<div class="row">

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Código</label>
        <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($datos['codigo']) ?>" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Cliente</label>
        <input type="text" name="cliente" class="form-control" value="<?= htmlspecialchars($datos['cliente']) ?>" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Tipo Servicio</label>
        <select name="tipo_servicio" id="tipo_servicio" class="form-select" required>
            <option value="propio" <?= ($datos['tipo_servicio'] ?? '') == 'propio' ? 'selected' : '' ?>>Vehículo propio</option>
            <option value="tercerizado" <?= ($datos['tipo_servicio'] ?? '') == 'tercerizado' ? 'selected' : '' ?>>Proveedor externo</option>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Ruta</label>
        <select name="ruta_id" id="ruta" class="form-select" required>
            <option value="">Seleccionar Ruta</option>
            <?php
            $rutas = pg_query($conexion, "SELECT * FROM rutas ORDER BY nombre");
            while($r = pg_fetch_assoc($rutas)):
                $sel = ($r['id'] == ($datos['ruta_id'] ?? '')) ? 'selected' : '';
            ?>
                <option value="<?= $r['id'] ?>" data-km="<?= $r['distancia_km'] ?>" <?= $sel ?>>
                    <?= htmlspecialchars($r['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">KM Estimado</label>
        <input type="number" id="km_estimado" class="form-control" readonly>
    </div>

    <div class="col-md-3 mb-3" id="campo_vehiculo" style="<?= ($datos['tipo_servicio'] ?? '') == 'tercerizado' ? 'display:none;' : '' ?>">
        <label class="form-label fw-semibold">Vehículo</label>
        <select name="vehiculo_id" class="form-select">
            <option value="">Seleccione</option>
            <?php
            $vehiculos = pg_query($conexion, "SELECT * FROM vehiculos ORDER BY placa");
            while($v = pg_fetch_assoc($vehiculos)):
                $sel = ($v['id_vehiculo'] == ($datos['vehiculo_id'] ?? '')) ? 'selected' : '';
            ?>
                <option value="<?= $v['id_vehiculo'] ?>" <?= $sel ?>><?= htmlspecialchars($v['placa']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3 mb-3" id="campo_proveedor" style="<?= ($datos['tipo_servicio'] ?? '') != 'tercerizado' ? 'display:none;' : '' ?>">
        <label class="form-label fw-semibold">Proveedor</label>
        <input type="text" name="proveedor" class="form-control" value="<?= htmlspecialchars($datos['proveedor'] ?? '') ?>">
    </div>

    <div class="col-md-3 mb-3" id="campo_placa_externa" style="<?= ($datos['tipo_servicio'] ?? '') != 'tercerizado' ? 'display:none;' : '' ?>">
        <label class="form-label fw-semibold">Placa Externa</label>
        <input type="text" name="placa_externa" class="form-control" value="<?= htmlspecialchars($datos['placa_externa'] ?? '') ?>">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Conductor</label>
        <select name="conductor" class="form-select" required>
            <option value="">Seleccione</option>
            <?php
            $conductores = pg_query($conexion, "SELECT * FROM conductores ORDER BY nombre");
            while($c = pg_fetch_assoc($conductores)):
                $sel = ($c['nombre'] == ($datos['conductor'] ?? '')) ? 'selected' : '';
            ?>
                <option value="<?= htmlspecialchars($c['nombre']) ?>" <?= $sel ?>><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Fecha</label>
        <input type="date" name="fecha" class="form-control" value="<?= $datos['fecha'] ?>" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Hora</label>
        <input type="time" name="hora" class="form-control" value="<?= $datos['hora'] ?>" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Personas</label>
        <input type="number" name="cantidad_personas" class="form-control" value="<?= $datos['cantidad_personas'] ?>">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">KM Salida</label>
        <input type="number" name="km_salida" class="form-control" value="<?= $datos['km_salida'] ?>">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select">
            <option value="Programado" <?= ($datos['estado'] ?? '') == 'Programado' ? 'selected' : '' ?>>📋 Programado</option>
            <option value="En ruta" <?= ($datos['estado'] ?? '') == 'En ruta' ? 'selected' : '' ?>>🚌 En ruta</option>
            <option value="Finalizado" <?= ($datos['estado'] ?? '') == 'Finalizado' ? 'selected' : '' ?>>✅ Finalizado</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="2"><?= htmlspecialchars($datos['observaciones'] ?? '') ?></textarea>
    </div>

    <div class="col-md-12">
        <button type="submit" class="btn btn-warning w-100"><i class="bi bi-save"></i> Actualizar Servicio</button>
    </div>

</div>
</form>

</div></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// KM automático
$("#ruta").change(function(){
    let km = $(this).find(':selected').data('km');
    $("#km_estimado").val(km);
}).trigger('change');

// Control tipo servicio
function toggleTipo(){
    if($("#tipo_servicio").val() === "tercerizado"){
        $("#campo_proveedor, #campo_placa_externa").show();
        $("#campo_vehiculo").hide();
    } else {
        $("#campo_proveedor, #campo_placa_externa").hide();
        $("#campo_vehiculo").show();
    }
}
$("#tipo_servicio").change(toggleTipo);
toggleTipo();

// Actualizar
$("#formEditarProgramacion").submit(function(e){
    e.preventDefault();
    $.post("actualizar_programacion.php", $(this).serialize(), function(res){
        if(res == "ok"){
            alert("Actualizado correctamente");
            window.location = "listar_programacion.php";
        } else {
            alert("Error al actualizar");
        }
    });
});
</script>

<?php include("../../includes/footer.php"); ?>