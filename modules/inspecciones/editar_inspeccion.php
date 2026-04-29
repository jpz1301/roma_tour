<?php 
include("../../config/conexion.php");

$id = (int) $_GET['id'];

$sql = "SELECT * FROM inspecciones_vehiculo WHERE id_inspeccion = $id";
$result = pg_query($conexion, $sql);
$d = pg_fetch_assoc($result);

// función segura
function v($campo){
    return $_POST[$campo] ?? null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "UPDATE inspecciones_vehiculo SET

    km_salida='{$_POST['km_salida']}',
    km_llegada='{$_POST['km_llegada']}',
    pax='{$_POST['pax']}',
    observaciones='{$_POST['observaciones']}',

    -- 🔥 DOCUMENTOS
    soat='{$_POST['soat']}',
    revision_tecnica='{$_POST['revision_tecnica']}',

    -- 🔥 CHECKLIST COMPLETO
    espejo_derecho='".v('espejo_derecho')."',
    espejo_izquierdo='".v('espejo_izquierdo')."',
    claxon='".v('claxon')."',
    antena='".v('antena')."',

    parabrisas_frontal='".v('parabrisas_frontal')."',
    parabrisas_posterior='".v('parabrisas_posterior')."',

    tapa_combustible='".v('tapa_combustible')."',
    tapa_aceite_motor='".v('tapa_aceite_motor')."',
    tapa_radiator='".v('tapa_radiator')."',

    luces_altas='".v('luces_altas')."',
    luces_bajas='".v('luces_bajas')."',
    luces_traseras='".v('luces_traseras')."',
    luces_freno='".v('luces_freno')."',
    luces_intermitentes='".v('luces_intermitentes')."',

    cinturon='".v('cinturon')."',
    radio='".v('radio')."',
    extintor='".v('extintor')."',
    llanta_repuesto='".v('llanta_repuesto')."',
    llave_rueda='".v('llave_rueda')."',
    linterna='".v('linterna')."',
    gato='".v('gato')."',
    aire_forzado='".v('aire_forzado')."',

    aceite_motor='".v('aceite_motor')."',
    refrigerante='".v('refrigerante')."',
    aceite_direccion='".v('aceite_direccion')."',
    alarma='".v('alarma')."',
    cone_seguridad='".v('cone_seguridad')."',
    suspension='".v('suspension')."',
    emblemas='".v('emblemas')."',

    -- 🔥 LLANTAS
    marca_llanta_del_izq='{$_POST['marca_llanta_del_izq']}',
    presion_llanta_del_izq='{$_POST['presion_llanta_del_izq']}',

    marca_llanta_del_der='{$_POST['marca_llanta_del_der']}',
    presion_llanta_del_der='{$_POST['presion_llanta_del_der']}',

    marca_llanta_post_izq_int='{$_POST['marca_llanta_post_izq_int']}',
    presion_llanta_post_izq_int='{$_POST['presion_llanta_post_izq_int']}',

    marca_llanta_post_izq_ext='{$_POST['marca_llanta_post_izq_ext']}',
    presion_llanta_post_izq_ext='{$_POST['presion_llanta_post_izq_ext']}',

    marca_llanta_post_der_int='{$_POST['marca_llanta_post_der_int']}',
    presion_llanta_post_der_int='{$_POST['presion_llanta_post_der_int']}',

    marca_llanta_post_der_ext='{$_POST['marca_llanta_post_der_ext']}',
    presion_llanta_post_der_ext='{$_POST['presion_llanta_post_der_ext']}'

    WHERE id_inspeccion=$id";

    pg_query($conexion, $sql);

    header("Location: inspecciones.php");
    exit();
}

// función radio
function radio($valor, $actual){
    return ($valor == $actual) ? "checked" : "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Inspección</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.section-title{
    background:#f1f1f1;
    padding:8px;
    border-left:5px solid #ffc107;
    margin-top:20px;
}
</style>

</head>

<body class="bg-light">

<div class="container mt-4">
<div class="card shadow">

<div class="card-header bg-warning">
<h4>Editar Inspección</h4>
</div>

<div class="card-body">

<form method="POST">

<!-- 🔥 DOCUMENTOS -->
<h5 class="section-title">Documentación</h5>

<div class="row">
<div class="col">
<label>SOAT</label>
<input type="date" name="soat" value="<?= $d['soat'] ?? '' ?>" class="form-control">
</div>

<div class="col">
<label>Revisión Técnica</label>
<input type="date" name="revision_tecnica" value="<?= $d['revision_tecnica'] ?? '' ?>" class="form-control">
</div>
</div>
<!-- 🔹 RUTA -->
<h5 class="section-title">Ruta / Servicio</h5>

<div class="row">
<div class="col">
<input type="text" name="ruta" value="<?= $d['ruta'] ?? '' ?>" placeholder="Ruta" class="form-control">
</div>

<div class="col">
<input type="text" name="tipo_servicio" value="<?= $d['tipo_servicio'] ?? '' ?>" placeholder="Tipo de servicio" class="form-control">
</div>
</div>

<!-- 🔹 CONTROL DE RUTA -->
<h5 class="section-title">Control de Ruta</h5>

<div class="row">
<div class="col">
<label>Hora salida</label>
<input type="time" name="hora_salida" value="<?= $d['hora_salida'] ?? '' ?>" class="form-control">
</div>

<div class="col">
<label>Hora llegada</label>
<input type="time" name="hora_llegada" value="<?= $d['hora_llegada'] ?? '' ?>" class="form-control">
</div>

<div class="col">
<label>KM salida</label>
<input type="number" name="km_salida" value="<?= $d['km_salida'] ?? '' ?>" class="form-control">
</div>

<div class="col">
<label>KM llegada</label>
<input type="number" name="km_llegada" value="<?= $d['km_llegada'] ?? '' ?>" class="form-control">
</div>
</div>

<br>

<div class="row">
<div class="col">
<label>Combustible salida</label>
<input type="number" name="combustible_salida" value="<?= $d['combustible_salida'] ?? '' ?>" class="form-control">
</div>

<div class="col">
<label>Combustible llegada</label>
<input type="number" name="combustible_llegada" value="<?= $d['combustible_llegada'] ?? '' ?>" class="form-control">
</div>

<div class="col">
<label>PAX</label>
<input type="number" name="pax" value="<?= $d['pax'] ?? '' ?>" class="form-control">
</div>
</div>


<!-- 🔥 CHECKLIST COMPLETO -->
<h5 class="section-title">Checklist Completo</h5>

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

foreach($items as $campo => $nombre){
echo "<tr>
<td>$nombre</td>
<td><input type='radio' name='$campo' value='B' ".radio('B',$d[$campo])."></td>
<td><input type='radio' name='$campo' value='R' ".radio('R',$d[$campo])."></td>
<td><input type='radio' name='$campo' value='M' ".radio('M',$d[$campo])."></td>
</tr>";
}
?>
</table>
<!-- 🔹 LLANTAS -->
<h5 class="section-title">Llantas</h5>

<div class="row">
<div class="col">
<label>Delantera Izquierda</label>
<input type="text" name="marca_llanta_del_izq" value="<?= $d['marca_llanta_del_izq'] ?? '' ?>" class="form-control">
<input type="number" name="presion_llanta_del_izq" value="<?= $d['presion_llanta_del_izq'] ?? '' ?>" class="form-control mt-1">
</div>

<div class="col">
<label>Delantera Derecha</label>
<input type="text" name="marca_llanta_del_der" value="<?= $d['marca_llanta_del_der'] ?? '' ?>" class="form-control">
<input type="number" name="presion_llanta_del_der" value="<?= $d['presion_llanta_del_der'] ?? '' ?>" class="form-control mt-1">
</div>
</div>

<br>

<h6>Trasera Izquierda</h6>
<div class="row">
<div class="col">
<label>Interna</label>
<input type="text" name="marca_llanta_post_izq_int" value="<?= $d['marca_llanta_post_izq_int'] ?? '' ?>" class="form-control">
<input type="number" name="presion_llanta_post_izq_int" value="<?= $d['presion_llanta_post_izq_int'] ?? '' ?>" class="form-control mt-1">
</div>

<div class="col">
<label>Externa</label>
<input type="text" name="marca_llanta_post_izq_ext" value="<?= $d['marca_llanta_post_izq_ext'] ?? '' ?>" class="form-control">
<input type="number" name="presion_llanta_post_izq_ext" value="<?= $d['presion_llanta_post_izq_ext'] ?? '' ?>" class="form-control mt-1">
</div>
</div>

<br>

<h6>Trasera Derecha</h6>
<div class="row">
<div class="col">
<label>Interna</label>
<input type="text" name="marca_llanta_post_der_int" value="<?= $d['marca_llanta_post_der_int'] ?? '' ?>" class="form-control">
<input type="number" name="presion_llanta_post_der_int" value="<?= $d['presion_llanta_post_der_int'] ?? '' ?>" class="form-control mt-1">
</div>

<div class="col">
<label>Externa</label>
<input type="text" name="marca_llanta_post_der_ext" value="<?= $d['marca_llanta_post_der_ext'] ?? '' ?>" class="form-control">
<input type="number" name="presion_llanta_post_der_ext" value="<?= $d['presion_llanta_post_der_ext'] ?? '' ?>" class="form-control mt-1">
</div>
</div>
<!-- 🔹 OBS -->
<h5 class="section-title">Observaciones</h5>
<textarea name="observaciones" class="form-control"><?= $d['observaciones'] ?></textarea>

<br>

<button class="btn btn-success">Actualizar</button>
<a href="inspecciones.php" class="btn btn-secondary">Cancelar</a>

</form>

</div>
</div>
</div>

</body>
</html>