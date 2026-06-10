<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Consulta con ordenamiento forzado por fecha (más reciente primero)
$sql = "SELECT 
            m.id,
            m.fecha,
            v.placa AS vehiculo,
            m.responsable,
            m.mecanico_id AS mecanico,
            m.taller_id AS taller,
            m.tipo,
            m.problema,
            m.costo,
            m.observaciones
        FROM mantenimiento m
        JOIN vehiculos v ON m.vehiculo_id = v.id_vehiculo
        ORDER BY m.fecha::DATE DESC NULLS LAST";

$result = pg_query($conexion, $sql);

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conexion));
}

// Estadísticas
$stats = pg_fetch_assoc(pg_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN tipo = 'Preventivo' THEN 1 ELSE 0 END) as preventivos,
    SUM(CASE WHEN tipo = 'Correctivo' THEN 1 ELSE 0 END) as correctivos,
    COALESCE(SUM(costo), 0) as costo_total
    FROM mantenimiento"));

// Configurar includes
$titulo = 'Mantenimientos | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Mantenimientos de Flota';
$btn_nuevo_url = 'mantenimiento.php';
$btn_nuevo_texto = 'Nuevo Mantenimiento';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + NUEVO -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="../../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Menú
        </a>
        <a href="mantenimiento.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Nuevo Mantenimiento
        </a>
    </div>
</div>

<div class="container mb-5">

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card total">
        <div class="stat-icon">🔧</div>
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Total Mantenimientos</div>
    </div>
    <div class="stat-card preventivo">
        <div class="stat-icon">🛡️</div>
        <div class="stat-number"><?= $stats['preventivos'] ?></div>
        <div class="stat-label">Preventivos</div>
    </div>
    <div class="stat-card correctivo">
        <div class="stat-icon">⚠️</div>
        <div class="stat-number"><?= $stats['correctivos'] ?></div>
        <div class="stat-label">Correctivos</div>
    </div>
    <div class="stat-card costo">
        <div class="stat-icon">💰</div>
        <div class="stat-number">S/ <?= number_format($stats['costo_total'], 0) ?></div>
        <div class="stat-label">Costo Total</div>
    </div>
</div>

<!-- Tabla -->
<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
    <h4><i class="bi bi-clipboard-check"></i> Historial de Mantenimientos</h4>
</div>
<div class="card-body">

<?php if(pg_num_rows($result) > 0): ?>
<div class="table-responsive">
<table id="tabla" class="table table-hover">
<thead>
<tr>
    <th>Fecha</th>
    <th>Vehículo</th>
    <th>Responsable</th>
    <th>Mecánico</th>
    <th>Taller</th>
    <th>Tipo</th>
    <th>Problema</th>
    <th>Costo</th>
    <th>Acciones</th>
</tr>
</thead>
<tbody>

<?php 
// Array para almacenar y ordenar manualmente por si acaso
$datos = [];
while($row = pg_fetch_assoc($result)) {
    $datos[] = $row;
}

// Ordenamiento manual en PHP (doble seguridad)
usort($datos, function($a, $b) {
    $fecha_a = new DateTime($a['fecha']);
    $fecha_b = new DateTime($b['fecha']);
    return $fecha_b <=> $fecha_a; // DESC (más reciente primero)
});
?>

<?php foreach($datos as $row): 
    $problema = $row['problema'] ?? '—';
    $costo = $row['costo'] ?? 0;
    $fecha_obj = new DateTime($row['fecha']);
?>
<tr>
    <td>
        <span style="font-weight:600;"><?= $fecha_obj->format("d") ?></span>
        <span style="color:#888;font-size:0.8rem;"> <?= $fecha_obj->format("M Y") ?></span>
    </td>
    <td><span class="placa-badge"><i class="bi bi-truck-front"></i> <?= htmlspecialchars($row['vehiculo']) ?></span></td>
    <td><i class="bi bi-person" style="color:#666;"></i> <?= htmlspecialchars($row['responsable'] ?? '—') ?></td>
    <td><i class="bi bi-person-check" style="color:#666;"></i> <?= htmlspecialchars($row['mecanico'] ?? '—') ?></td>
    <td><i class="bi bi-building" style="color:#666;"></i> <?= htmlspecialchars($row['taller'] ?? '—') ?></td>
    <td>
        <?php if($row['tipo'] == 'Preventivo'): ?>
            <span class="badge-tipo badge-preventivo"><i class="bi bi-shield-check"></i> Preventivo</span>
        <?php else: ?>
            <span class="badge-tipo badge-correctivo"><i class="bi bi-exclamation-diamond"></i> Correctivo</span>
        <?php endif; ?>
    </td>
    <td>
        <span class="problema-text" title="<?= htmlspecialchars($problema) ?>"><?= htmlspecialchars($problema) ?></span>
    </td>
    <td><span class="costo-badge">S/ <?= number_format($costo, 2) ?></span></td>
    <td>
        <div class="d-flex gap-1">
            <a href="ver_mantenimiento.php?id=<?= $row['id'] ?>" class="btn btn-action btn-view" title="Ver">
                <i class="bi bi-eye-fill"></i>
            </a>
            <a href="editar_mantenimiento.php?id=<?= $row['id'] ?>" class="btn btn-action btn-edit" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
            <a href="eliminar_mantenimiento.php?id=<?= $row['id'] ?>" class="btn btn-action btn-delete"
               onclick="return confirm('¿Eliminar este mantenimiento?')" title="Eliminar">
                <i class="bi bi-trash3"></i>
            </a>
        </div>
    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

<?php else: ?>
<div class="text-center py-5">
    <i class="bi bi-tools" style="font-size:5rem;color:#ccc;"></i>
    <h4 class="text-muted mt-3">No se encontraron mantenimientos</h4>
    <a href="mantenimiento.php" class="btn btn-success mt-3">
        <i class="bi bi-plus-circle-fill"></i> Registrar Nuevo Mantenimiento
    </a>
</div>
<?php endif; ?>

</div></div>
</div>

<style>
.btn-view {
    background-color: #0dcaf0;
    color: #000;
}
.btn-view:hover {
    background-color: #0aa2c0;
    color: #fff;
}
.btn-action {
    padding: 0.3rem 0.6rem;
    border-radius: 8px;
    transition: all 0.2s;
}
.btn-edit {
    background-color: #ffc107;
    color: #000;
}
.btn-edit:hover {
    background-color: #e0a800;
    color: #000;
}
.btn-delete {
    background-color: #dc3545;
    color: #fff;
}
.btn-delete:hover {
    background-color: #bb2d3b;
    color: #fff;
}
</style>

<?php include("../../includes/footer.php"); ?>