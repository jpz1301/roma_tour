<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Función auxiliar para convertir a entero o 0 si viene vacío
function toIntOrZero($value) {
    return ($value !== '' && $value !== null) ? (int)$value : 0;
}

// Función auxiliar para convertir a entero o NULL si viene vacío (para IDs opcionales)
function toIntOrNull($value) {
    return ($value !== '' && $value !== null) ? (int)$value : null;
}

// --- Recibir y normalizar campos ---
$id = $_POST['id'] ?? '';
$id = ($id !== '' && $id !== null) ? (int)$id : '';  // Si es vacío, queda '' (para el INSERT)

$nombre = $_POST['nombre'] ?? '';
$dni = $_POST['dni'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$licencia = $_POST['licencia'] ?? '';
$estado = $_POST['estado'] ?? '';
$fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
$dias_libres = toIntOrZero($_POST['dias_libres'] ?? '');
$dias_salidas = toIntOrZero($_POST['dias_salidas'] ?? '');
$direccion = $_POST['direccion'] ?? '';
$telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
$tipo_contrato = $_POST['tipo_contrato'] ?? '';
$vacaciones = toIntOrZero($_POST['vacaciones'] ?? '');

// Convertir fecha_ingreso vacía a NULL
$fecha_ingreso = ($fecha_ingreso !== '' && $fecha_ingreso !== null) ? $fecha_ingreso : null;

// --- Construir consulta según si es edición o nuevo ---
if ($id != "") {
    // EDITAR
    $sql = "UPDATE conductores SET 
            nombre=$1, dni=$2, telefono=$3, licencia=$4, estado=$5,
            fecha_ingreso=$6, dias_libres=$7, dias_salidas=$8,
            direccion=$9, telefono_emergencia=$10, tipo_contrato=$11, vacaciones=$12
            WHERE id_conductor=$13";
    $params = [$nombre, $dni, $telefono, $licencia, $estado, $fecha_ingreso, 
               $dias_libres, $dias_salidas, $direccion, $telefono_emergencia, 
               $tipo_contrato, $vacaciones, $id];
} else {
    // NUEVO
    $sql = "INSERT INTO conductores (nombre, dni, telefono, licencia, estado, fecha_ingreso, 
                                      dias_libres, dias_salidas, direccion, telefono_emergencia, 
                                      tipo_contrato, vacaciones)
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12)";
    $params = [$nombre, $dni, $telefono, $licencia, $estado, $fecha_ingreso, 
               $dias_libres, $dias_salidas, $direccion, $telefono_emergencia, 
               $tipo_contrato, $vacaciones];
}

// Ejecutar consulta
$result = pg_query_params($conexion, $sql, $params);

if (!$result) {
    die("Error: " . pg_last_error($conexion));
}

// Redirigir al listado
header("Location: conductores.php");
exit();
?>