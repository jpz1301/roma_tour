<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

function v($campo){
    return $_POST[$campo] ?? 'B';
}

// Obtener vehículo de forma segura
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM vehiculos WHERE id_vehiculo = $1";
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

// Actualizar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $code = $_POST['code'];
    $placa = $_POST['placa'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $edicion = $_POST['edicion'];
    $estado = $_POST['estado'];
    $asientos = $_POST['asientos'] ?? '';
    $soat = $_POST['soat'] ?? '';
    $soat_fecha_vencimiento = $_POST['soat_fecha_vencimiento'] ?? null;

    $verificar = pg_query_params($conexion, "SELECT 1 FROM vehiculos WHERE code = $1 AND id_vehiculo != $2", [$code, $id]);

    if (pg_num_rows($verificar) > 0) {
        $error = "El código ya existe";
    } else {
        // Convertir presiones vacías a NULL
        $presiones = [];
        $campos_presion = [
            'presion_llanta_del_izq', 'presion_llanta_del_der',
            'presion_llanta_post_izq_int', 'presion_llanta_post_izq_ext',
            'presion_llanta_post_der_int', 'presion_llanta_post_der_ext'
        ];
        foreach ($campos_presion as $campo) {
            $val = $_POST[$campo] ?? '';
            $presiones[$campo] = ($val !== '' && $val !== null) ? intval($val) : null;
        }

        if (!isset($error)) {
            $sql = "UPDATE vehiculos SET 
                    code = $1, placa = $2, marca = $3, modelo = $4, edicion = $5, asientos = $6, estado = $7,
                    soat = $8, soat_fecha_vencimiento = $9,
                    espejo_derecho = $10, espejo_izquierdo = $11, claxon = $12, antena = $13,
                    parabrisas_frontal = $14, parabrisas_posterior = $15,
                    tapa_combustible = $16, tapa_aceite_motor = $17, tapa_radiator = $18,
                    luces_altas = $19, luces_bajas = $20, luces_traseras = $21, luces_freno = $22, luces_intermitentes = $23,
                    cinturon = $24, radio = $25, extintor = $26, llanta_repuesto = $27,
                    llave_rueda = $28, linterna = $29, gato = $30, aire_forzado = $31,
                    aceite_motor = $32, refrigerante = $33, aceite_direccion = $34,
                    alarma = $35, cone_seguridad = $36, suspension = $37, emblemas = $38,
                    observaciones = $39,
                    marca_llanta_del_izq = $41, presion_llanta_del_izq = $42,
                    marca_llanta_del_der = $43, presion_llanta_del_der = $44,
                    marca_llanta_post_izq_int = $45, presion_llanta_post_izq_int = $46,
                    marca_llanta_post_izq_ext = $47, presion_llanta_post_izq_ext = $48,
                    marca_llanta_post_der_int = $49, presion_llanta_post_der_int = $50,
                    marca_llanta_post_der_ext = $51, presion_llanta_post_der_ext = $52
                    WHERE id_vehiculo = $40";

            $params = [
                $code, $placa, $marca, $modelo, $edicion, $asientos, $estado,
                $soat, $soat_fecha_vencimiento,
                v('espejo_derecho'), v('espejo_izquierdo'), v('claxon'), v('antena'),
                v('parabrisas_frontal'), v('parabrisas_posterior'),
                v('tapa_combustible'), v('tapa_aceite_motor'), v('tapa_radiator'),
                v('luces_altas'), v('luces_bajas'), v('luces_traseras'), v('luces_freno'), v('luces_intermitentes'),
                v('cinturon'), v('radio'), v('extintor'), $_POST['llanta_repuesto'] ?? '',
                v('llave_rueda'), v('linterna'), v('gato'), v('aire_forzado'),
                $_POST['aceite_motor'] ?? '', $_POST['refrigerante'] ?? '', $_POST['aceite_direccion'] ?? '',
                v('alarma'), v('cone_seguridad'), v('suspension'), v('emblemas'),
                $_POST['observaciones'] ?? '',
                $id,
                $_POST['marca_llanta_del_izq'] ?? '', $presiones['presion_llanta_del_izq'],
                $_POST['marca_llanta_del_der'] ?? '', $presiones['presion_llanta_del_der'],
                $_POST['marca_llanta_post_izq_int'] ?? '', $presiones['presion_llanta_post_izq_int'],
                $_POST['marca_llanta_post_izq_ext'] ?? '', $presiones['presion_llanta_post_izq_ext'],
                $_POST['marca_llanta_post_der_int'] ?? '', $presiones['presion_llanta_post_der_int'],
                $_POST['marca_llanta_post_der_ext'] ?? '', $presiones['presion_llanta_post_der_ext']
            ];

            $result = pg_query_params($conexion, $sql, $params);

            if ($result) {
                header("Location: vehiculos.php");
                exit();
            } else {
                $error = "Error al actualizar: " . pg_last_error($conexion);
            }
        }
    }
}

// Configurar includes
$titulo = 'Editar Vehículo | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Editar Vehículo';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTÓN VOLVER -->
<div class="container mb-3">
    <a href="vehiculos.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver a Vehículos
    </a>
</div>

<div class="container mb-5">

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
    <h4><i class="bi bi-pencil-fill"></i> Editar Vehículo: <?= htmlspecialchars($vehiculo['placa']) ?></h4>
</div>
<div class="card-body">

<form method="POST">
<input type="hidden" name="id" value="<?= $vehiculo['id_vehiculo'] ?>">

<!-- DATOS BÁSICOS -->
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Código</label>
        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($vehiculo['code']) ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Placa</label>
        <input type="text" name="placa" class="form-control" value="<?= htmlspecialchars($vehiculo['placa']) ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Marca</label>
        <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($vehiculo['marca']) ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Modelo</label>
        <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($vehiculo['modelo']) ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Edición</label>
        <input type="number" name="edicion" class="form-control" value="<?= $vehiculo['edicion'] ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Asientos</label>
        <input type="number" name="asientos" class="form-control" value="<?= $vehiculo['asientos'] ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select">
            <option value="Activo" <?= $vehiculo['estado']=='Activo'?'selected':'' ?>>Activo</option>
            <option value="Mantenimiento" <?= $vehiculo['estado']=='Mantenimiento'?'selected':'' ?>>Mantenimiento</option>
            <option value="Inactivo" <?= $vehiculo['estado']=='Inactivo'?'selected':'' ?>>Inactivo</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">📄 SOAT (Aseguradora/Número)</label>
        <input type="text" name="soat" class="form-control" value="<?= htmlspecialchars($vehiculo['soat'] ?? '') ?>" placeholder="Ej: Sura - 123456">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">
            <i class="bi bi-calendar"></i> Fecha Vencimiento SOAT
        </label>
        <input type="date" name="soat_fecha_vencimiento" class="form-control" 
               value="<?= htmlspecialchars($vehiculo['soat_fecha_vencimiento'] ?? '') ?>" required>
        <small class="text-muted">⚠️ El sistema mostrará alertas 30 días antes del vencimiento</small>
    </div>
</div>

<hr>
<h5 class="fw-bold">📋 Checklist de Elementos</h5>

<table class="table table-bordered checklist-table">
<thead class="table-dark">
<tr><th>Item</th><th class="text-center bg-success text-white">B</th><th class="text-center bg-warning">R</th><th class="text-center bg-danger text-white">M</th></tr>
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
    echo "<tr>
        <td class='fw-semibold'>$label</td>
        <td class='text-center bg-success bg-opacity-25'><input type='radio' name='$name' value='B' ".($val=='B'?'checked':'')."></td>
        <td class='text-center bg-warning bg-opacity-25'><input type='radio' name='$name' value='R' ".($val=='R'?'checked':'')."></td>
        <td class='text-center bg-danger bg-opacity-25'><input type='radio' name='$name' value='M' ".($val=='M'?'checked':'')."></td>
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
                <label class="form-label fw-bold">🛞 Llanta de Repuesto</label>
                <input type="text" name="llanta_repuesto" class="form-control" value="<?= htmlspecialchars($vehiculo['llanta_repuesto'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">🛢️ Aceite Motor</label>
                <input type="text" name="aceite_motor" class="form-control" value="<?= htmlspecialchars($vehiculo['aceite_motor'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">💧 Refrigerante</label>
                <input type="text" name="refrigerante" class="form-control" value="<?= htmlspecialchars($vehiculo['refrigerante'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">⚙️ Aceite Dirección</label>
                <input type="text" name="aceite_direccion" class="form-control" value="<?= htmlspecialchars($vehiculo['aceite_direccion'] ?? '') ?>">
            </div>
        </div>
    </div>
</div>

<hr>
<h5 class="fw-bold">📦 Observaciones</h5>
<div class="mb-3">
    <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($vehiculo['observaciones'] ?? '') ?></textarea>
</div>

<hr>
<h5 class="fw-bold">🛞 Llantas</h5>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Del Izq Marca</label>
        <input type="text" name="marca_llanta_del_izq" class="form-control" value="<?= htmlspecialchars($vehiculo['marca_llanta_del_izq'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_del_izq" class="form-control" value="<?= $vehiculo['presion_llanta_del_izq'] ?? '' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Del Der Marca</label>
        <input type="text" name="marca_llanta_del_der" class="form-control" value="<?= htmlspecialchars($vehiculo['marca_llanta_del_der'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_del_der" class="form-control" value="<?= $vehiculo['presion_llanta_del_der'] ?? '' ?>">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Izq Int</label>
        <input type="text" name="marca_llanta_post_izq_int" class="form-control" value="<?= htmlspecialchars($vehiculo['marca_llanta_post_izq_int'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_izq_int" class="form-control" value="<?= $vehiculo['presion_llanta_post_izq_int'] ?? '' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Izq Ext</label>
        <input type="text" name="marca_llanta_post_izq_ext" class="form-control" value="<?= htmlspecialchars($vehiculo['marca_llanta_post_izq_ext'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_izq_ext" class="form-control" value="<?= $vehiculo['presion_llanta_post_izq_ext'] ?? '' ?>">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Der Int</label>
        <input type="text" name="marca_llanta_post_der_int" class="form-control" value="<?= htmlspecialchars($vehiculo['marca_llanta_post_der_int'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_der_int" class="form-control" value="<?= $vehiculo['presion_llanta_post_der_int'] ?? '' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Der Ext</label>
        <input type="text" name="marca_llanta_post_der_ext" class="form-control" value="<?= htmlspecialchars($vehiculo['marca_llanta_post_der_ext'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_der_ext" class="form-control" value="<?= $vehiculo['presion_llanta_post_der_ext'] ?? '' ?>">
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar</button>
    <a href="vehiculos.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
</div>

</form>
</div></div>
</div>

<style>
.card-repuesto {
    background-color: #f8f9fa;
    border-left: 4px solid #ff6b00;
}
.checklist-table input[type="radio"] { transform: scale(1.2); cursor: pointer; }
</style>

<?php include("../../includes/footer.php"); ?>