<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id'] ?? 0);

    if ($id == 0) {
        die("Error: ID inválido");
    }

    $result = pg_query_params($conexion, "DELETE FROM programacion_servicio WHERE id = $1", [$id]);

    echo $result ? "ok" : pg_last_error($conexion);
} else {
    echo "Acceso no permitido";
}
?>