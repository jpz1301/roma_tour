<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $vehiculo_id   = $_POST['vehiculo_id'] ?? '';
    $mecanico      = $_POST['mecanico'] ?? '';
    $taller        = $_POST['taller'] ?? '';
    $tipo          = $_POST['tipo'] ?? '';
    $fecha         = $_POST['fecha'] ?? '';
    $responsable   = $_POST['responsable'] ?? '';
    $problema      = $_POST['problema'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';

    // Convertir costo (vacío = NULL)
    $costo = $_POST['costo'] ?? '';
    $costo = ($costo !== '' && $costo !== null) ? floatval($costo) : null;

    // Validación
    if ($vehiculo_id == "" || $mecanico == "" || $taller == "" || $tipo == "" || $fecha == "" || $responsable == "" || $problema == "") {
        echo "<script>alert('Complete todos los campos obligatorios'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO mantenimiento 
            (vehiculo_id, mecanico_id, taller_id, tipo, fecha, responsable, problema, observaciones, costo)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";

    $result = pg_query_params($conexion, $sql, [
        $vehiculo_id, $mecanico, $taller, $tipo, $fecha, $responsable, $problema, $observaciones, $costo
    ]);

    if ($result) {
        header("Location: listar_mantenimiento.php");
        exit();
    } else {
        echo "Error al guardar: " . pg_last_error($conexion);
    }
}
?>