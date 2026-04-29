<?php
include("../../includes/seguridad.php"); // 🔒 PROTECCIÓN
include("../../config/conexion.php");

// 🔍 BUSCADOR
$buscar = $_GET['buscar'] ?? '';
$fecha_buscar = $_GET['fecha'] ?? '';

// 🔥 DASHBOARD
$total = pg_fetch_result(pg_query($conexion, "SELECT COUNT(*) FROM inspecciones_vehiculo"), 0, 0);

$soat_vencidos = pg_fetch_result(pg_query($conexion, "
    SELECT COUNT(*) FROM inspecciones_vehiculo 
    WHERE soat < CURRENT_DATE
"), 0, 0);

$rev_vencidos = pg_fetch_result(pg_query($conexion, "
    SELECT COUNT(*) FROM inspecciones_vehiculo 
    WHERE revision_tecnica < CURRENT_DATE
"), 0, 0);

$hoy_total = pg_fetch_result(pg_query($conexion, "
    SELECT COUNT(*) FROM inspecciones_vehiculo 
    WHERE fecha_inspeccion = CURRENT_DATE
"), 0, 0);

// 🔥 CONSULTA
$sql = "
SELECT 
    i.id_inspeccion,
    v.placa,
    c.nombre AS conductor,
    i.fecha_inspeccion,
    i.hora_salida,
    i.hora_llegada,
    i.km_salida,
    i.km_llegada,
    i.pax,
    i.observaciones,
    i.soat,
    i.revision_tecnica

FROM inspecciones_vehiculo i
JOIN vehiculos v ON v.id_vehiculo = i.id_vehiculo
JOIN conductores c ON c.id_conductor = i.id_conductor
WHERE 1=1
";

// 🔍 FILTROS
if($buscar != ''){
    $sql .= " AND (v.placa ILIKE '%$buscar%' OR c.nombre ILIKE '%$buscar%')";
}

if($fecha_buscar != ''){
    $sql .= " AND i.fecha_inspeccion = '$fecha_buscar'";
}

$sql .= " ORDER BY i.fecha_inspeccion DESC, i.hora_salida DESC";

$result = pg_query($conexion, $sql);

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lista de Inspecciones</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- ✅ CSS CORRECTO -->
<link rel="stylesheet" href="../../assets/css/estilos.css">

<style>
body { background:#f8f9fa; }
.header {
    background: linear-gradient(135deg,#c8102e,#a00d24);
    color:white; padding:20px; text-align:center;
}
</style>
</head>

<body>

<div class="header">
    <h2>🚗 Lista de Inspecciones</h2>
</div>

<div class="container mt-4">

<!-- 🔥 DASHBOARD -->
<div class="row mb-4 text-center">

<div class="col-md-3">
<div class="card shadow bg-primary text-white">
<div class="card-body">
<h6>Total</h6>
<h3><?= $total ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow bg-danger text-white">
<div class="card-body">
<h6>SOAT Vencido</h6>
<h3><?= $soat_vencidos ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow bg-warning text-dark">
<div class="card-body">
<h6>Revisión Vencida</h6>
<h3><?= $rev_vencidos ?></h3>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow bg-success text-white">
<div class="card-body">
<h6>Hoy</h6>
<h3><?= $hoy_total ?></h3>
</div>
</div>
</div>

</div>

<!-- 🔥 BOTONES -->
<div class="d-flex justify-content-between mb-3">

<a href="../../index.php" class="btn btn-secondary">
<i class="bi bi-arrow-left"></i> Volver
</a>

<a href="inspecciones_registro.php" class="btn btn-success">
<i class="bi bi-plus-circle"></i> Nueva Inspección
</a>

</div>

<!-- 🔍 BUSCADOR -->
<form method="GET" class="row g-2 mb-3">

<div class="col-md-4">
<input type="text" name="buscar" value="<?= $buscar ?>" class="form-control" placeholder="Buscar placa o conductor">
</div>

<div class="col-md-3">
<input type="date" name="fecha" value="<?= $fecha_buscar ?>" class="form-control">
</div>

<div class="col-md-3">
<button class="btn btn-primary w-100">🔍 Buscar</button>
</div>

<div class="col-md-2">
<a href="inspecciones.php" class="btn btn-secondary w-100">Limpiar</a>
</div>

</form>

<div class="card">
<div class="card-body p-0">

<?php if (pg_num_rows($result) > 0): ?>

<table class="table table-hover text-center">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Vehículo</th>
<th>Conductor</th>
<th>Fecha</th>
<th>SOAT</th>
<th>Revisión</th>
<th>KM Salida</th>
<th>KM Llegada</th>
<th>Pax</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>
<?php while($row = pg_fetch_assoc($result)): 

$hoy = date("Y-m-d");

$soat_color = ($row['soat'] >= $hoy) ? "success" : "danger";
$rev_color = ($row['revision_tecnica'] >= $hoy) ? "success" : "danger";

?>

<tr>

<td><?= $row['id_inspeccion'] ?></td>
<td><b><?= $row['placa'] ?></b></td>
<td><?= $row['conductor'] ?></td>
<td><?= $row['fecha_inspeccion'] ?></td>

<td><span class="badge bg-<?= $soat_color ?>"><?= $row['soat'] ?></span></td>
<td><span class="badge bg-<?= $rev_color ?>"><?= $row['revision_tecnica'] ?></span></td>

<td><?= number_format($row['km_salida'], 0, ',', '.') ?></td>
<td><?= number_format($row['km_llegada'] ?? 0, 0, ',', '.') ?></td>
<td><?= $row['pax'] ?? '-' ?></td>

<td>
<div class="d-flex gap-2 justify-content-center">

<a href="ver_inspeccion.php?id=<?= $row['id_inspeccion'] ?>" class="btn btn-primary btn-sm">
<i class="bi bi-eye"></i>
</a>

<a href="editar_inspeccion.php?id=<?= $row['id_inspeccion'] ?>" class="btn btn-warning btn-sm">
<i class="bi bi-pencil"></i>
</a>

<a href="eliminar_inspeccion.php?id=<?= $row['id_inspeccion'] ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('¿Eliminar esta inspección?');">
<i class="bi bi-trash"></i>
</a>

</div>
</td>

</tr>

<?php endwhile; ?>
</tbody>
</table>

<?php else: ?>

<div class="text-center p-5">
<p>No hay resultados</p>
<a href="inspecciones.php" class="btn btn-secondary">Limpiar filtros</a>
</div>

<?php endif; ?>

</div>
</div>

</div>

</body>
</html>