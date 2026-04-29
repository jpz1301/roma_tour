<?php
include("../../config/conexion.php");

$vehiculos = pg_query($conexion, "SELECT id_vehiculo, placa FROM vehiculos");
$conductores = pg_query($conexion, "SELECT id_conductor, nombre, dni FROM conductores");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro de Inspección</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.section-title{
    background:#f1f1f1;
    padding:8px;
    border-left:5px solid #198754;
    margin-top:20px;
}
</style>

</head>

<body class="bg-light">

<div class="container mt-4">
<div class="card shadow">

<div class="card-header bg-success text-white">
<h4>Registro de Inspección Vehicular</h4>
</div>

<div class="card-body">

<form method="POST" action="guardar_inspeccion.php">


<h5 class="section-title">Datos Generales</h5>

<div class="row">
<div class="col">
<select name="id_vehiculo" class="form-control" required>
<option value="">Vehículo</option>
<?php while($v = pg_fetch_assoc($vehiculos)): ?>
<option value="<?= $v['id_vehiculo'] ?>"><?= $v['placa'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col">
<select name="id_conductor" class="form-control" required>
<option value="">Conductor</option>
<?php while($c = pg_fetch_assoc($conductores)): ?>
<option value="<?= $c['id_conductor'] ?>"><?= $c['nombre'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col">
<input type="date" name="fecha_inspeccion" class="form-control" required>
</div>
</div>
<!-- 🔹 DATOS -->

<!-- 🔥 🔹 DOCUMENTACIÓN (AQUÍ VA LO NUEVO) -->
<h5 class="section-title">Documentación del Vehículo</h5>

<div class="row">

<div class="col">
<label>SOAT (Vencimiento)</label>
<input type="date" name="soat" class="form-control" required>
</div>

<div class="col">
<label>Revisión Técnica</label>
<input type="date" name="revision_tecnica" class="form-control" required>
</div>

<div class="col">
<label>Manifiesto de Pasajeros</label>
<select name="manifiesto_pasajeros" class="form-control" required>
<option value="">Seleccione</option>
<option value="1">Sí</option>
<option value="0">No</option>
</select>
</div>

</div>
<!-- 🔹 RUTA -->
<h5 class="section-title">Ruta / Servicio</h5>

<div class="row">
<div class="col">
<input type="text" name="ruta" placeholder="Ruta" class="form-control">
</div>

<div class="col">
<input type="text" name="tipo_servicio" placeholder="Tipo de servicio" class="form-control">
</div>
</div>

<!-- 🔹 CONTROL -->
<h5 class="section-title">Control de Ruta</h5>

<div class="row">
<div class="col">
<input type="time" name="hora_salida" class="form-control">
</div>

<div class="col">
<input type="time" name="hora_llegada" class="form-control">
</div>

<div class="col">
<input type="number" name="km_salida" placeholder="KM Salida" class="form-control">
</div>

<div class="col">
<input type="number" name="km_llegada" placeholder="KM Llegada" class="form-control">
</div>
</div>

<br>

<div class="row">
<div class="col">
<input type="number" name="combustible_salida" placeholder="Combustible Salida" class="form-control">
</div>

<div class="col">
<input type="number" name="combustible_llegada" placeholder="Combustible Llegada" class="form-control">
</div>

<div class="col">
<input type="number" name="pax" placeholder="PAX" class="form-control">
</div>
</div>

<!-- 🔹 CHECKLIST -->
<h5 class="section-title">Checklist</h5>

<table class="table table-bordered text-center">
<tr><th>Item</th><th>B</th><th>R</th><th>M</th></tr>

<?php
$items = [
"espejo_derecho"=>"Espejo Derecho",
"espejo_izquierdo"=>"Espejo Izquierdo",
"claxon"=>"Claxon",
"antena"=>"Antena",
"parabrisas_frontal"=>"Parabrisas Frontal",
"parabrisas_posterior"=>"Parabrisas Posterior",
"tapa_combustible"=>"Tapa Combustible",
"tapa_aceite_motor"=>"Tapa Aceite",
"tapa_radiator"=>"Tapa Radiador",
"luces_altas"=>"Luces Altas",
"luces_bajas"=>"Luces Bajas",
"luces_traseras"=>"Luces Traseras",
"luces_freno"=>"Luces Freno",
"luces_intermitentes"=>"Intermitentes",
"cinturon"=>"Cinturón",
"radio"=>"Radio",
"extintor"=>"Extintor",
"llanta_repuesto"=>"Llanta Repuesto",
"llave_rueda"=>"Llave Rueda",
"linterna"=>"Linterna",
"gato"=>"Gato",
"aire_forzado"=>"Aire Forzado",
"aceite_motor"=>"Aceite Motor",
"refrigerante"=>"Refrigerante",
"aceite_direccion"=>"Aceite Dirección",
"alarma"=>"Alarma",
"cone_seguridad"=>"Cono Seguridad",
"suspension"=>"Suspensión",
"emblemas"=>"Emblemas"
];

foreach($items as $name => $label){
echo "<tr>
<td>$label</td>
<td><input type='radio' name='$name' value='B' checked></td>
<td><input type='radio' name='$name' value='R'></td>
<td><input type='radio' name='$name' value='M'></td>
</tr>";
}
?>
</table>

<!-- 🔹 LLANTAS -->
<h5 class="section-title">Llantas</h5>

<div class="row">
<div class="col">
<input type="text" name="marca_llanta_del_izq" placeholder="Del Izq Marca" class="form-control">
<input type="number" name="presion_llanta_del_izq" placeholder="Presión" class="form-control mt-1">
</div>

<div class="col">
<input type="text" name="marca_llanta_del_der" placeholder="Del Der Marca" class="form-control">
<input type="number" name="presion_llanta_del_der" placeholder="Presión" class="form-control mt-1">
</div>
</div>

<br>

<div class="row">
<div class="col">
<input type="text" name="marca_llanta_post_izq_int" placeholder="Post Izq Int" class="form-control">
<input type="number" name="presion_llanta_post_izq_int" class="form-control mt-1">
</div>

<div class="col">
<input type="text" name="marca_llanta_post_izq_ext" placeholder="Post Izq Ext" class="form-control">
<input type="number" name="presion_llanta_post_izq_ext" class="form-control mt-1">
</div>

<div class="col">
<input type="text" name="marca_llanta_post_der_int" placeholder="Post Der Int" class="form-control">
<input type="number" name="presion_llanta_post_der_int" class="form-control mt-1">
</div>

<div class="col">
<input type="text" name="marca_llanta_post_der_ext" placeholder="Post Der Ext" class="form-control">
<input type="number" name="presion_llanta_post_der_ext" class="form-control mt-1">
</div>
</div>

<!-- 🔹 FINAL -->
<h5 class="section-title">Final</h5>

<textarea name="observaciones" placeholder="Observaciones" class="form-control"></textarea>

<br>

<div class="row">
<div class="col">
<input type="text" id="revisado_por" name="revisado_por" placeholder="Responsable" class="form-control">
</div>

<div class="col">
<input type="text" id="dni_revisor" name="dni_revisor" placeholder="DNI" class="form-control">
</div>

<div class="col">
<select name="estado_general" class="form-control">
<option value="BUENO">BUENO</option>
<option value="REGULAR">REGULAR</option>
<option value="MALO">MALO</option>
</select>
</div>
</div>

<br>

<button class="btn btn-success">✅Guardar</button>
<a href="inspecciones.php" class="btn btn-danger"
   onclick="return confirm('¿Seguro que deseas cancelar? Se perderán los datos no guardados');">
   ❌Cancelar
</a>
</form>

</div>
</div>
</div>

<!-- 🔥 SCRIPT DNI -->
<script>
document.getElementById("dni_revisor").addEventListener("keyup", function(){

    let dni = this.value;

    if(dni.length >= 8){
        fetch("buscar_conductor.php?dni=" + dni)
        .then(res => res.text())
        .then(data => {
            document.getElementById("revisado_por").value = data;
        });
    } else {
        document.getElementById("revisado_por").value = "";
    }

});
</script>

</body>
</html>