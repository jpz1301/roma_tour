<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Validar ID usando pg_query_params (seguro)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT m.*, v.placa 
            FROM mantenimiento m
            LEFT JOIN vehiculos v ON m.vehiculo_id = v.id_vehiculo
            WHERE m.id = $1";
    $result = pg_query_params($conexion, $sql, [$id]);

    if ($result && pg_num_rows($result) > 0) {
        $mantenimiento = pg_fetch_assoc($result);
    } else {
        echo "Mantenimiento no encontrado";
        exit();
    }
} else {
    header("Location: listar_mantenimiento.php");
    exit();
}

// Configurar includes
$titulo = 'Ver Mantenimiento | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Detalle del Mantenimiento';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + EDITAR -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="listar_mantenimiento.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Mantenimientos
        </a>
        <a href="editar_mantenimiento.php?id=<?= $mantenimiento['id'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil-fill"></i> Editar Mantenimiento
        </a>
    </div>
</div>

<div class="container mb-5">

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #0dcaf0, #0aa2c0);">
    <h4><i class="bi bi-eye-fill"></i> Detalle del Mantenimiento #<?= $mantenimiento['id'] ?></h4>
</div>
<div class="card-body">

<!-- DATOS PRINCIPALES -->
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">ID</small>
                <h5 class="mb-0">#<?= $mantenimiento['id'] ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">Fecha</small>
                <h5 class="mb-0">
                    <?php 
                    $fecha = $mantenimiento['fecha'];
                    $timestamp = strtotime($fecha);
                    echo $timestamp ? date('d/m/Y', $timestamp) : htmlspecialchars($fecha);
                    ?>
                </h5>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">Tipo</small>
                <h5 class="mb-0">
                    <?php 
                    $tipo = $mantenimiento['tipo'];
                    $badge = ($tipo == 'Preventivo') ? 'badge-preventivo' : 'badge-correctivo';
                    ?>
                    <span class="badge-tipo <?= $badge ?>"><?= $tipo ?></span>
                </h5>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Vehículo</label>
        <p class="fs-5"><i class="bi bi-truck"></i> <?= htmlspecialchars($mantenimiento['placa'] ?? '—') ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Responsable (Conductor)</label>
        <p class="fs-5"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($mantenimiento['responsable'] ?? '—') ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Mecánico</label>
        <p class="fs-5"><?= htmlspecialchars($mantenimiento['mecanico_id'] ?? '—') ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Taller</label>
        <p class="fs-5"><?= htmlspecialchars($mantenimiento['taller_id'] ?? '—') ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Costo (S/)</label>
        <p class="fs-5"><?= number_format($mantenimiento['costo'] ?? 0, 2) ?></p>
    </div>
</div>

<hr>
<h5 class="fw-bold">🔧 Detalle del problema</h5>
<div class="card card-mantenimiento mb-4">
    <div class="card-body">
        <label class="fw-semibold">Problema reportado</label>
        <p class="fs-5"><?= nl2br(htmlspecialchars($mantenimiento['problema'] ?? 'Sin descripción')) ?></p>
    </div>
</div>

<hr>
<h5 class="fw-bold">📦 Observaciones</h5>
<div class="card card-mantenimiento">
    <div class="card-body">
        <p class="mb-0"><?= nl2br(htmlspecialchars($mantenimiento['observaciones'] ?? 'Sin observaciones')) ?></p>
    </div>
</div>

</div></div>
</div>

<style>
.card-mantenimiento {
    background-color: #f8f9fa;
    border-left: 4px solid #ff6b00;
}
.badge-tipo {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 500;
}
.badge-preventivo {
    background-color: #198754;
    color: white;
}
.badge-correctivo {
    background-color: #dc3545;
    color: white;
}
</style>

<?php include("../../includes/footer.php"); ?>
