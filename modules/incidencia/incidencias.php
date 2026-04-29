<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Recibir filtro por conductor desde la URL
$conductor_filtro = $_GET['conductor'] ?? '';

// Consulta principal con o sin filtro
if (!empty($conductor_filtro)) {
    $sql = "SELECT * FROM incidencias WHERE LOWER(conductor) = LOWER($1) ORDER BY fecha DESC, id_incidencia DESC";
    $result = pg_query_params($conexion, $sql, [$conductor_filtro]);
} else {
    $sql = "SELECT * FROM incidencias ORDER BY fecha DESC, id_incidencia DESC";
    $result = pg_query($conexion, $sql);
}

if (!$result) {
    die("Error en la consulta: " . pg_last_error($conexion));
}

// Estadísticas según filtro
if (!empty($conductor_filtro)) {
    $stats = pg_fetch_assoc(pg_query_params($conexion, "
        SELECT 
            COUNT(*) as total,
            COALESCE(SUM(costo), 0) as costo_total
        FROM incidencias 
        WHERE LOWER(conductor) = LOWER($1)
    ", [$conductor_filtro]));
} else {
    $stats = pg_fetch_assoc(pg_query($conexion, "
        SELECT 
            COUNT(*) as total,
            COALESCE(SUM(costo), 0) as costo_total
        FROM incidencias
    "));
}

// Configurar includes
$titulo = 'Incidencias | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = !empty($conductor_filtro) ? 'Incidencias de ' . htmlspecialchars($conductor_filtro) : 'Gestión de Incidencias';
$btn_nuevo_url = 'incidencias_registro.php';
$btn_nuevo_texto = 'Nueva Incidencia';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + NUEVO -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <div>
            <a href="../../index.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Volver al Menú
            </a>
            <?php if (!empty($conductor_filtro)): ?>
                <a href="../conductores/conductores.php" class="btn btn-outline-primary">
                    <i class="bi bi-people-fill"></i> Ver todos los conductores
                </a>
            <?php endif; ?>
        </div>
        <a href="incidencias_registro.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Nueva Incidencia
        </a>
    </div>
</div>

<div class="container mb-5">

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card total">
        <div class="stat-icon">📋</div>
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Total Incidencias</div>
    </div>
    <div class="stat-card activo">
        <div class="stat-icon">💰</div>
        <div class="stat-number">S/ <?= number_format($stats['costo_total'], 2) ?></div>
        <div class="stat-label">Costo Total</div>
    </div>
    <?php if (!empty($conductor_filtro)): ?>
    <div class="stat-card inactivo">
        <div class="stat-icon">👤</div>
        <div class="stat-number" style="font-size:1rem;"><?= htmlspecialchars($conductor_filtro) ?></div>
        <div class="stat-label">Conductor filtrado</div>
    </div>
    <?php endif; ?>
</div>

<!-- Tabla -->
<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #dc3545, #a71d2a);">
    <h4>
        <i class="bi bi-exclamation-triangle-fill"></i> 
        <?= !empty($conductor_filtro) ? 'Incidencias de ' . htmlspecialchars($conductor_filtro) : 'Listado de Incidencias' ?>
    </h4>
</div>
<div class="card-body">

<?php if(pg_num_rows($result) > 0): ?>
<div class="table-responsive">
<table id="tabla" class="table table-hover">
<thead>
<tr>
    <th>N° Incidencia</th>
    <th>Placa</th>
    <th>Conductor</th>
    <th>Fecha</th>
    <th>Tipo</th>
    <th>Incidencia</th>
    <th>Costo</th>
    <th>Servicio</th>
    <th>Acciones</th>
</tr>
</thead>
<tbody>

<?php while($row = pg_fetch_assoc($result)): ?>
<tr>
    <td><span class="badge bg-dark">#<?= $row['id_incidencia'] ?></span></td>
    <td><span class="code-badge"><?= htmlspecialchars($row['placa']) ?></span></td>
    <td><strong><?= htmlspecialchars($row['conductor']) ?></strong></td>
    <td><?= date("d/m/Y", strtotime($row['fecha'])) ?></td>
    <td>
        <?php 
        $tipos = [
            'leve'     => 'bg-warning text-dark',
            'moderada' => 'bg-orange text-white',
            'grave'    => 'bg-danger'
        ];
        $clase = $tipos[$row['tipo']] ?? 'bg-secondary';
        ?>
        <span class="badge <?= $clase ?>"><?= ucfirst($row['tipo']) ?></span>
    </td>
    <td><?= htmlspecialchars($row['incidencia']) ?></td>
    <td>
        <?php if ($row['costo'] > 0): ?>
            <span class="text-danger fw-bold">S/ <?= number_format($row['costo'], 2) ?></span>
        <?php else: ?>
            <span class="text-muted">—</span>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($row['tipo_servicio'] == 'tercerizado'): ?>
            <span class="badge bg-info">Tercerizado</span>
        <?php else: ?>
            <span class="badge bg-primary">Propio</span>
        <?php endif; ?>
    </td>
    <td>
        <div class="d-flex gap-1 justify-content-center">
            <a href="editar_incidencia.php?id=<?= $row['id_incidencia'] ?>" class="btn btn-action btn-edit" title="Editar">
                <i class="bi bi-pencil-fill"></i>
            </a>
            <a href="eliminar_incidencia.php?id=<?= $row['id_incidencia'] ?>" 
               class="btn btn-action btn-delete"
               onclick="return confirm('¿Eliminar esta incidencia?')" 
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
    <i class="bi bi-clipboard-check" style="font-size:5rem;color:#ccc;"></i>
    <h4 class="text-muted mt-3">
        <?= !empty($conductor_filtro) ? 'No hay incidencias para ' . htmlspecialchars($conductor_filtro) : 'No se encontraron incidencias' ?>
    </h4>
    <a href="incidencias_registro.php" class="btn btn-danger mt-3">
        <i class="bi bi-plus-circle-fill"></i> Registrar Nueva Incidencia
    </a>
</div>
<?php endif; ?>

</div>
</div>
</div>

<?php include("../../includes/footer.php"); ?>  