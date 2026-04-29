<?php
$host = getenv('PGHOST');
$port = getenv('PGPORT');
$dbname = getenv('PGDATABASE');
$user = getenv('PGUSER');
$password = getenv('PGPASSWORD');

$conexion = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if(!$conexion){
    echo "Error de conexión a PostgreSQL";
}
?>