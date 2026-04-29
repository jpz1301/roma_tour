<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = intval($_POST['id']);
    $accion = $_POST['accion'];

    if ($accion == "iniciar") {
        $result = pg_query_params($conexion, 
            "UPDATE programacion_servicio SET estado = 'En ruta' WHERE id = $1", 
            [$id]);

    } elseif ($accion == "finalizar") {
        $km_llegada = intval($_POST['km_llegada']);

        // Obtener km_salida
        $res = pg_query_params($conexion, 
            "SELECT km_salida FROM programacion_servicio WHERE id = $1", 
            [$id]);
        $row = pg_fetch_assoc($res);

        $km_salida = intval($row['km_salida']);
        $km_recorrido = $km_llegada - $km_salida;

        $result = pg_query_params($conexion, 
            "UPDATE programacion_servicio 
             SET estado = 'Finalizado', km_llegada = $1, km_recorrido = $2 
             WHERE id = $3", 
            [$km_llegada, $km_recorrido, $id]);
    }

    if ($result) {
        echo "ok";
    } else {
        echo "error";
    }
}
?>