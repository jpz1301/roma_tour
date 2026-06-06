<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

function v($campo){
    return $_POST[$campo] ?? 'B';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $code     = $_POST['code'] ?? '';
    $placa    = $_POST['placa'] ?? '';
    $marca    = $_POST['marca'] ?? '';
    $modelo   = $_POST['modelo'] ?? '';
    $edicion  = $_POST['edicion'] ?? '';
    $asientos = $_POST['asientos'] ?? '';
    $estado   = $_POST['estado'] ?? 'Activo';
    $soat     = $_POST['soat'] ?? '';
    $soat_fecha_vencimiento = $_POST['soat_fecha_vencimiento'] ?? null;

    // Validación
    if ($code == "" || $placa == "" || $marca == "" || $modelo == "" || $edicion == "" || $asientos == "") {
        echo "<script>alert('Todos los campos son obligatorios'); window.history.back();</script>";
        exit();
    }

    // Validar duplicado
    $verificar = pg_query_params($conexion, "SELECT 1 FROM vehiculos WHERE code=$1", [$code]);
    if (pg_num_rows($verificar) > 0) {
        echo "<script>alert('El código ya existe'); window.history.back();</script>";
        exit();
    }

    // Preparar campos de presión de llantas (vacío = NULL)
    $p_del_izq = $_POST['presion_llanta_del_izq'] !== '' ? intval($_POST['presion_llanta_del_izq']) : null;
    $p_del_der = $_POST['presion_llanta_del_der'] !== '' ? intval($_POST['presion_llanta_del_der']) : null;
    $p_post_izq_int = $_POST['presion_llanta_post_izq_int'] !== '' ? intval($_POST['presion_llanta_post_izq_int']) : null;
    $p_post_izq_ext = $_POST['presion_llanta_post_izq_ext'] !== '' ? intval($_POST['presion_llanta_post_izq_ext']) : null;
    $p_post_der_int = $_POST['presion_llanta_post_der_int'] !== '' ? intval($_POST['presion_llanta_post_der_int']) : null;
    $p_post_der_ext = $_POST['presion_llanta_post_der_ext'] !== '' ? intval($_POST['presion_llanta_post_der_ext']) : null;

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
             llanta_repuesto, aceite_motor, refrigerante, aceite_direccion, observaciones,
             marca_llanta_del_izq, presion_llanta_del_izq,
             marca_llanta_del_der, presion_llanta_del_der,
             marca_llanta_post_izq_int, presion_llanta_post_izq_int,
             marca_llanta_post_izq_ext, presion_llanta_post_izq_ext,
             marca_llanta_post_der_int, presion_llanta_post_der_int,
             marca_llanta_post_der_ext, presion_llanta_post_der_ext) 
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,NULL,NULL,
             $10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23,
             $24,$25,$26,$27,$28,$29,$30,$31,$32,$33,$34,
             $35,$36,$37,$38,$39,
             $40,$41,$42,$43,$44,$45,$46,$47,$48,$49,$50,$51)
            RETURNING id_vehiculo";

    $params = [
        $code, $placa, $marca, $modelo, $edicion, $asientos, $estado,
        $soat, $soat_fecha_vencimiento,
        v('espejo_derecho'), v('espejo_izquierdo'), v('claxon'), v('antena'),
        v('parabrisas_frontal'), v('parabrisas_posterior'),
        v('tapa_combustible'), v('tapa_aceite_motor'), v('tapa_radiator'),
        v('luces_altas'), v('luces_bajas'), v('luces_traseras'), v('luces_freno'), v('luces_intermitentes'),
        v('cinturon'), v('radio'), v('extintor'), v('llave_rueda'),
        v('linterna'), v('gato'), v('aire_forzado'),
        v('alarma'), v('cone_seguridad'), v('suspension'), v('emblemas'),
        $_POST['llanta_repuesto'] ?? '', $_POST['aceite_motor'] ?? '', $_POST['refrigerante'] ?? '', $_POST['aceite_direccion'] ?? '', $_POST['observaciones'] ?? '',
        $_POST['marca_llanta_del_izq'] ?? '', $p_del_izq,
        $_POST['marca_llanta_del_der'] ?? '', $p_del_der,
        $_POST['marca_llanta_post_izq_int'] ?? '', $p_post_izq_int,
        $_POST['marca_llanta_post_izq_ext'] ?? '', $p_post_izq_ext,
        $_POST['marca_llanta_post_der_int'] ?? '', $p_post_der_int,
        $_POST['marca_llanta_post_der_ext'] ?? '', $p_post_der_ext
    ];

    $resultado = pg_query_params($conexion, $sql, $params);

    if ($resultado) {
        echo "<script>alert('Vehículo guardado correctamente'); window.location='vehiculos.php';</script>";
    } else {
        echo "Error al guardar: " . pg_last_error($conexion);
    }
} else {
    echo "Acceso no permitido";
}
?>