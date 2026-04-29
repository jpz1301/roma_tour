<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    pg_query_params($conexion, "DELETE FROM mantenimiento WHERE id = $1", [$id]);
}

header("Location: listar_mantenimiento.php");
exit();
?>