<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id         = intval($_POST['id']);
    $codigo     = $_POST['codigo'] ?? '';
    $cliente    = $_POST['cliente'] ?? '';
    $ruta_id    = $_POST['ruta_id'] !== '' ? intval($_POST['ruta_id']) : null;
    $tipo       = $_POST['tipo_servicio'] ?? '';
    $vehiculo   = ($_POST['vehiculo_id'] ?? '') !== '' ? intval($_POST['vehiculo_id']) : null;
    $proveedor  = $_POST['proveedor'] ?? null;
    $placa_ext  = $_POST['placa_externa'] ?? null;
    $conductor  = $_POST['conductor'] ?? '';
    $fecha      = $_POST['fecha'] ?? '';
    $hora       = $_POST['hora'] ?? '';
    $personas   = intval($_POST['cantidad_personas'] ?? 0);
    $km_salida  = intval($_POST['km_salida'] ?? 0);
    $estado     = $_POST['estado'] ?? 'Programado';
    $obs        = $_POST['observaciones'] ?? '';

    if ($tipo == "tercerizado") {
        $vehiculo = null;
    } else {
        $proveedor = null;
        $placa_ext = null;
    }

    $sql = "UPDATE programacion_servicio SET 
            codigo=$1, cliente=$2, ruta_id=$3, tipo_servicio=$4, vehiculo_id=$5,
            proveedor=$6, placa_externa=$7, conductor=$8, fecha=$9, hora=$10,
            cantidad_personas=$11, km_salida=$12, estado=$13, observaciones=$14
            WHERE id=$15";

    $res = pg_query_params($conexion, $sql, [
        $codigo, $cliente, $ruta_id, $tipo, $vehiculo, $proveedor, $placa_ext,
        $conductor, $fecha, $hora, $personas, $km_salida, $estado, $obs, $id
    ]);

    echo $res ? "ok" : "error";
}
?>