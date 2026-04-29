<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = $_POST['id'] ?? '';

$nombre = $_POST['nombre'] ?? '';
$dni = $_POST['dni'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$licencia = $_POST['licencia'] ?? '';
$estado = $_POST['estado'] ?? '';
$fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
$dias_libres = $_POST['dias_libres'] ?? 0;
$dias_salidas = $_POST['dias_salidas'] ?? 0;
$direccion = $_POST['direccion'] ?? '';
$telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
$tipo_contrato = $_POST['tipo_contrato'] ?? '';
$vacaciones = $_POST['vacaciones'] ?? 0;

// Convertir vacíos a NULL
$fecha_ingreso = ($fecha_ingreso !== '' && $fecha_ingreso !== null) ? $fecha_ingreso : null;

if ($id != "") {
    // EDITAR
    $sql = "UPDATE conductores SET 
            nombre=$1, dni=$2, telefono=$3, licencia=$4, estado=$5,
            fecha_ingreso=$6, dias_libres=$7, dias_salidas=$8,
            direccion=$9, telefono_emergencia=$10, tipo_contrato=$11, vacaciones=$12
            WHERE id_conductor=$13";
    $params = [$nombre, $dni, $telefono, $licencia, $estado, $fecha_ingreso, $dias_libres, $dias_salidas, $direccion, $telefono_emergencia, $tipo_contrato, $vacaciones, $id];
} else {
    // NUEVO
    $sql = "INSERT INTO conductores (nombre, dni, telefono, licencia, estado, fecha_ingreso, dias_libres, dias_salidas, direccion, telefono_emergencia, tipo_contrato, vacaciones)
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12)";
    $params = [$nombre, $dni, $telefono, $licencia, $estado, $fecha_ingreso, $dias_libres, $dias_salidas, $direccion, $telefono_emergencia, $tipo_contrato, $vacaciones];
}

$result = pg_query_params($conexion, $sql, $params);

if (!$result) {
    die("Error: " . pg_last_error($conexion));
}

header("Location: conductores.php");
exit();
?>