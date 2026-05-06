<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id            = intval($_POST['id'] ?? 0);
$vehiculo_id   = intval($_POST['vehiculo_id'] ?? 0);
$mecanico      = $_POST['mecanico'] ?? '';
$taller        = $_POST['taller'] ?? '';
$tipo          = $_POST['tipo'] ?? '';
$fecha         = $_POST['fecha'] ?? '';
$responsable   = $_POST['responsable'] ?? '';
$problema      = $_POST['problema'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';

$costo = $_POST['costo'] ?? '';
$costo = ($costo !== '' && $costo !== null) ? floatval($costo) : null;

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