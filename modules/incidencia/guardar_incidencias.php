<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Mostrar qué llega por POST (para depurar)
    // echo "<pre>"; print_r($_POST); echo "</pre>"; exit();

    $tipo_servicio = $_POST['tipo_servicio'] ?? 'propio';
    $proveedor     = $_POST['proveedor'] ?? null;
    $tipo_unidad   = $_POST['tipo_unidad'] ?? null;

    $placa_select  = $_POST['placa'] ?? '';
    $placa_manual  = $_POST['placa_manual'] ?? '';

    $conductor  = $_POST['conductor'] ?? '';
    $fecha      = $_POST['fecha'] ?? '';
    $tipo       = $_POST['tipo'] ?? '';
    $incidencia = $_POST['incidencia'] ?? '';
    $costo      = $_POST['costo'] ?? 0;

    // Lógica de placa según tipo de servicio
    if ($tipo_servicio == "tercerizado") {
        $placa = $placa_manual;
    } else {
        $placa = $placa_select;
        $proveedor = null;
        $tipo_unidad = null;
    }

    // Validar campos obligatorios
    if (empty($placa) || empty($conductor) || empty($fecha) || empty($tipo) || empty($incidencia)) {
        die("Error: Faltan campos obligatorios. Placa: '$placa', Conductor: '$conductor', Fecha: '$fecha', Tipo: '$tipo', Incidencia: '$incidencia'");
    }

    // Convertir costo
    if ($costo === '' || $costo === null) {
        $costo = null;
    } else {
        $costo = floatval($costo);
    }

    $sql = "INSERT INTO incidencias 
            (placa, conductor, fecha, tipo, incidencia, costo, proveedor, tipo_servicio, tipo_unidad)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";

    $result = pg_query_params($conexion, $sql, [
        $placa, $conductor, $fecha, $tipo, $incidencia, $costo, $proveedor, $tipo_servicio, $tipo_unidad
    ]);

    if ($result) {
        header("Location: incidencias.php");
        exit();
    } else {
        die("Error SQL: " . pg_last_error($conexion));
    }
} else {
    echo "Acceso no permitido";
}
?>