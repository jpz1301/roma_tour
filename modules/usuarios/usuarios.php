<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// 🔒 Solo admin y boss pueden acceder
if (!in_array($_SESSION['rol'], ['admin', 'boss'])) {
    header("Location: ../../index.php");
    exit();
}

$mensaje = '';
$tipo = '';

// ====================== CREAR NUEVO USUARIO ======================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_usuario    = trim($_POST['usuario'] ?? '');
    $nombre_completo  = trim($_POST['nombre_completo'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $rol              = $_POST['rol'] ?? '';

    if (empty($nuevo_usuario) || empty($nombre_completo) || empty($email) || empty($password) || empty($rol)) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Por favor ingresa un correo electrónico válido.";
        $tipo = "danger";
    } else {
        $check = pg_query_params($conexion, 
            "SELECT id FROM usuarios WHERE usuario = $1 OR email = $2", 
            [$nuevo_usuario, $email]);
        
        if (pg_num_rows($check) > 0) {
            $mensaje = "El nombre de usuario o el correo ya están registrados.";
            $tipo = "danger";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $result = pg_query_params($conexion, 
                "INSERT INTO usuarios (usuario, nombre_completo, email, password, rol) VALUES ($1, $2, $3, $4, $5)", 
                [$nuevo_usuario, $nombre_completo, $email, $password_hash, $rol]);

            if ($result) {
                $mensaje = "✅ Usuario creado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "❌ Error al crear el usuario.";
                $tipo = "danger";
            }
        }
    }
}

// Lista de usuarios
$result = pg_query($conexion, "SELECT id, usuario, nombre_completo, email, rol FROM usuarios ORDER BY rol, usuario");
$lista = $result ? pg_fetch_all($result) : [];

// Estadísticas
$stats = pg_fetch_assoc(pg_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN rol='admin' THEN 1 ELSE 0 END) as admins,
    SUM(CASE WHEN rol='boss' THEN 1 ELSE 0 END) as bosses,
    SUM(CASE WHEN rol='pro' THEN 1 ELSE 0 END) as pros,
    SUM(CASE WHEN rol='driver' THEN 1 ELSE 0 END) as drivers
    FROM usuarios"));

// Configurar includes
$titulo = 'Usuarios | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Gestión de Usuarios';
// Sin botón nuevo porque se crea desde el formulario

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTÓN VOLVER -->
<div class="container mb-3">
    <a href="../../index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver al Menú
    </a>
</div>

<div class="container mb-5">

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card total">
        <div class="stat-icon">👥</div>
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Total Usuarios</div>
    </div>
    <div class="stat-card" style="border-bottom:3px solid #dc3545;">
        <div class="stat-icon">🔴</div>
        <div class="stat-number" style="color:#dc3545;"><?= $stats['admins'] ?></div>
        <div class="stat-label">Admins</div>
    </div>
    <div class="stat-card" style="border-bottom:3px solid #ffc107;">
        <div class="stat-icon">🟡</div>
        <div class="stat-number" style="color:#b8860b;"><?= $stats['bosses'] ?></div>
        <div class="stat-label">Boss</div>
    </div>
    <div class="stat-card" style="border-bottom:3px solid #0d6efd;">
        <div class="stat-icon">🔵</div>
        <div class="stat-number" style="color:#0d6efd;"><?= $stats['pros'] ?></div>
        <div class="stat-label">Pro</div>
    </div>
    <div class="stat-card" style="border-bottom:3px solid #198754;">
        <div class="stat-icon">🟢</div>
        <div class="stat-number" style="color:#198754;"><?= $stats['drivers'] ?></div>
        <div class="stat-label">Drivers</div>
    </div>
</div>

<?php if ($mensaje): ?>
    <div class="alert alert-<?= $tipo ?> d-flex align-items-center gap-2">
        <i class="bi bi-<?= $tipo == 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= $mensaje ?>
    </div>
<?php endif; ?>

<!-- FORMULARIO CREAR USUARIO -->
<div class="main-card mb-4">
<div class="card-header" style="background: linear-gradient(135deg, #6f42c1, #5a339e);">
    <h4><i class="bi bi-person-plus-fill"></i> Crear Nuevo Usuario</h4>
</div>
<div class="card-body">
    <form method="POST" class="row g-3">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Usuario</label>
            <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Nombre Completo</label>
            <input type="text" name="nombre_completo" class="form-control" placeholder="Nombre completo" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Correo Electrónico</label>
            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
        </div>
        <div class="col-md-1">
            <label class="form-label fw-semibold">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="">—</option>
                <option value="admin">Admin</option>
                <option value="boss">Boss</option>
                <option value="pro">Pro</option>
                <option value="driver">Driver</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary" style="background:var(--morado);border:none;">
                <i class="bi bi-person-plus"></i> Crear Usuario
            </button>
        </div>
    </form>
</div>
</div>

<!-- LISTA DE USUARIOS -->
<div class="main-card">
<div class="card-header" style="background: linear-gradient(135deg, #dc3545, #b02a37);">
    <h4><i class="bi bi-people-fill"></i> Usuarios Registrados</h4>
</div>
<div class="card-body">

<?php if (!empty($lista)): ?>
<div class="table-responsive">
<table id="tabla" class="table table-hover">
<thead>
<tr>
    <th>Usuario</th>
    <th>Nombre Completo</th>
    <th>Email</th>
    <th>Rol</th>
</tr>
</thead>
<tbody>
<?php foreach ($lista as $u): 
    $color = match($u['rol']) {
        'admin' => 'danger',
        'boss' => 'warning',
        'pro' => 'primary',
        'driver' => 'success',
        default => 'secondary'
    };
?>
<tr>
    <td><strong><?= htmlspecialchars($u['usuario']) ?></strong></td>
    <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
    <td><i class="bi bi-envelope" style="color:#888;"></i> <?= htmlspecialchars($u['email'] ?? 'Sin correo') ?></td>
    <td><span class="badge bg-<?= $color ?>"><?= strtoupper($u['rol']) ?></span></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="text-center py-5">
    <i class="bi bi-people" style="font-size:5rem;color:#ccc;"></i>
    <h4 class="text-muted mt-3">No hay usuarios registrados</h4>
</div>
<?php endif; ?>

</div></div>
</div>

<?php include("../../includes/footer.php"); ?>