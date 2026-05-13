<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id            = intval($_POST['id'] ?? 0);
$vehiculo_id   = intval($_POST['vehiculo_id'] ?? 0);
$mecanico      = $_POST['mecanico'] ?? '';
$taller        = $_POST['taller'] ?? '';
$tipo          = $_POST['tipo'] ?? '';
$fecha         = $_POST['fecha'] ?? '';
$responsable_id = $_POST['responsable_id'] ?? ''; // ✅ Cambiado: ID del conductor
$problema      = $_POST['problema'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$costo         = $_POST['costo'] ?? '';
$costo = ($costo !== '' && $costo !== null) ? floatval($costo) : null;

// 🔍 Obtener el nombre del conductor a partir del ID
$responsable = '';
if ($responsable_id != '') {
    $query_nombre = pg_query_params($conexion, "SELECT nombre FROM conductores WHERE id_conductor = $1", [$responsable_id]);
    if ($row = pg_fetch_assoc($query_nombre)) {
        $responsable = $row['nombre'];
    } else {
        echo "<script>alert('Conductor no válido'); window.history.back();</script>";
        exit();
    }
}

// Validar campos obligatorios (ahora $responsable se obtiene del ID)
if ($id == 0 || $vehiculo_id == 0 || $mecanico == "" || $taller == "" || $tipo == "" || $fecha == "" || $responsable == "" || $problema == "") {
    echo "<script>alert('Complete todos los campos obligatorios'); window.history.back();</script>";
    exit();
}

$sql = "UPDATE mantenimiento SET
        vehiculo_id = $1, mecanico_id = $2, taller_id = $3,
        tipo = $4, fecha = $5, responsable = $6,
        problema = $7, observaciones = $8, costo = $9
        WHERE id = $10";

$result = pg_query_params($conexion, $sql, [
    $vehiculo_id, $mecanico, $taller,
    $tipo, $fecha, $responsable,
    $problema, $observaciones, $costo, $id
]);

if ($result) {
    // Actualizar estado del vehículo a "Mantenimiento"
    pg_query_params($conexion, "UPDATE vehiculos SET estado = 'Mantenimiento' WHERE id_vehiculo = $1", [$vehiculo_id]);
    
    header("Location: listar_mantenimiento.php");
    exit();
} else {
    echo "Error al actualizar: " . pg_last_error($conexion);
}
?>