<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$rol     = $_SESSION['rol'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pequeña Roma Tours | Sistema de Gestión</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
:root {
    --rojo-principal: #c8102e;
    --rojo-oscuro: #a00d24;
    --azul: #0d6efd;
    --naranja: #fd7e14;
    --verde: #198754;
    --morado: #6f42c1;
}

body {
    background: linear-gradient(135deg, #f0f2f5 0%, #e4e8ec 100%);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    transition: all 0.3s ease;
    min-height: 100vh;
}

/* HEADER */
.header {
    background: linear-gradient(135deg, var(--rojo-principal) 0%, var(--rojo-oscuro) 100%);
    color: white;
    padding: 2.5rem 0 2rem;
    text-align: center;
    position: relative;
    box-shadow: 0 8px 32px rgba(200,16,46,0.2);
    overflow: hidden;
}

.header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size: 30px 30px;
    pointer-events: none;
}

.logo {
    max-width: 200px;
    margin: 0 auto 1.2rem;
    display: block;
    border-radius: 12px;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
    transition: transform 0.3s ease;
}

.logo:hover {
    transform: scale(1.05);
}

.title {
    font-size: 2.5rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 400;
    letter-spacing: 1px;
}

/* USER BOX */
.user-box {
    position: absolute;
    top: 20px;
    right: 25px;
    z-index: 10;
}

.user-btn {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.user-btn:hover {
    background: rgba(255,255,255,0.25);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.user-info small {
    font-size: 0.7rem;
    opacity: 0.8;
}

.rol-badge {
    background: rgba(255,255,255,0.2);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* DROPDOWN */
.dropdown-menu {
    border-radius: 14px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    padding: 8px;
    min-width: 220px;
}

.dropdown-item {
    border-radius: 10px;
    padding: 10px 15px;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: #fce4ec;
}

.dropdown-item.text-danger:hover {
    background: #fce4ec;
}

/* DARK MODE BUTTON */
.btn-dark-mode {
    position: absolute;
    top: 20px;
    left: 25px;
    z-index: 10;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    border-radius: 12px;
    padding: 8px 14px;
    transition: all 0.3s ease;
}

.btn-dark-mode:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* MAIN */
.main-container {
    margin-top: -30px;
    position: relative;
    z-index: 5;
}

/* MENU CARD */
.menu-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    padding: 2.5rem 2rem;
    max-width: 700px;
    margin: 0 auto;
}

.menu-title {
    font-weight: 700;
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.menu-title::before,
.menu-title::after {
    content: '';
    flex: 1;
    height: 2px;
    background: #eee;
    border-radius: 1px;
}

/* BOTONES */
.menu-btn {
    font-size: 1.1rem;
    padding: 1rem 1.5rem;
    border-radius: 14px;
    margin-bottom: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.menu-btn::after {
    content: '';
    position: absolute;
    right: 20px;
    font-family: 'bootstrap-icons';
    content: '\F285';
    font-size: 1rem;
    opacity: 0;
    transition: all 0.3s ease;
}

.menu-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.menu-btn:hover::after {
    opacity: 0.6;
    right: 15px;
}

.menu-btn i {
    font-size: 1.4rem;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.25);
}

.btn-usuarios { background: var(--morado); color: white; }
.btn-usuarios:hover { background: #5a339e; color: white; }

.btn-vehiculos { background: var(--rojo-principal); color: white; }
.btn-vehiculos:hover { background: #a00d24; color: white; }

.btn-conductores { background: var(--azul); color: white; }
.btn-conductores:hover { background: #0b5ed7; color: white; }

.btn-mantenimiento { background: var(--naranja); color: white; }
.btn-mantenimiento:hover { background: #e06d0a; color: white; }

.btn-incidencias { background: #dc3545; color: white; }
.btn-incidencias:hover { background: #b02a37; color: white; }

.btn-programacion { background: var(--verde); color: white; }
.btn-programacion:hover { background: #157347; color: white; }

.btn-bloqueado {
    background: var(--morado);
    color: white;
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-bloqueado:hover {
    transform: none;
    box-shadow: none;
}

/* DECORACIÓN */
.decoracion-linea {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 1.5rem 0;
    color: #aaa;
    font-size: 0.85rem;
}

.decoracion-linea::before,
.decoracion-linea::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e0e0e0;
}

/* FOOTER */
.footer {
    background: #1a1a2e;
    color: rgba(255,255,255,0.7);
    text-align: center;
    padding: 1.2rem;
    margin-top: 2rem;
}

.footer small {
    font-size: 0.85rem;
}

.footer .separador {
    color: rgba(255,255,255,0.3);
    margin: 0 10px;
}

/* 🌙 MODO OSCURO */
body.dark-mode {
    background: linear-gradient(135deg, #121212 0%, #1a1a2e 100%);
    color: #e4e4e4;
}

body.dark-mode .header {
    background: linear-gradient(135deg, #1f1f1f, #2c2c2c);
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}

body.dark-mode .menu-card {
    background: #1e1e1e;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

body.dark-mode .menu-title {
    color: #eee;
}

body.dark-mode .menu-title::before,
body.dark-mode .menu-title::after {
    background: #333;
}

body.dark-mode .decoracion-linea {
    color: #555;
}

body.dark-mode .decoracion-linea::before,
body.dark-mode .decoracion-linea::after {
    background: #333;
}

body.dark-mode .dropdown-menu {
    background: #1e1e1e;
    color: white;
}

body.dark-mode .dropdown-item {
    color: #ddd;
}

body.dark-mode .dropdown-item:hover {
    background: #2c2c2c;
}

body.dark-mode .footer {
    background: #111;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .header {
        padding: 2rem 0 1.5rem;
    }
    
    .title {
        font-size: 1.8rem;
    }
    
    .menu-card {
        padding: 1.5rem 1rem;
        border-radius: 20px;
    }
    
    .menu-btn {
        font-size: 1rem;
        padding: 0.8rem 1rem;
    }
    
    .menu-btn i {
        width: 35px;
        height: 35px;
        font-size: 1.1rem;
    }
    
    .user-btn {
        padding: 6px 10px;
    }
    
    .user-btn .user-info {
        display: none;
    }
    
    .btn-dark-mode {
        top: 10px;
        left: 10px;
        padding: 6px 10px;
    }
    
    .user-box {
        top: 10px;
        right: 10px;
    }
}
</style>
</head>

<body class="d-flex flex-column min-vh-100">

<!-- HEADER -->
<header class="header">

<!-- BOTÓN MODO OSCURO -->
<button onclick="toggleDarkMode()" class="btn btn-dark-mode" title="Modo oscuro">
    <i class="bi bi-moon-stars-fill"></i>
</button>

<!-- USUARIO -->
<div class="user-box dropdown">

<a class="user-btn dropdown-toggle" href="#" data-bs-toggle="dropdown">
    <div class="user-avatar">
        <i class="bi bi-person-fill"></i>
    </div>
    <div class="user-info">
        <div style="font-weight:600;"><?= htmlspecialchars($usuario) ?></div>
        <span class="rol-badge"><?= strtoupper($rol) ?></span>
    </div>
</a>

<ul class="dropdown-menu dropdown-menu-end shadow">

<li class="px-3 py-2">
    <div class="d-flex align-items-center gap-3">
        <div class="user-avatar" style="background:var(--rojo-principal);width:35px;height:35px;font-size:1rem;">
            <i class="bi bi-person-fill"></i>
        </div>
        <div>
            <div style="font-weight:600;font-size:0.9rem;"><?= htmlspecialchars($usuario) ?></div>
            <small class="text-muted"><?= strtoupper($rol) ?></small>
        </div>
    </div>
</li>

<li><hr class="dropdown-divider"></li>

<li>
    <a class="dropdown-item d-flex align-items-center gap-2" href="#">
        <i class="bi bi-gear"></i> Configuración
    </a>
</li>

<li>
    <a class="dropdown-item text-danger d-flex align-items-center gap-2"
       href="#"
       onclick="confirmarCerrarSesion()">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
</li>

</ul>

</div>

<div class="container">
    <img src="assets/image/logo_Pr.png" class="logo" alt="Pequeña Roma Tours">
    <h1 class="title">SISTEMA DE GESTIÓN</h1>
    <p class="subtitle">Pequeña Roma Tours S.R.L.</p>
</div>

</header>

<!-- MAIN -->
<main class="flex-fill">
<div class="container main-container">

<div class="menu-card">

<h2 class="menu-title">📋 MENÚ PRINCIPAL</h2>

<!-- USUARIOS -->
<?php if ($rol == 'admin'): ?>
<a href="modules/usuarios/usuarios.php" class="menu-btn btn-usuarios w-100 text-decoration-none">
    <i class="bi bi-people-fill"></i>
    <span>Usuarios del Sistema</span>
</a>
<?php else: ?>
<button class="menu-btn btn-bloqueado w-100" onclick="alertaSinPermiso()">
    <i class="bi bi-lock-fill"></i>
    <span>Usuarios (Solo Admin)</span>
</button>
<?php endif; ?>

<!-- VEHÍCULOS -->
<a href="modules/vehiculos/vehiculos.php" class="menu-btn btn-vehiculos w-100 text-decoration-none">
    <i class="bi bi-bus-front-fill"></i>
    <span>Vehículos</span>
</a>

<!-- CONDUCTORES -->
<a href="modules/conductores/conductores.php" class="menu-btn btn-conductores w-100 text-decoration-none">
    <i class="bi bi-person-badge-fill"></i>
    <span>Conductores</span>
</a>

<div class="decoracion-linea">OPERACIONES</div>

<!-- MANTENIMIENTO -->
<a href="modules/mantenimientos/listar_mantenimiento.php" class="menu-btn btn-mantenimiento w-100 text-decoration-none">
    <i class="bi bi-tools"></i>
    <span>Mantenimiento</span>
</a>

<!-- INCIDENCIAS -->
<a href="modules/incidencia/incidencias.php" class="menu-btn btn-incidencias w-100 text-decoration-none">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>Incidencias</span>
</a>

<!-- PROGRAMACIÓN -->
<a href="modules/programacion_servicio/listar_programacion.php" class="menu-btn btn-programacion w-100 text-decoration-none">
    <i class="bi bi-calendar-check-fill"></i>
    <span>Programación de Servicios</span>
</a>

</div>

</div>
</main>

<!-- FOOTER -->
<footer class="footer">
<div class="container">
<small>
    © <?= date('Y') ?> Pequeña Roma Tours S.R.L.
    <span class="separador d-none d-md-inline">|</span>
    <br class="d-md-none">
    Cusco - Perú
    <span class="separador d-none d-md-inline">|</span>
    <br class="d-md-none">
    Todos los derechos reservados
</small>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// 🌙 MODO OSCURO
function toggleDarkMode() {
    const btn = document.querySelector('.btn-dark-mode i');
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        localStorage.setItem("darkMode", "on");
        btn.className = 'bi bi-sun-fill';
    } else {
        localStorage.setItem("darkMode", "off");
        btn.className = 'bi bi-moon-stars-fill';
    }
}

window.onload = function() {
    if (localStorage.getItem("darkMode") === "on") {
        document.body.classList.add("dark-mode");
        document.querySelector('.btn-dark-mode i').className = 'bi bi-sun-fill';
    }
}

// 🔒 ALERTA PERMISO
function alertaSinPermiso() {
    Swal.fire({
        icon: 'warning',
        title: 'Acceso restringido',
        text: 'Solo los administradores pueden acceder a este módulo',
        confirmButtonColor: '#c8102e'
    });
}

// 🔒 LOGOUT
function confirmarCerrarSesion() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: "Tu sesión actual se cerrará",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c8102e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-box-arrow-right"></i> Sí, salir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "logout.php";
        }
    });
}
</script>

</body>
</html>