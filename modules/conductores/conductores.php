<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$sql = "SELECT * FROM conductores ORDER BY nombre ASC";
$result = pg_query($conexion, $sql);

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conexion));
}

// Incidencias por conductor
$incidencias = [];
$resInc = pg_query($conexion, "
    SELECT conductor, COUNT(*) as total 
    FROM incidencias 
    GROUP BY conductor
");

while($inc = pg_fetch_assoc($resInc)){
    $nombre = strtolower(trim($inc['conductor']));
    $incidencias[$nombre] = $inc['total'];
}

// Estadísticas
$stats = pg_fetch_assoc(pg_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado='Activo' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estado='Inactivo' THEN 1 ELSE 0 END) as inactivos,
    SUM(CASE WHEN LOWER(tipo_contrato) LIKE '%temporal%' OR LOWER(tipo_contrato) = 'temporal' THEN 1 ELSE 0 END) as temporales,
    SUM(CASE WHEN LOWER(tipo_contrato) LIKE '%permanente%' OR LOWER(tipo_contrato) LIKE '%indefinido%' OR LOWER(tipo_contrato) LIKE '%plazo fijo%' OR tipo_contrato IS NULL OR tipo_contrato = '' THEN 1 ELSE 0 END) as permanentes
    FROM conductores"));

// Configurar includes
$titulo = 'Conductores | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Gestión de Conductores';
$btn_nuevo_url = 'registrar.php';
$btn_nuevo_texto = 'Nuevo Conductor';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + NUEVO -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="../../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Menú
        </a>
        <a href="registrar.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Nuevo Conductor
        </a>
    </div>
</div>

<div class="container mb-5">

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card total">
        <div class="stat-icon">👥</div>
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Total Conductores</div>
    </div>
    <div class="stat-card activo">
        <div class="stat-icon">✅</div>
        <div class="stat-number"><?= $stats['activos'] ?></div>
        <div class="stat-label">Activos</div>
    </div>
    <div class="stat-card inactivo">
        <div class="stat-icon">⛔</div>
        <div class="stat-number"><?= $stats['inactivos'] ?></div>
        <div class="stat-label">Inactivos</div>
    </div>
    <div class="stat-card temporales" style="border-bottom: 3px solid #ffc107;">
        <div class="stat-icon">🕐</div>
        <div class="stat-number" style="color:#b8860b;"><?= $stats['temporales'] ?></div>
        <div class="stat-label">Temporales</div>
    </div>
    <div class="stat-card permanentes" style="border-bottom: 3px solid #0d6efd;">
        <div class="stat-icon">🔒</div>
        <div class="stat-number" style="color:#0d6efd;"><?= $stats['permanentes'] ?></div>
        <div class="stat-label">Permanentes</div>
    </div>
</div>

<!-- Tabla -->
<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
    <h4><i class="bi bi-people-fill"></i> Listado de Conductores</h4>
</div>
<div class="card-body">

<?php if(pg_num_rows($result) > 0): ?>
<div class="table-responsive">
<table id="tabla" class="table table-hover">
<thead>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>DNI</th>
    <th>Teléfono</th>
    <th>Licencia</th>
    <th>Estado</th>
    <th>Ingreso</th>
    <th>Libres</th>
    <th>Salidas</th>
    <th>Dirección</th>
    <th>Emergencia</th>
    <th>Contrato</th>
    <th>Vacaciones</th>
    <th>Incidencias</th>
    <th>Acciones</th>
</tr>
</thead>
<tbody>

<?php while($row = pg_fetch_assoc($result)): 
    $nombre_cond = strtolower(trim($row['nombre']));
    $total_inc = $incidencias[$nombre_cond] ?? 0;
    $contrato = strtolower(trim($row['tipo_contrato'] ?? ''));
    
    // Determinar tipo de contrato
    if (strpos($contrato, 'temporal') !== false) {
        $badge_contrato = 'badge bg-warning text-dark';
    } elseif (strpos($contrato, 'permanente') !== false || strpos($contrato, 'indefinido') !== false || strpos($contrato, 'plazo fijo') !== false) {
        $badge_contrato = 'badge bg-primary';
    } elseif (empty($contrato)) {
        $badge_contrato = 'badge bg-secondary';
        $row['tipo_contrato'] = 'No definido';
    } else {
        $badge_contrato = 'badge bg-info';
    }
?>
<tr>
    <td><span class="badge bg-dark">#<?= $row['id_conductor'] ?></span></td>
    <td><strong><?= htmlspecialchars($row['nombre']) ?></strong></td>
    <td><?= htmlspecialchars($row['dni']) ?></td>
    <td><i class="bi bi-telephone" style="color:#888;"></i> <?= htmlspecialchars($row['telefono'] ?? '—') ?></td>
    <td><span class="code-badge"><?= htmlspecialchars($row['licencia']) ?></span></td>

    <td>
        <?php if ($row['estado'] == 'Activo'): ?>
            <span class="badge-estado badge-activo"><i class="bi bi-check-circle-fill"></i> Activo</span>
        <?php else: ?>
            <span class="badge-estado badge-inactivo"><i class="bi bi-x-circle-fill"></i> Inactivo</span>
        <?php endif; ?>
    </td>

    <td><?= $row['fecha_ingreso'] ? date("d/m/Y", strtotime($row['fecha_ingreso'])) : '—' ?></td>
    <td><span class="badge bg-info"><?= $row['dias_libres'] ?? 0 ?> días</span></td>
    <td><span class="badge bg-primary"><?= $row['dias_salidas'] ?? 0 ?> días</span></td>
    <td><?= htmlspecialchars($row['direccion'] ?? '—') ?></td>
    <td><?= htmlspecialchars($row['telefono_emergencia'] ?? '—') ?></td>
    <td><span class="<?= $badge_contrato ?>"><?= htmlspecialchars($row['tipo_contrato'] ?? 'No definido') ?></span></td>
    <td><span class="badge bg-secondary"><?= $row['vacaciones'] ?? 0 ?> días</span></td>

    <td>
        <?php if ($total_inc > 0): ?>
            <a href="../incidencia/incidencias.php?conductor=<?= urlencode($row['nombre']) ?>" 
               class="badge bg-danger text-decoration-none" 
               title="Ver incidencias de <?= htmlspecialchars($row['nombre']) ?>"
               style="cursor:pointer;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $total_inc ?>
            </a>
        <?php else: ?>
            <span class="badge bg-success"><i class="bi bi-check-circle"></i> 0</span>
        <?php endif; ?>
    </td>

    <td>
        <div class="d-flex gap-1 justify-content-center">
            <a href="editar_conduc.php?id=<?= $row['id_conductor'] ?>" class="btn btn-action btn-edit" title="Editar">
                <i class="bi bi-pencil-fill"></i>
            </a>
            <a href="eliminar.php?id=<?= $row['id_conductor'] ?>" 
               class="btn btn-action btn-delete"
               onclick="return confirm('¿Eliminar este conductor?')" 
               title="Eliminar">
                <i class="bi bi-trash3-fill"></i>
            </a>
        </div>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<?php else: ?>
<div class="text-center py-5">
    <i class="bi bi-people" style="font-size:5rem;color:#ccc;"></i>
    <h4 class="text-muted mt-3">No se encontraron conductores</h4>
    <a href="registrar.php" class="btn btn-success mt-3">
        <i class="bi bi-plus-circle-fill"></i> Registrar Nuevo Conductor
    </a>
</div>
<?php endif; ?>

</div>
</div>
</div>

<?php include("../../includes/footer.php"); ?>