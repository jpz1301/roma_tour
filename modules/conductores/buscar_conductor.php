<?php
include("../../config/conexion.php");

$dni = $_GET['dni'] ?? '';

if (!empty($dni)) {
    $sql = "SELECT nombre FROM conductores WHERE dni = $1 LIMIT 1";
    $result = pg_query_params($conexion, $sql, [$dni]);

    if ($row = pg_fetch_assoc($result)) {
        echo $row['nombre'];
    } else {
        echo "";
    }
}
?>