<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo      = $_POST['codigo'] ?? '';
    $cliente     = $_POST['cliente'] ?? '';
    $ruta_id     = $_POST['ruta_id'] ?? null;
    $tipo        = $_POST['tipo_servicio'] ?? '';
    $vehiculo    = $_POST['vehiculo_id'] ?? null;
    $proveedor   = $_POST['proveedor'] ?? null;
    $placa_ext   = $_POST['placa_externa'] ?? null;
    $conductor   = $_POST['conductor'] ?? '';
    $fecha       = $_POST['fecha'] ?? '';
    $hora        = $_POST['hora'] ?? '';
    $personas    = intval($_POST['cantidad_personas'] ?? 0);
    $km_salida   = intval($_POST['km_salida'] ?? 0);
    $obs         = $_POST['observaciones'] ?? '';

    // Convertir vacíos a NULL
    $ruta_id   = ($ruta_id !== '' && $ruta_id !== null) ? intval($ruta_id) : null;
    $vehiculo  = ($vehiculo !== '' && $vehiculo !== null) ? intval($vehiculo) : null;

    // Lógica según tipo de servicio
    if ($tipo == "tercerizado") {
        $vehiculo = null;
    } else {
        $proveedor = null;
        $placa_ext = null;
    }

    $sql = "INSERT INTO programacion_servicio 
            (codigo, cliente, ruta_id, tipo_servicio, vehiculo_id, proveedor, placa_externa, 
             conductor, fecha, hora, cantidad_personas, km_salida, observaciones)
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13)";

    $res = pg_query_params($conexion, $sql, [
        $codigo, $cliente, $ruta_id, $tipo, $vehiculo, $proveedor, $placa_ext,
        $conductor, $fecha, $hora, $personas, $km_salida, $obs
    ]);

    echo $res ? "ok" : "error";
}
?>