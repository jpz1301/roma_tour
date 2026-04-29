<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Configurar includes
$titulo = 'Nuevo Servicio | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Registrar Servicio';

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
<div class="card-header" style="background: linear-gradient(135deg, #198754, #0f5132);">
    <h4><i class="bi bi-calendar-plus"></i> Registrar Servicio Turístico</h4>
</div>
<div class="card-body">

<form id="formProgramacion">
<div class="row">

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Código</label>
        <input type="text" name="codigo" class="form-control" placeholder="Código" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Cliente</label>
        <input type="text" name="cliente" class="form-control" placeholder="Cliente" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Tipo Servicio</label>
        <select name="tipo_servicio" id="tipo_servicio" class="form-select" required>
            <option value="">Seleccione</option>
            <option value="propio">Vehículo propio</option>
            <option value="tercerizado">Proveedor externo</option>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Ruta</label>
        <select name="ruta_id" id="ruta" class="form-select" required>
            <option value="">Seleccionar Ruta</option>
            <?php
            $rutas = pg_query($conexion, "SELECT * FROM rutas ORDER BY nombre");
            while($r = pg_fetch_assoc($rutas)){
                echo "<option value='".$r['id']."' data-km='".$r['distancia_km']."'>".htmlspecialchars($r['nombre'])."</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">KM Estimado</label>
        <input type="number" id="km_estimado" class="form-control" placeholder="KM estimado" readonly>
    </div>

    <div class="col-md-3 mb-3" id="campo_vehiculo">
        <label class="form-label fw-semibold">Vehículo</label>
        <select name="vehiculo_id" class="form-select">
            <option value="">Seleccione</option>
            <?php
            $vehiculos = pg_query($conexion, "SELECT * FROM vehiculos ORDER BY placa");
            while($v = pg_fetch_assoc($vehiculos)){
                echo "<option value='".$v['id_vehiculo']."'>".htmlspecialchars($v['placa'])."</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-3 mb-3" id="campo_proveedor" style="display:none;">
        <label class="form-label fw-semibold">Proveedor</label>
        <input type="text" name="proveedor" class="form-control" placeholder="Proveedor">
    </div>

    <div class="col-md-3 mb-3" id="campo_placa_externa" style="display:none;">
        <label class="form-label fw-semibold">Placa Externa</label>
        <input type="text" name="placa_externa" class="form-control" placeholder="Placa externa">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Conductor</label>
        <select name="conductor" class="form-select" required>
            <option value="">Seleccione</option>
            <?php
            $conductores = pg_query($conexion, "SELECT * FROM conductores ORDER BY nombre");
            while($c = pg_fetch_assoc($conductores)){
                echo "<option value='".htmlspecialchars($c['nombre'])."'>".htmlspecialchars($c['nombre'])."</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Fecha</label>
        <input type="date" name="fecha" class="form-control" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Hora</label>
        <input type="time" name="hora" class="form-control" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">Personas</label>
        <input type="number" name="cantidad_personas" class="form-control" placeholder="Cantidad">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold">KM Salida</label>
        <input type="number" name="km_salida" class="form-control" placeholder="KM salida">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones"></textarea>
    </div>

    <div class="col-md-12">
        <button type="submit" class="btn btn-success w-100"><i class="bi bi-save"></i> Guardar Servicio</button>
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
});

// Control tipo servicio
$("#tipo_servicio").change(function(){
    let tipo = $(this).val();
    if(tipo === "tercerizado"){
        $("#campo_proveedor").show();
        $("#campo_placa_externa").show();
        $("#campo_vehiculo").hide();
    } else {
        $("#campo_proveedor").hide();
        $("#campo_placa_externa").hide();
        $("#campo_vehiculo").show();
    }
});

// Guardar
$("#formProgramacion").submit(function(e){
    e.preventDefault();
    $.post("guardar_programacion.php", $(this).serialize(), function(res){
        if(res == "ok"){
            alert("Guardado correctamente");
            window.location = "listar_programacion.php";
        } else {
            alert("Error al guardar");
        }
    });
});
</script>

<?php include("../../includes/footer.php"); ?>