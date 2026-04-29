<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $placa = $_POST['placa'];
    $conductor = $_POST['conductor'];
    $fecha = $_POST['fecha'];
    $tipo = $_POST['tipo'];
    $incidencia = $_POST['incidencia'];
    $costo = $_POST['costo'] ?? null;
    $tipo_servicio = $_POST['tipo_servicio'] ?? 'propio';
    $proveedor = $_POST['proveedor'] ?? null;
    $tipo_unidad = $_POST['tipo_unidad'] ?? null;

    $costo = ($costo !== '' && $costo !== null) ? floatval($costo) : null;

    $sql = "UPDATE incidencias 
            SET placa=$1, conductor=$2, fecha=$3, tipo=$4, incidencia=$5,
                costo=$6, tipo_servicio=$7, proveedor=$8, tipo_unidad=$9
            WHERE id_incidencia=$10";

    $result = pg_query_params($conexion, $sql, [
        $placa, $conductor, $fecha, $tipo, $incidencia,
        $costo, $tipo_servicio, $proveedor, $tipo_unidad, $id
    ]);

    if ($result) {
        header("Location: incidencias.php");
        exit();
    } else {
        echo "Error: " . pg_last_error($conexion);
    }
}
?>