<?php
include("../../config/conexion.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 🔹 FUNCIÓN SEGURA
    function v($campo){
        return $_POST[$campo] ?? null;
    }

    // 🔹 DATOS GENERALES
    $id_vehiculo = v('id_vehiculo');
    $id_conductor = v('id_conductor');
    $fecha = v('fecha_inspeccion');

    // 🔥 DOCUMENTACIÓN
    $soat = v('soat');
    $revision_tecnica = v('revision_tecnica');
    $manifiesto = v('manifiesto_pasajeros');

    // 🔹 RUTA
    $ruta = v('ruta');
    $tipo_servicio = v('tipo_servicio');

    // 🔹 CONTROL
    $hora_salida = v('hora_salida');
    $hora_llegada = v('hora_llegada');
    $km_salida = v('km_salida');
    $km_llegada = v('km_llegada');

    $combustible_salida = v('combustible_salida');
    $combustible_llegada = v('combustible_llegada');

    $pax = v('pax') ?? 0;
    $observaciones = v('observaciones');

    // 🔹 RESPONSABLE
    $revisado_por = v('revisado_por');
    $dni_revisor = v('dni_revisor');

    // 🔹 ESTADO
    $estado_general = v('estado_general');

    // 🔹 LLANTAS
    $marca_llanta_del_izq = v('marca_llanta_del_izq');
    $presion_llanta_del_izq = v('presion_llanta_del_izq');

    $marca_llanta_del_der = v('marca_llanta_del_der');
    $presion_llanta_del_der = v('presion_llanta_del_der');

    $marca_llanta_post_izq_int = v('marca_llanta_post_izq_int');
    $presion_llanta_post_izq_int = v('presion_llanta_post_izq_int');

    $marca_llanta_post_izq_ext = v('marca_llanta_post_izq_ext');
    $presion_llanta_post_izq_ext = v('presion_llanta_post_izq_ext');

    $marca_llanta_post_der_int = v('marca_llanta_post_der_int');
    $presion_llanta_post_der_int = v('presion_llanta_post_der_int');

    $marca_llanta_post_der_ext = v('marca_llanta_post_der_ext');
    $presion_llanta_post_der_ext = v('presion_llanta_post_der_ext');

    // 🔥 INSERT COMPLETO
    $sql = "INSERT INTO inspecciones_vehiculo (

    id_vehiculo, id_conductor, fecha_inspeccion,
    soat, revision_tecnica, manifiesto_pasajeros,

    hora_salida, hora_llegada,
    km_salida, km_llegada,
    combustible_salida, combustible_llegada,
    pax, observaciones,

    espejo_derecho, espejo_izquierdo, claxon, antena,
    parabrisas_frontal, parabrisas_posterior,
    tapa_combustible, tapa_aceite_motor, tapa_radiator,

    luces_altas, luces_bajas, luces_traseras, luces_freno, luces_intermitentes,

    cinturon, radio, extintor, llanta_repuesto, llave_rueda,
    linterna, gato, aire_forzado,
    aceite_motor, refrigerante, aceite_direccion,
    alarma, cone_seguridad, suspension, emblemas,

    marca_llanta_del_izq, presion_llanta_del_izq,
    marca_llanta_del_der, presion_llanta_del_der,

    marca_llanta_post_izq_int, presion_llanta_post_izq_int,
    marca_llanta_post_izq_ext, presion_llanta_post_izq_ext,

    marca_llanta_post_der_int, presion_llanta_post_der_int,
    marca_llanta_post_der_ext, presion_llanta_post_der_ext,

    ruta, tipo_servicio, estado_general,
    revisado_por, dni_revisor

    ) VALUES (

    '$id_vehiculo', '$id_conductor', '$fecha',
    '$soat', '$revision_tecnica', '$manifiesto',

    '$hora_salida', '$hora_llegada',
    '$km_salida', '$km_llegada',
    '$combustible_salida', '$combustible_llegada',
    '$pax', '$observaciones',

    '".v('espejo_derecho')."', '".v('espejo_izquierdo')."', '".v('claxon')."', '".v('antena')."',
    '".v('parabrisas_frontal')."', '".v('parabrisas_posterior')."',
    '".v('tapa_combustible')."', '".v('tapa_aceite_motor')."', '".v('tapa_radiator')."',

    '".v('luces_altas')."', '".v('luces_bajas')."', '".v('luces_traseras')."', '".v('luces_freno')."', '".v('luces_intermitentes')."',

    '".v('cinturon')."', '".v('radio')."', '".v('extintor')."', '".v('llanta_repuesto')."', '".v('llave_rueda')."',
    '".v('linterna')."', '".v('gato')."', '".v('aire_forzado')."',
    '".v('aceite_motor')."', '".v('refrigerante')."', '".v('aceite_direccion')."',
    '".v('alarma')."', '".v('cone_seguridad')."', '".v('suspension')."', '".v('emblemas')."',

    '$marca_llanta_del_izq', '$presion_llanta_del_izq',
    '$marca_llanta_del_der', '$presion_llanta_del_der',

    '$marca_llanta_post_izq_int', '$presion_llanta_post_izq_int',
    '$marca_llanta_post_izq_ext', '$presion_llanta_post_izq_ext',

    '$marca_llanta_post_der_int', '$presion_llanta_post_der_int',
    '$marca_llanta_post_der_ext', '$presion_llanta_post_der_ext',

    '$ruta', '$tipo_servicio', '$estado_general',
    '$revisado_por', '$dni_revisor'
    )";

    $result = pg_query($conexion, $sql);

    if ($result) {
        header("Location: inspecciones.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conexion);
    }
}
?>