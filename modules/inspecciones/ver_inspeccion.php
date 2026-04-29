<?php
include("../../config/conexion.php");


$id = (int) $_GET['id'];

$sql = "SELECT i.*, v.placa, v.marca, v.modelo, c.nombre 
        FROM inspecciones_vehiculo i
        JOIN vehiculos v ON v.id_vehiculo = i.id_vehiculo
        JOIN conductores c ON c.id_conductor = i.id_conductor
        WHERE i.id_inspeccion = $id";

$result = pg_query($conexion, $sql);
$d = pg_fetch_assoc($result);

function estadoColor($valor){
    if($valor == 'B') return "<span class='badge bg-success'>Bueno</span>";
    if($valor == 'R') return "<span class='badge bg-warning'>Regular</span>";
    if($valor == 'M') return "<span class='badge bg-danger'>Malo</span>";
    return "-";
}

$hoy = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle de Inspección</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.section-title{
    background:#f1f1f1;
    padding:8px;
    border-left:5px solid #0d6efd;
    margin-top:20px;
}
</style>

</head>

<body class="bg-light">

<div class="container mt-4">
<div class="card shadow">

<div class="card-header bg-primary text-white">
<h4>Detalle de Inspección</h4>
</div>

<div class="card-body">

<!-- 🔹 DATOS -->
<h5 class="section-title">Datos Generales</h5>
<p><b>Vehículo:</b> <?= $d['placa'] ?> - <?= $d['marca'] ?> <?= $d['modelo'] ?></p>
<p><b>Conductor:</b> <?= $d['nombre'] ?></p>
<p><b>Fecha:</b> <?= $d['fecha_inspeccion'] ?></p>

<!-- 🔥 DOCUMENTOS -->
<h5 class="section-title">Documentación</h5>
<p>
SOAT: 
<span class="badge bg-<?= ($d['soat'] >= $hoy) ? 'success':'danger' ?>">
<?= $d['soat'] ?>
</span>
</p>

<p>
Revisión Técnica: 
<span class="badge bg-<?= ($d['revision_tecnica'] >= $hoy) ? 'success':'danger' ?>">
<?= $d['revision_tecnica'] ?>
</span>
</p>

<!-- 🔹 RUTA -->
<h5 class="section-title">Ruta / Servicio</h5>
<p><b>Ruta:</b> <?= $d['ruta'] ?? '-' ?></p>
<p><b>Tipo:</b> <?= $d['tipo_servicio'] ?? '-' ?></p>

<!-- 🔹 CONTROL -->
<h5 class="section-title">Control de Ruta</h5>
<p>
Salida: <?= $d['hora_salida'] ?? '-' ?> |
Llegada: <?= $d['hora_llegada'] ?? '-' ?>
</p>

<p>
KM: <?= $d['km_salida'] ?? 0 ?> → <?= $d['km_llegada'] ?? 0 ?> |
Recorrido: <?= ($d['km_llegada'] ?? 0) - ($d['km_salida'] ?? 0) ?> km
</p>

<p>
Combustible: <?= $d['combustible_salida'] ?? 0 ?> → <?= $d['combustible_llegada'] ?? 0 ?>
</p>

<p>
PAX: <?= $d['pax'] ?? 0 ?>
</p>

<!-- 🔥 CHECKLIST COMPLETO -->
<h5 class="section-title">Checklist Completo</h5>

<table class="table table-bordered text-center">
<tr class="table-dark">
<th>Item</th>
<th>Estado</th>
</tr>

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

foreach($items as $campo => $nombre){
echo "<tr>
<td>$nombre</td>
<td>".estadoColor($d[$campo] ?? null)."</td>
</tr>";
}
?>
</table>

<!-- 🔹 LLANTAS -->
<h5 class="section-title">Llantas</h5>

<table class="table table-bordered text-center">

<tr class="table-warning">
<th>Posición</th>
<th>Marca</th>
<th>Presión</th>
</tr>

<tr>
<td>Delantera Izquierda</td>
<td><?= $d['marca_llanta_del_izq'] ?? '-' ?></td>
<td><?= $d['presion_llanta_del_izq'] ?? '-' ?></td>
</tr>

<tr>
<td>Delantera Derecha</td>
<td><?= $d['marca_llanta_del_der'] ?? '-' ?></td>
<td><?= $d['presion_llanta_del_der'] ?? '-' ?></td>
</tr>

<tr class="table-light">
<td colspan="3"><b>Trasera Izquierda</b></td>
</tr>

<tr>
<td>Interna</td>
<td><?= $d['marca_llanta_post_izq_int'] ?? '-' ?></td>
<td><?= $d['presion_llanta_post_izq_int'] ?? '-' ?></td>
</tr>

<tr>
<td>Externa</td>
<td><?= $d['marca_llanta_post_izq_ext'] ?? '-' ?></td>
<td><?= $d['presion_llanta_post_izq_ext'] ?? '-' ?></td>
</tr>

<tr class="table-light">
<td colspan="3"><b>Trasera Derecha</b></td>
</tr>

<tr>
<td>Interna</td>
<td><?= $d['marca_llanta_post_der_int'] ?? '-' ?></td>
<td><?= $d['presion_llanta_post_der_int'] ?? '-' ?></td>
</tr>

<tr>
<td>Externa</td>
<td><?= $d['marca_llanta_post_der_ext'] ?? '-' ?></td>
<td><?= $d['presion_llanta_post_der_ext'] ?? '-' ?></td>
</tr>

</table>

<!-- 🔹 OBS -->
<h5 class="section-title">Observaciones</h5>
<p><?= $d['observaciones'] ?? '-' ?></p>

<!-- 🔹 RESPONSABLE -->
<h5 class="section-title">Responsable</h5>
<p><?= $d['revisado_por'] ?? '-' ?> (<?= $d['dni_revisor'] ?? '-' ?>)</p>

<a href="inspecciones.php" class="btn btn-secondary mt-3">Volver</a>

</div>
</div>
</div>

</body>
</html>