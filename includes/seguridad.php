<?php
session_start();

// Verificar si el usuario inició sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>