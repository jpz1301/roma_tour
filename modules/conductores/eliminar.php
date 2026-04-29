<?php
include("../../config/conexion.php");


$id = intval($_GET['id']);

// 1. Borrar registros relacionados
pg_query($conexion, "DELETE FROM inspecciones_vehiculo WHERE id_conductor = $id");

// 2. Ahora sí borrar conductor
pg_query($conexion, "DELETE FROM conductores WHERE id_conductor = $id");

header("Location: conductores.php");
exit();