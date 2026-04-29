<?php
include("../../config/conexion.php");

$dni = $_GET['dni'] ?? '';

$sql = "SELECT nombre FROM conductores WHERE dni = '$dni' LIMIT 1";
$result = pg_query($conexion, $sql);

if ($row = pg_fetch_assoc($result)) {
    echo $row['nombre'];
} else {
    echo "";
}
?>