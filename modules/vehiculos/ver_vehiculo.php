<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Validar ID usando pg_query_params (seguro)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT *, 
                   EXTRACT(DAY FROM (soat_fecha_vencimiento - CURRENT_DATE)) as dias_restantes
            FROM vehiculos WHERE id_vehiculo = $1";
    $result = pg_query_params($conexion, $sql, [$id]);

    if ($result && pg_num_rows($result) > 0) {
        $vehiculo = pg_fetch_assoc($result);
    } else {
        echo "Vehículo no encontrado";
        exit();
    }
} else {
    header("Location: vehiculos.php");
    exit();
}

// Función para obtener el badge del SOAT
function getBadgeSOATDetalle($fecha, $dias) {
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
                    <i class="bi bi-exclamation-triangle-fill"></i> Vence en ' . $dias . ' días (URGENTE)
                </span>';
    } elseif($dias <= 15) {
        return '<span class="badge-soat-proximo" title="Vence en ' . $dias . ' días - ' . $fecha_formateada . '">
                    <i class="bi bi-clock-fill"></i> Vence en ' . $dias . ' días
                </span>';
    } elseif($dias <= 30) {
        return '<span class="badge-soat-aviso" title="Vence en ' . $dias . ' días - ' . $fecha_formateada . '">
                    <i class="bi bi-bell-fill"></i> Vence en ' . $dias . ' días
                </span>';
    } else {
        return '<span class="badge-soat-vigente" title="Vence: ' . $fecha_formateada . '">
                    <i class="bi bi-shield-check-fill"></i> Vigente
                </span>';
    }
}

// Configurar includes
$titulo = 'Ver Vehículo | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Detalle del Vehículo';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + EDITAR -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="vehiculos.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Vehículos
        </a>
        <a href="editar_vehi.php?id=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil-fill"></i> Editar Vehículo
        </a>
    </div>
</div>

<div class="container mb-5">

<!-- ALERTA DE SOAT (si está próximo a vencer) -->
<?php if(!empty($vehiculo['soat_fecha_vencimiento'])): 
    $dias = $vehiculo['dias_restantes'];
    if($dias < 0): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow mb-4" role="alert" style="border-left: 5px solid #8B0000;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-x-octagon-fill" style="font-size: 2rem; color: #8B0000;"></i>
                </div>
                <div>
                    <strong><i class="bi bi-exclamation-triangle-fill"></i> ¡SOAT VENCIDO!</strong><br>
                    El SOAT venció el <?= date('d/m/Y', strtotime($vehiculo['soat_fecha_vencimiento'])) ?>.
                    <strong>No puede circular sin SOAT vigente.</strong>
                </div>
            </div>
        </div>
    <?php elseif($dias <= 7): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow mb-4" role="alert" style="border-left: 5px solid #ff0000;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; color: #ff0000;"></i>
                </div>
                <div>
                    <strong><i class="bi bi-alarm-fill"></i> ¡ALERTA! SOAT por vencer en <?= $dias ?> días</strong><br>
                    Vence el <?= date('d/m/Y', strtotime($vehiculo['soat_fecha_vencimiento'])) ?>.
                    <strong>Renovar lo antes posible.</strong>
                </div>
            </div>
        </div>
    <?php elseif($dias <= 15): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow mb-4" role="alert" style="border-left: 5px solid #ff6b00;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-clock-fill" style="font-size: 2rem; color: #ff6b00;"></i>
                </div>
                <div>
                    <strong><i class="bi bi-calendar-exclamation"></i> SOAT por vencer en <?= $dias ?> días</strong><br>
                    Vence el <?= date('d/m/Y', strtotime($vehiculo['soat_fecha_vencimiento'])) ?>.
                </div>
            </div>
        </div>
    <?php elseif($dias <= 30): ?>
        <div class="alert alert-info alert-dismissible fade show shadow mb-4" role="alert" style="border-left: 5px solid #ffc107;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-bell-fill" style="font-size: 2rem; color: #ffc107;"></i>
                </div>
                <div>
                    <strong><i class="bi bi-calendar-check"></i> Recordatorio: SOAT vence en <?= $dias ?> días</strong><br>
                    Vence el <?= date('d/m/Y', strtotime($vehiculo['soat_fecha_vencimiento'])) ?>.
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #0dcaf0, #0aa2c0);">
    <h4><i class="bi bi-eye-fill"></i> Detalle del Vehículo: <?= htmlspecialchars($vehiculo['placa']) ?></h4>
</div>
<div class="card-body">

<!-- DATOS BÁSICOS -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">ID</small>
                <h5 class="mb-0">#<?= $vehiculo['id_vehiculo'] ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">Código</small>
                <h5 class="mb-0"><span class="code-badge"><?= htmlspecialchars($vehiculo['code'] ?? '—') ?></span></h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">Estado</small>
                <h5 class="mb-0">
                    <?php
                    $estado = $vehiculo['estado'];
                    $badge = match($estado) {
                        'Activo' => 'badge-activo',
                        'Mantenimiento' => 'badge-mantenimiento',
                        default => 'badge-inactivo'
                    };
                    ?>
                    <span class="badge-estado <?= $badge ?>"><?= $estado ?></span>
                </h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">Asientos</small>
                <h5 class="mb-0"><i class="bi bi-person-fill"></i> <?= $vehiculo['asientos'] ?? '—' ?></h5>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Placa</label>
        <p><span class="placa-badge"><i class="bi bi-truck-front"></i> <?= htmlspecialchars($vehiculo['placa']) ?></span></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Marca</label>
        <p class="fs-5"><?= htmlspecialchars($vehiculo['marca']) ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Modelo</label>
        <p class="fs-5"><?= htmlspecialchars($vehiculo['modelo']) ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Edición</label>
        <p class="fs-5"><?= htmlspecialchars($vehiculo['edicion']) ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">SOAT (Aseguradora/Número)</label>
        <p class="fs-5"><?= htmlspecialchars($vehiculo['soat'] ?? '—') ?></p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold"><i class="bi bi-calendar"></i> Fecha Vencimiento SOAT</label>
        <p class="fs-5">
            <?php 
            if (!empty($vehiculo['soat_fecha_vencimiento'])) {
                $fecha = $vehiculo['soat_fecha_vencimiento'];
                $timestamp = strtotime($fecha);
                if ($timestamp !== false) {
                    echo date('d/m/Y', $timestamp);
                } else {
                    echo htmlspecialchars($fecha);
                }
            } else {
                echo '—';
            }
            ?>
        </p>
    </div>
    <div class="col-md-4 mb-3">
        <label class="fw-semibold">Estado SOAT</label>
        <p><?= getBadgeSOATDetalle($vehiculo['soat_fecha_vencimiento'], $vehiculo['dias_restantes'] ?? null) ?></p>
    </div>
</div>

<hr>
<h5 class="fw-bold">📋 Checklist de Elementos</h5>

<table class="table table-bordered checklist-table">
<thead class="table-dark">
<tr><th>Item</th><th class="text-center bg-success text-white">B</th><th class="text-center bg-warning">R</th><th class="text-center bg-danger text-white">M</th><th>Estado</th></tr>
</thead>
<tbody>

<?php
$campos_radio = [
    "espejo_derecho" => "Espejo Derecho", "espejo_izquierdo" => "Espejo Izquierdo",
    "claxon" => "Claxon", "antena" => "Antena",
    "parabrisas_frontal" => "Parabrisas Frontal", "parabrisas_posterior" => "Parabrisas Posterior",
    "tapa_combustible" => "Tapa Combustible", "tapa_aceite_motor" => "Tapa Aceite Motor", "tapa_radiator" => "Tapa Radiador",
    "luces_altas" => "Luces Altas", "luces_bajas" => "Luces Bajas", "luces_traseras" => "Luces Traseras",
    "luces_freno" => "Luces Freno", "luces_intermitentes" => "Luces Intermitentes",
    "cinturon" => "Cinturón", "radio" => "Radio", "extintor" => "Extintor",
    "llave_rueda" => "Llave Rueda", "linterna" => "Linterna", "gato" => "Gato",
    "aire_forzado" => "Aire Forzado", "alarma" => "Alarma", "cone_seguridad" => "Cono Seguridad",
    "suspension" => "Suspensión", "emblemas" => "Emblemas"
];

foreach($campos_radio as $name => $label){
    $val = $vehiculo[$name] ?? 'B';
    $texto = match($val) { 'B' => '✅ Bueno', 'R' => '⚠️ Regular', default => '❌ Malo' };
    echo "<tr>
        <td class='fw-semibold'>$label</td>
        <td class='text-center bg-success bg-opacity-25'>".($val=='B'?'●':'')."</td>
        <td class='text-center bg-warning bg-opacity-25'>".($val=='R'?'●':'')."</td>
        <td class='text-center bg-danger bg-opacity-25'>".($val=='M'?'●':'')."</td>
        <td><b>$texto</b></td>
    </tr>";
}
?>
</tbody>
</table>

<hr>
<h5 class="fw-bold">🛞 Repuestos e Insumos</h5>

<div class="row">
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">🛞 Llanta Repuesto</label>
                <p class="mb-0 fs-5"><?= htmlspecialchars($vehiculo['llanta_repuesto'] ?? 'No especificado') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">🛢️ Aceite Motor</label>
                <p class="mb-0 fs-5"><?= htmlspecialchars($vehiculo['aceite_motor'] ?? 'No especificado') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">💧 Refrigerante</label>
                <p class="mb-0 fs-5"><?= htmlspecialchars($vehiculo['refrigerante'] ?? 'No especificado') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">⚙️ Aceite Dirección</label>
                <p class="mb-0 fs-5"><?= htmlspecialchars($vehiculo['aceite_direccion'] ?? 'No especificado') ?></p>
            </div>
        </div>
    </div>
</div>

<hr>
<h5 class="fw-bold">🛞 Llantas</h5>

<div class="row mb-3">
    <?php
    $llantas = [
        ['Del Izq', 'marca_llanta_del_izq', 'presion_llanta_del_izq'],
        ['Del Der', 'marca_llanta_del_der', 'presion_llanta_del_der'],
        ['Post Izq Int', 'marca_llanta_post_izq_int', 'presion_llanta_post_izq_int'],
        ['Post Izq Ext', 'marca_llanta_post_izq_ext', 'presion_llanta_post_izq_ext'],
        ['Post Der Int', 'marca_llanta_post_der_int', 'presion_llanta_post_der_int'],
        ['Post Der Ext', 'marca_llanta_post_der_ext', 'presion_llanta_post_der_ext'],
    ];
    foreach($llantas as $ll):
    ?>
    <div class="col-md-2 mb-2">
        <div class="card card-repuesto h-100">
            <div class="card-body text-center">
                <label class="form-label fw-bold"><?= $ll[0] ?></label>
                <p class="mb-0 small">Marca: <?= htmlspecialchars($vehiculo[$ll[1]] ?? '—') ?></p>
                <p class="mb-0 small">Presión: <?= htmlspecialchars($vehiculo[$ll[2]] ?? '—') ?> PSI</p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<hr>
<h5 class="fw-bold">📦 Observaciones</h5>
<div class="card card-repuesto">
    <div class="card-body">
        <p class="mb-0"><?= nl2br(htmlspecialchars($vehiculo['observaciones'] ?? 'Sin observaciones')) ?></p>
    </div>
</div>

</div></div>
</div>

<style>
.card-repuesto {
    background-color: #f8f9fa;
    border-left: 4px solid #ff6b00;
}
.checklist-table td { vertical-align: middle; }

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