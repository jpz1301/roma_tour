<?php
include("../../config/conexion.php");

if(isset($_GET['id'])){

    $id = (int) $_GET['id'];

    $sql = "DELETE FROM inspecciones_vehiculo 
            WHERE id_inspeccion = $id";

    $result = pg_query($conexion, $sql);

    if($result){
        header("Location: inspecciones.php");
        exit();
    } else {
        echo "Error al eliminar: " . pg_last_error($conexion);
    }

} else {
    echo "ID no válido";
}
?>