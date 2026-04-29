<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$result = pg_query($conexion, "
    SELECT p.*, v.placa, r.nombre AS ruta
    FROM programacion_servicio p
    LEFT JOIN vehiculos v ON p.vehiculo_id = v.id_vehiculo
    LEFT JOIN rutas r ON p.ruta_id = r.id
    ORDER BY p.fecha ASC
");

if (!$result) {
    die("Error: " . pg_last_error($conexion));
}

// Estadísticas
$stats = pg_fetch_assoc(pg_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado = 'Programado' THEN 1 ELSE 0 END) as programados,
    SUM(CASE WHEN estado = 'En ruta' THEN 1 ELSE 0 END) as en_ruta,
    SUM(CASE WHEN estado = 'Finalizado' THEN 1 ELSE 0 END) as finalizados
    FROM programacion_servicio"));

// Configurar includes
$titulo = 'Programación de Servicios | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Programación de Servicios';
$btn_nuevo_url = 'programacion.php';
$btn_nuevo_texto = 'Nuevo Servicio';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + NUEVO -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="../../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Menú
        </a>
        <a href="programacion.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Nuevo Servicio
        </a>
    </div>
</div>

<div class="container mb-5">

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card total">
        <div class="stat-icon">📅</div>
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Total Servicios</div>
    </div>
    <div class="stat-card programado">
        <div class="stat-icon">📋</div>
        <div class="stat-number"><?= $stats['programados'] ?></div>
        <div class="stat-label">Programados</div>
    </div>
    <div class="stat-card en_ruta">
        <div class="stat-icon">🚌</div>
        <div class="stat-number"><?= $stats['en_ruta'] ?></div>
        <div class="stat-label">En Ruta</div>
    </div>
    <div class="stat-card finalizado">
        <div class="stat-icon">✅</div>
        <div class="stat-number"><?= $stats['finalizados'] ?></div>
        <div class="stat-label">Finalizados</div>
    </div>
</div>

<!-- Tabla -->
<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #198754, #0f5132);">
    <h4><i class="bi bi-calendar-check"></i> Programación de Servicios</h4>
</div>
<div class="card-body">

<?php if(pg_num_rows($result) > 0): ?>
<div class="table-responsive">
<table id="tabla" class="table table-hover">
<thead>
<tr>
    <th>Código</th>
    <th>Cliente</th>
    <th>Ruta</th>
    <th>Servicio</th>
    <th>Vehículo / Proveedor</th>
    <th>Conductor</th>
    <th>Fecha</th>
    <th>Hora</th>
    <th>Personas</th>
    <th>Estado</th>
    <th>KM</th>
    <th>Acciones</th>
</tr>
</thead>
<tbody>

<?php while($row = pg_fetch_assoc($result)): ?>
<tr>
    <td><span class="code-badge"><?= htmlspecialchars($row['codigo']) ?></span></td>
    <td><strong><?= htmlspecialchars($row['cliente']) ?></strong></td>
    <td><?= htmlspecialchars($row['ruta'] ?? '—') ?></td>

    <td>
        <?php if($row['tipo_servicio'] == 'propio'): ?>
            <span class="badge-servicio badge-propio"><i class="bi bi-person-check"></i> Propio</span>
        <?php else: ?>
            <span class="badge-servicio badge-tercerizado"><i class="bi bi-people"></i> Tercerizado</span>
        <?php endif; ?>
    </td>

    <td>
        <?php if($row['tipo_servicio'] == 'propio'): ?>
            <span class="placa-badge"><i class="bi bi-truck-front"></i> <?= htmlspecialchars($row['placa']) ?></span>
        <?php else: ?>
            <strong><?= htmlspecialchars($row['proveedor']) ?></strong><br>
            <small class="text-muted">Placa: <?= htmlspecialchars($row['placa_externa']) ?></small>
        <?php endif; ?>
    </td>

    <td><i class="bi bi-person" style="color:#666;"></i> <?= htmlspecialchars($row['conductor']) ?></td>

    <td>
        <span style="font-weight:600;"><?= date("d", strtotime($row['fecha'])) ?></span>
        <span style="color:#888;font-size:0.8rem;"><?= date("M Y", strtotime($row['fecha'])) ?></span>
    </td>

    <td><?= $row['hora'] ? date("H:i", strtotime($row['hora'])) : '—' ?></td>

    <td><span class="badge bg-dark"><?= $row['cantidad_personas'] ?> pax</span></td>

    <td>
        <?php if($row['estado'] == 'Programado'): ?>
            <span class="badge-estado badge-mantenimiento"><i class="bi bi-clock"></i> Programado</span>
        <?php elseif($row['estado'] == 'En ruta'): ?>
            <span class="badge-estado" style="background:#dbeafe;color:#1e40af;"><i class="bi bi-truck"></i> En ruta</span>
        <?php else: ?>
            <span class="badge-estado badge-activo"><i class="bi bi-check-circle-fill"></i> Finalizado</span>
        <?php endif; ?>
    </td>

    <td><span class="badge bg-secondary"><?= $row['km_salida'] ?? '—' ?></span></td>

    <td>
        <div class="d-flex gap-1 justify-content-center">
            <a href="editar_programacion.php?id=<?= $row['id'] ?>" class="btn btn-action btn-edit" title="Editar">
                <i class="bi bi-pencil-fill"></i>
            </a>
            <button class="btn btn-action btn-view iniciar" data-id="<?= $row['id'] ?>" title="Iniciar">
                <i class="bi bi-play-fill"></i>
            </button>
            <button class="btn btn-action btn-edit finalizar" data-id="<?= $row['id'] ?>" title="Finalizar">
                <i class="bi bi-check-lg"></i>
            </button>
            <button class="btn btn-action btn-delete eliminar" data-id="<?= $row['id'] ?>" title="Eliminar">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </div>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<?php else: ?>
<div class="text-center py-5">
    <i class="bi bi-calendar-x" style="font-size:5rem;color:#ccc;"></i>
    <h4 class="text-muted mt-3">No hay servicios programados</h4>
    <a href="programacion.php" class="btn btn-success mt-3">
        <i class="bi bi-plus-circle-fill"></i> Programar Nuevo Servicio
    </a>
</div>
<?php endif; ?>

</div></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// INICIAR
$(".iniciar").click(function(){
    let id = $(this).data("id");
    if(confirm("¿Iniciar este servicio?")){
        $.post("actualizar_estado.php", {id: id, accion: "iniciar"}, function(res){
            if(res.trim() == "ok"){
                alert("Servicio iniciado 🚗");
                location.reload();
            } else {
                alert("Error al iniciar el servicio: " + res);
            }
        });
    }
});

// FINALIZAR
$(".finalizar").click(function(){
    let id = $(this).data("id");
    let km_llegada = prompt("Ingrese KM de llegada:");
    if(km_llegada != null && km_llegada != ""){
        $.post("actualizar_estado.php", {
            id: id,
            accion: "finalizar",
            km_llegada: km_llegada
        }, function(res){
            if(res.trim() == "ok"){
                alert("Servicio finalizado ✅");
                location.reload();
            } else {
                alert("Error al finalizar el servicio: " + res);
            }
        });
    }
});

// ELIMINAR
$(".eliminar").click(function(){
    let id = $(this).data("id");
    if(confirm("¿Estás seguro de eliminar este servicio? Esta acción no se puede deshacer.")){
        $.post("eliminar_servicio.php", {id: id}, function(res){
            if(res.trim() == "ok"){
                alert("Servicio eliminado correctamente 🗑️");
                location.reload();
            } else {
                alert("Error al eliminar el servicio: " + res);
            }
        });
    }
});
</script>

<?php include("../../includes/footer.php"); ?>