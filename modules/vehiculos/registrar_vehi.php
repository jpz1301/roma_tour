<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../includes/seguridad.php");
include("../../config/conexion.php");

function v($campo){
    return $_POST[$campo] ?? 'B';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code   = $_POST['code'] ?? '';
    $placa  = $_POST['placa'] ?? '';
    $marca  = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $edicion = $_POST['edicion'] ?? '';
    $asientos = $_POST['asientos'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $soat_fecha_vencimiento = $_POST['soat_fecha_vencimiento'] ?? null;

    if ($code == "" || $placa == "" || $marca == "" || $modelo == "" || $edicion == "" || $asientos == "") {
        $error = "Todos los campos son obligatorios";
    } else {
        $verificar = pg_query_params($conexion, "SELECT 1 FROM vehiculos WHERE code=$1", [$code]);

        if (pg_num_rows($verificar) > 0) {
            $error = "El código ya existe";
        } else {
            $sql = "INSERT INTO vehiculos 
            (code, placa, marca, modelo, edicion, asientos, estado,
             soat, soat_fecha_vencimiento, revision_tecnica, manifiesto_pasajeros,
             espejo_derecho, espejo_izquierdo, claxon, antena,
             parabrisas_frontal, parabrisas_posterior,
             tapa_combustible, tapa_aceite_motor, tapa_radiator,
             luces_altas, luces_bajas, luces_traseras, luces_freno, luces_intermitentes,
             cinturon, radio, extintor, llave_rueda,
             linterna, gato, aire_forzado,
             alarma, cone_seguridad, suspension, emblemas,
             llanta_repuesto, aceite_motor, refrigerante, aceite_direccion, observaciones) 
            VALUES ($1,$2,$3,$4,$5,$6,$7, $8,$9,NULL,NULL,
             $10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23,
             $24,$25,$26,$27,$28,$29,$30,$31,$32,$33,$34,
             $35,$36,$37,$38,$39)
            RETURNING id_vehiculo";

            $params = array(
                $code, $placa, $marca, $modelo, $edicion, $asientos, $estado,
                $_POST['soat'] ?? '',
                $soat_fecha_vencimiento,
                v('espejo_derecho'), v('espejo_izquierdo'), v('claxon'), v('antena'),
                v('parabrisas_frontal'), v('parabrisas_posterior'),
                v('tapa_combustible'), v('tapa_aceite_motor'), v('tapa_radiator'),
                v('luces_altas'), v('luces_bajas'), v('luces_traseras'), v('luces_freno'), v('luces_intermitentes'),
                v('cinturon'), v('radio'), v('extintor'), v('llave_rueda'),
                v('linterna'), v('gato'), v('aire_forzado'),
                v('alarma'), v('cone_seguridad'), v('suspension'), v('emblemas'),
                $_POST['llanta_repuesto'] ?? '',
                $_POST['aceite_motor'] ?? '',
                $_POST['refrigerante'] ?? '',
                $_POST['aceite_direccion'] ?? '',
                $_POST['observaciones'] ?? ''
            );

            $result = pg_query_params($conexion, $sql, $params);

            if ($result) {
                echo "<script>alert('✅ Vehículo guardado correctamente'); window.location='vehiculos.php';</script>";
                exit();
            } else {
                $error = "Error: " . pg_last_error($conexion);
            }
        }
    }
}

// Configurar includes
$titulo = 'Registrar Vehículo | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Registrar Vehículo';

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
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?></div>
<?php endif; ?>

<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
    <h4><i class="bi bi-plus-circle-fill"></i> Registrar Nuevo Vehículo</h4>
</div>
<div class="card-body">

<form method="POST">

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Código</label>
        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($_POST['code'] ?? '') ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Placa</label>
        <input type="text" name="placa" class="form-control" value="<?= htmlspecialchars($_POST['placa'] ?? '') ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Marca</label>
        <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Modelo</label>
        <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Edición</label>
        <input type="number" name="edicion" class="form-control" value="<?= htmlspecialchars($_POST['edicion'] ?? '') ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Asientos</label>
        <input type="number" name="asientos" class="form-control" min="1" value="<?= htmlspecialchars($_POST['asientos'] ?? '') ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select">
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
            <option value="Mantenimiento">Mantenimiento</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">SOAT</label>
        <input type="text" name="soat" class="form-control" placeholder="Ej: Número de póliza" value="<?= htmlspecialchars($_POST['soat'] ?? '') ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">
            <i class="bi bi-calendar"></i> Fecha Vencimiento SOAT
        </label>
        <input type="date" name="soat_fecha_vencimiento" class="form-control" 
               value="<?= htmlspecialchars($_POST['soat_fecha_vencimiento'] ?? '') ?>" required>
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
    echo "<tr>
        <td class='fw-semibold'>$label</td>
        <td class='text-center bg-success bg-opacity-25'><input type='radio' name='$name' value='B' checked></td>
        <td class='text-center bg-warning bg-opacity-25'><input type='radio' name='$name' value='R'></td>
        <td class='text-center bg-danger bg-opacity-25'><input type='radio' name='$name' value='M'></td>
    </tr>";
}
?>
</tbody>
</table>

<hr>
<h5 class="fw-bold">🛞 Repuestos e Insumos</h5>
<p class="text-muted small">Describe el estado, marca o detalles de cada elemento</p>

<div class="row">
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">🛞 Llanta de Repuesto</label>
                <input type="text" name="llanta_repuesto" class="form-control" placeholder="Ej: Nueva, Goodyear, 15 pulgadas" value="<?= htmlspecialchars($_POST['llanta_repuesto'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">🛢️ Aceite Motor</label>
                <input type="text" name="aceite_motor" class="form-control" placeholder="Ej: 5W-30, Castrol, Nivel óptimo" value="<?= htmlspecialchars($_POST['aceite_motor'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">💧 Refrigerante</label>
                <input type="text" name="refrigerante" class="form-control" placeholder="Ej: Verde, 50/50, Nivel correcto" value="<?= htmlspecialchars($_POST['refrigerante'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-repuesto mb-3">
            <div class="card-body">
                <label class="form-label fw-bold">⚙️ Aceite Dirección Hidráulica</label>
                <input type="text" name="aceite_direccion" class="form-control" placeholder="Ej: ATF, Nivel óptimo" value="<?= htmlspecialchars($_POST['aceite_direccion'] ?? '') ?>">
            </div>
        </div>
    </div>
</div>

<hr>
<h5 class="fw-bold">📦 Observaciones</h5>
<div class="mb-3">
    <textarea name="observaciones" class="form-control" rows="3" placeholder="Ej: Bujías NGK, Filtro de aire, Pastillas de freno..."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
</div>

<hr>
<h5 class="fw-bold">🛞 Llantas (Configuración)</h5>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Del Izq Marca</label>
        <input type="text" name="marca_llanta_del_izq" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_del_izq" class="form-control" placeholder="PSI">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Del Der Marca</label>
        <input type="text" name="marca_llanta_del_der" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_del_der" class="form-control" placeholder="PSI">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Izq Int</label>
        <input type="text" name="marca_llanta_post_izq_int" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_izq_int" class="form-control" placeholder="PSI">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Izq Ext</label>
        <input type="text" name="marca_llanta_post_izq_ext" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_izq_ext" class="form-control" placeholder="PSI">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Der Int</label>
        <input type="text" name="marca_llanta_post_der_int" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_der_int" class="form-control" placeholder="PSI">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Post Der Ext</label>
        <input type="text" name="marca_llanta_post_der_ext" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Presión</label>
        <input type="number" name="presion_llanta_post_der_ext" class="form-control" placeholder="PSI">
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar Vehículo</button>
    <a href="vehiculos.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
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