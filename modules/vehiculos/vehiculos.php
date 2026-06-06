<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$buscar = $_GET['buscar'] ?? '';

// Consulta principal con fecha de SOAT (corregida)
$sql = "SELECT id_vehiculo, code, placa, marca, modelo, estado, soat,
               llanta_repuesto, aceite_motor, refrigerante, aceite_direccion,
               soat_fecha_vencimiento,
               CASE 
                   WHEN soat_fecha_vencimiento IS NOT NULL THEN 
                       EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE))
                   ELSE NULL 
               END as dias_restantes
        FROM vehiculos";
$params = [];
if ($buscar) {
    $sql .= " WHERE code ILIKE $1 OR placa ILIKE $1 OR marca ILIKE $1 OR modelo ILIKE $1";
    $params[] = '%'.$buscar.'%';
}
$sql .= " ORDER BY 
            CASE 
                WHEN soat_fecha_vencimiento IS NULL THEN 6
                WHEN soat_fecha_vencimiento < CURRENT_DATE THEN 1
                WHEN EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE)) <= 7 THEN 2
                WHEN EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE)) <= 15 THEN 3
                WHEN EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE)) <= 30 THEN 4
                ELSE 5
            END,
            soat_fecha_vencimiento ASC NULLS LAST";
$result = pg_query_params($conexion, $sql, $params);

// Verificar si la consulta falló
if (!$result) {
    echo "Error en consulta principal: " . pg_last_error($conexion);
    exit();
}

// Estadísticas normales
$stats_query = pg_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado='Activo' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estado='Mantenimiento' THEN 1 ELSE 0 END) as mantenimiento,
    SUM(CASE WHEN estado='Inactivo' THEN 1 ELSE 0 END) as inactivos
    FROM vehiculos");

if (!$stats_query) {
    echo "Error en estadísticas: " . pg_last_error($conexion);
    exit();
}
$stats = pg_fetch_assoc($stats_query);

// Estadísticas de SOAT para alertas (corregida)
$soat_sql = "SELECT 
    COUNT(*) as total_con_soat,
    COUNT(CASE WHEN soat_fecha_vencimiento < CURRENT_DATE THEN 1 END) as vencidos,
    COUNT(CASE WHEN soat_fecha_vencimiento IS NOT NULL AND 
                    EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE)) BETWEEN 1 AND 7 THEN 1 END) as vence_7_dias,
    COUNT(CASE WHEN soat_fecha_vencimiento IS NOT NULL AND 
                    EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE)) BETWEEN 8 AND 15 THEN 1 END) as vence_15_dias,
    COUNT(CASE WHEN soat_fecha_vencimiento IS NOT NULL AND 
                    EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE)) BETWEEN 16 AND 30 THEN 1 END) as vence_30_dias,
    MIN(CASE WHEN soat_fecha_vencimiento > CURRENT_DATE THEN soat_fecha_vencimiento END) as proximo_vencimiento
    FROM vehiculos 
    WHERE soat_fecha_vencimiento IS NOT NULL";

$soat_stats_query = pg_query($conexion, $soat_sql);

if (!$soat_stats_query) {
    echo "Error en estadísticas SOAT: " . pg_last_error($conexion);
    exit();
}
$soat_stats = pg_fetch_assoc($soat_stats_query);

// Si no hay resultados, inicializar con ceros
if (!$soat_stats) {
    $soat_stats = [
        'total_con_soat' => 0,
        'vencidos' => 0,
        'vence_7_dias' => 0,
        'vence_15_dias' => 0,
        'vence_30_dias' => 0,
        'proximo_vencimiento' => null
    ];
}

function truncar($t, $l=25) {
    return empty($t) ? '—' : (strlen($t)<=$l ? htmlspecialchars($t) : htmlspecialchars(substr($t,0,$l)).'...');
}

function getBadgeSOAT($fecha, $dias) {
    if(!$fecha) {
        return '<span class="text-muted">—</span>';
    }
    
    $fecha_formateada = date('d/m/Y', strtotime($fecha));
    
    if($dias < 0) {
        return '<span class="badge-soat-vencido" title="VENCIDO desde: ' . $fecha_formateada . '">
                    <i class="bi bi-x-octagon-fill"></i> VENCIDO
                </span>';
    } elseif($dias <= 7) {
        return '<span class="badge-soat-urgente" title="Vence en ' . $dias . ' días - ' . $fecha_formateada . '">
                    <i class="bi bi-exclamation-triangle-fill"></i> Vence en ' . $dias . ' días
                </span>';
    } elseif($dias <= 15) {
        return '<span class="badge-soat-proximo" title="Vence en ' . $dias . ' días - ' . $fecha_formateada . '">
                    <i class="bi bi-clock-fill"></i> Vence en ' . $dias . ' días
                </span>';
    } elseif($dias <= 30) {
        return '<span class="badge-soat-aviso" title="Vence en ' . $dias . ' días - ' . $fecha_formateada . '">
                    <i class="bi bi-bell-fill"></i> ' . $dias . ' días
                </span>';
    } else {
        return '<span class="badge-soat-vigente" title="Vence: ' . $fecha_formateada . '">
                    <i class="bi bi-shield-check-fill"></i> Vigente
                </span>';
    }
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

<!-- Estadísticas normales -->
<div class="stats-row">
    <div class="stat-card total"><div class="stat-icon">🚛</div><div class="stat-number"><?= $stats['total'] ?></div><div class="stat-label">Total</div></div>
    <div class="stat-card activo"><div class="stat-icon">✅</div><div class="stat-number"><?= $stats['activos'] ?></div><div class="stat-label">Activos</div></div>
    <div class="stat-card mantenimiento"><div class="stat-icon">🔧</div><div class="stat-number"><?= $stats['mantenimiento'] ?></div><div class="stat-label">Mantenimiento</div></div>
    <div class="stat-card inactivo"><div class="stat-icon">⛔</div><div class="stat-number"><?= $stats['inactivos'] ?></div><div class="stat-label">Inactivos</div></div>
</div>

<!-- ALERTA DE SOAT -->
<?php if(($soat_stats['vencidos'] ?? 0) > 0 || ($soat_stats['vence_7_dias'] ?? 0) > 0 || ($soat_stats['vence_15_dias'] ?? 0) > 0): ?>
<div class="alert alert-warning alert-dismissible fade show mt-3 shadow" role="alert" style="border-left: 5px solid #ff0000;">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem; color: #ff6b00;"></i>
        </div>
        <div class="flex-grow-1">
            <strong><i class="bi bi-calendar-exclamation"></i> ALERTA DE VENCIMIENTO DE SOAT</strong><br>
            <?php if(($soat_stats['vencidos'] ?? 0) > 0): ?>
                <span class="badge bg-danger me-2">🔴 <?= $soat_stats['vencidos'] ?> VENCIDOS</span>
            <?php endif; ?>
            <?php if(($soat_stats['vence_7_dias'] ?? 0) > 0): ?>
                <span class="badge bg-warning text-dark me-2">🟡 <?= $soat_stats['vence_7_dias'] ?> vencen en ≤7 días</span>
            <?php endif; ?>
            <?php if(($soat_stats['vence_15_dias'] ?? 0) > 0): ?>
                <span class="badge bg-info me-2">🟠 <?= $soat_stats['vence_15_dias'] ?> vencen en ≤15 días</span>
            <?php endif; ?>
            <?php if(($soat_stats['vence_30_dias'] ?? 0) > 0): ?>
                <span class="badge bg-secondary">🟡 <?= $soat_stats['vence_30_dias'] ?> vencen en ≤30 días</span>
            <?php endif; ?>
            <?php if(!empty($soat_stats['proximo_vencimiento'])): ?>
                <br><small class="mt-1 d-block">
                    <i class="bi bi-calendar"></i> Próximo vencimiento: 
                    <strong><?= date('d/m/Y', strtotime($soat_stats['proximo_vencimiento'])) ?></strong>
                </small>
            <?php endif; ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

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
<thead>
    <tr><th>ID</th><th>Código</th><th>Placa</th><th>Marca</th><th>Modelo</th><th>Llanta R.</th><th>Aceite Motor</th><th>Refrigerante</th><th>Aceite Dir.</th><th>Estado</th><th>SOAT</th><th>Estado SOAT</th><th>Acciones</th></tr>
</thead>
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
    <td><?= getBadgeSOAT($r['soat_fecha_vencimiento'], $r['dias_restantes']) ?></td>
    <td>
        <div class="d-flex gap-1 justify-content-center">
            <a href="ver_vehiculo.php?id=<?= $r['id_vehiculo'] ?>" class="btn btn-action btn-view" title="Ver"><i class="bi bi-eye-fill"></i></a>
            <a href="editar_vehi.php?id=<?= $r['id_vehiculo'] ?>" class="btn btn-action btn-edit" title="Editar"><i class="bi bi-pencil-fill"></i></a>
            <a href="eliminar_vehiculos.php?id=<?= $r['id_vehiculo'] ?>" class="btn btn-action btn-delete" onclick="return confirm('¿Eliminar este vehículo?')" title="Eliminar"><i class="bi bi-trash3-fill"></i></a>
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

<style>
/* Estilos para badges de SOAT */
.badge-soat-vigente {
    background-color: #d4edda;
    color: #155724;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-block;
}

.badge-soat-aviso {
    background-color: #fff3cd;
    color: #856404;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-block;
}

.badge-soat-proximo {
    background-color: #ffe5b4;
    color: #cc7000;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-block;
    animation: pulse 1s infinite;
}

.badge-soat-urgente {
    background-color: #ffcccc;
    color: #cc0000;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    display: inline-block;
    animation: pulse 0.5s infinite;
}

.badge-soat-vencido {
    background-color: #dc3545;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    display: inline-block;
    animation: blink 1s infinite;
}

@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(0.98); }
    100% { opacity: 1; transform: scale(1); }
}

@keyframes blink {
    0% { background-color: #dc3545; }
    50% { background-color: #ff6b6b; }
    100% { background-color: #dc3545; }
}
</style>

<?php include("../../includes/footer.php"); ?>