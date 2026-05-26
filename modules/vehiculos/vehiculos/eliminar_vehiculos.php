<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    // Verificar si tiene inspecciones
    $verificar = pg_query_params(
        $conexion,
        "SELECT 1 FROM inspecciones_vehiculo WHERE id_vehiculo = $1",
        [$id]
    );

    if ($verificar && pg_num_rows($verificar) > 0) {
        echo "<script>
                alert('No puedes eliminar este vehículo porque tiene inspecciones registradas');
                window.location='vehiculos.php';
              </script>";
        exit();
    }

    // Verificar si tiene mantenimientos
    $verificar2 = pg_query_params(
        $conexion,
        "SELECT 1 FROM mantenimiento WHERE vehiculo_id = $1",
        [$id]
    );

    if ($verificar2 && pg_num_rows($verificar2) > 0) {
        echo "<script>
                alert('No puedes eliminar este vehículo porque tiene mantenimientos registrados');
                window.location='vehiculos.php';
              </script>";
        exit();
    }

    // Eliminar
    $result = pg_query_params($conexion, "DELETE FROM vehiculos WHERE id_vehiculo = $1", [$id]);

    if ($result) {
        header("Location: vehiculos.php");
        exit();
    } else {
        echo "Error al eliminar: " . pg_last_error($conexion);
    }
} else {
    echo "ID no recibido";
}
?>