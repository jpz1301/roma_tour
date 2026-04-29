<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    pg_query_params($conexion, "DELETE FROM incidencias WHERE id_incidencia = $1", [$id]);
}

header("Location: incidencias.php");
exit();
?>