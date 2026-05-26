<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$buscar = $_GET['buscar'] ?? '';

$sql = "SELECT id_vehiculo, code, placa, marca, modelo, estado, soat,
               llanta_repuesto, aceite_motor, refrigerante, aceite_direccion
        FROM vehiculos";
$params = [];
if ($buscar) {
    $sql .= " WHERE code ILIKE $1 OR placa ILIKE $1 OR marca ILIKE $1 OR modelo ILIKE $1";
    $params[] = '%'.$buscar.'%';
}
$sql .= " ORDER BY id_vehiculo ASC";
$result = pg_query_params($conexion, $sql, $params);

$stats = pg_fetch_assoc(pg_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado='Activo' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estado='Mantenimiento' THEN 1 ELSE 0 END) as mantenimiento,
    SUM(CASE WHEN estado='Inactivo' THEN 1 ELSE 0 END) as inactivos
    FROM vehiculos"));

function truncar($t, $l=25) {
    return empty($t) ? '—' : (strlen($t)<=$l ? htmlspecialchars($t) : htmlspecialchars(substr($t,0,$l)).'...');
}

// Configurar includes
$titulo = 'Vehículos | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Gestión de Vehículos';
$btn_nuevo_url = 'registrar_vehi.php';
$btn_nuevo_texto = 'Nuevo Vehículo';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + NUEVO -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="../../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Menú
        </a>
        <a href="registrar_vehi.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Nuevo Vehículo
        </a>
    </div>
</div>

<div class="container mb-5">

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card total"><div class="stat-icon">🚛</div><div class="stat-number"><?= $stats['total'] ?></div><div class="stat-label">Total</div></div>
    <div class="stat-card activo"><div class="stat-icon">✅</div><div class="stat-number"><?= $stats['activos'] ?></div><div class="stat-label">Activos</div></div>
    <div class="stat-card mantenimiento"><div class="stat-icon">🔧</div><div class="stat-number"><?= $stats['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
    <div class="stat-card inactivo"><div class="stat-icon">⛔</div><div class="stat-number"><?= $stats['inactivos'] ?></div><div class="stat-label">Inactivos</div></div>
</div>

<!-- Buscador -->
<form method="GET" class="search-box">
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por código, placa, marca o modelo..." value="<?= htmlspecialchars($buscar) ?>">
        <button class="btn btn-search"><i class="bi bi-search"></i> Buscar</button>
        <?php if($buscar): ?><a href="vehiculos.php" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a><?php endif; ?>
    </div>
</form>

<!-- Tabla -->
<div class="main-card">
<div class="card-header"><h4><i class="bi bi-list-ul"></i> Listado de Vehículos</h4></div>
<div class="card-body">

<?php if(pg_num_rows($result) > 0): ?>
<div class="table-responsive">
<table id="tabla" class="table table-hover">
<thead><tr><th>ID</th><th>Código</th><th>Placa</th><th>Marca</th><th>Modelo</th><th>Llanta R.</th><th>Aceite Motor</th><th>Refrigerante</th><th>Aceite Dir.</th><th>Estado</th><th>SOAT</th><th>Acciones</th></tr></thead>
<tbody>
<?php while($r = pg_fetch_assoc($result)):
$e = $r['estado'];
$estados = ['Activo'=>'badge-activo','Mantenimiento'=>'badge-mantenimiento','Inactivo'=>'badge-inactivo'];
$iconos = ['Activo'=>'bi-check-circle-fill','Mantenimiento'=>'bi-tools','Inactivo'=>'bi-x-circle-fill'];
?>
<tr>
<td><span class="badge bg-dark">#<?= $r['id_vehiculo'] ?></span></td>
<td><?= $r['code'] ? "<span class='code-badge'>".htmlspecialchars($r['code'])."</span>" : '—' ?></td>
<td><span class="placa-badge"><i class="bi bi-truck-front"></i> <?= htmlspecialchars($r['placa']) ?></span></td>
<td><?= htmlspecialchars($r['marca'] ?? '—') ?></td>
<td><?= htmlspecialchars($r['modelo'] ?? '—') ?></td>
<td><?= $r['llanta_repuesto'] ? "<span class='repuesto-badge' title='".htmlspecialchars($r['llanta_repuesto'])."'>".truncar($r['llanta_repuesto'])."</span>" : '<span class="text-muted">—</span>' ?></td>
<td><?= $r['aceite_motor'] ? "<span class='repuesto-badge' title='".htmlspecialchars($r['aceite_motor'])."'>".truncar($r['aceite_motor'])."</span>" : '<span class="text-muted">—</span>' ?></td>
<td><?= $r['refrigerante'] ? "<span class='repuesto-badge' title='".htmlspecialchars($r['refrigerante'])."'>".truncar($r['refrigerante'])."</span>" : '<span class="text-muted">—</span>' ?></td>
<td><?= $r['aceite_direccion'] ? "<span class='repuesto-badge' title='".htmlspecialchars($r['aceite_direccion'])."'>".truncar($r['aceite_direccion'])."</span>" : '<span class="text-muted">—</span>' ?></td>
<td><span class="badge-estado <?= $estados[$e] ?>"><i class="bi <?= $iconos[$e] ?>"></i> <?= $e ?></span></td>
<td><?= $r['soat'] ? "<span class='soat-badge' title='".htmlspecialchars($r['soat'])."'>".truncar($r['soat'])."</span>" : '<span class="text-muted">—</span>' ?></td>
<td>
<div class="d-flex gap-1 justify-content-center">
<a href="ver_vehiculo.php?id=<?= $r['id_vehiculo'] ?>" class="btn btn-action btn-view" title="Ver"><i class="bi bi-eye-fill"></i></a>
<a href="editar_vehi.php?id=<?= $r['id_vehiculo'] ?>" class="btn btn-action btn-edit" title="Editar"><i class="bi bi-pencil-fill"></i></a>
<a href="eliminar_vehiculos.php?id=<?= $r['id_vehiculo'] ?>" class="btn btn-action btn-delete" onclick="return confirm('¿Eliminar?')" title="Eliminar"><i class="bi bi-trash3-fill"></i></a>
</div>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="text-center py-5"><i class="bi bi-inbox" style="font-size:5rem;color:#ccc;"></i><h4 class="text-muted mt-3">No se encontraron vehículos</h4></div>
<?php endif; ?>

</div></div>
</div>

<?php include("../../includes/footer.php"); ?>