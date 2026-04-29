<?php
// Detectar si estamos en Railway o en local
if (getenv('PGHOST')) {
    // Railway (online)
    $host = getenv('PGHOST');
    $port = getenv('PGPORT');
    $dbname = getenv('PGDATABASE');
    $user = getenv('PGUSER');
    $password = getenv('PGPASSWORD');
} else {
    // XAMPP (local)
    $host = "localhost";
    $port = "5433";
    $dbname = "pequena_roma";
    $user = "postgres";
    $password = "123456";
}

$conexion = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if(!$conexion){
    echo "Error de conexión a PostgreSQL";
}
?>