<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = intval($_GET['id']);

if ($id > 0) {
    // Eliminar el conductor directamente (sin pasar por inspecciones)
    pg_query_params($conexion, "DELETE FROM conductores WHERE id_conductor = $1", [$id]);
}

header("Location: conductores.php");
exit();