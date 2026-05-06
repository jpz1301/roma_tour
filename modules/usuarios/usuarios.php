<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Solo admin y boss
if (!in_array($_SESSION['rol'], ['admin', 'boss'])) {
    header("Location: ../../index.php");
    exit();
}

$mensaje = '';
$tipo = '';

// ====================== ACCIONES ======================
$accion = $_POST['accion'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // CREAR USUARIO
    if ($accion == 'crear') {
        $nuevo_usuario   = trim($_POST['usuario'] ?? '');
        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $rol             = $_POST['rol'] ?? '';

        if (empty($nuevo_usuario) || empty($nombre_completo) || empty($email) || empty($password) || empty($rol)) {
            $mensaje = "Todos los campos son obligatorios."; $tipo = "danger";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "Correo electrónico inválido."; $tipo = "danger";
        } else {
            $check = pg_query_params($conexion, "SELECT id FROM usuarios WHERE usuario=$1 OR email=$2", [$nuevo_usuario, $email]);
            if (pg_num_rows($check) > 0) {
                $mensaje = "El usuario o correo ya existen."; $tipo = "danger";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $r = pg_query_params($conexion, "INSERT INTO usuarios (usuario,nombre_completo,email,password,rol) VALUES ($1,$2,$3,$4,$5)", [$nuevo_usuario,$nombre_completo,$email,$hash,$rol]);
                $mensaje = $r ? "✅ Usuario creado." : "❌ Error al crear.";
                $tipo = $r ? "success" : "danger";
            }
        }
    }

    // EDITAR ROL
    if ($accion == 'editar_rol') {
        $id  = intval($_POST['id']);
        $rol = $_POST['rol'] ?? '';
        if ($id > 0 && $rol != '') {
            $r = pg_query_params($conexion, "UPDATE usuarios SET rol=$1 WHERE id=$2", [$rol, $id]);
            $mensaje = $r ? "✅ Rol actualizado." : "❌ Error.";
            $tipo = $r ? "success" : "danger";
        }
    }

    // CAMBIAR CONTRASEÑA
    if ($accion == 'cambiar_password') {
        $id = intval($_POST['id']);
        $nueva = $_POST['nueva_password'] ?? '';
        if ($id > 0 && strlen($nueva) >= 4) {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $r = pg_query_params($conexion, "UPDATE usuarios SET password=$1 WHERE id=$2", [$hash, $id]);
            $mensaje = $r ? "✅ Contraseña actualizada." : "❌ Error.";
            $tipo = $r ? "success" : "danger";
        } else {
            $mensaje = "La contraseña debe tener al menos 4 caracteres."; $tipo = "danger";
        }
    }

    // ELIMINAR
    if ($accion == 'eliminar') {
        $id = intval($_POST['id']);
        if ($id > 0) {
            $r = pg_query_params($conexion, "DELETE FROM usuarios WHERE id=$1", [$id]);
            $mensaje = $r ? "🗑️ Usuario eliminado." : "❌ Error.";
            $tipo = $r ? "success" : "danger";
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

$titulo = 'Usuarios | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Gestión de Usuarios';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<div class="container mb-3">
    <a href="../../index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver al Menú</a>
</div>

<div class="container mb-5">

<!-- Estadísticas -->
<div class="stats-row">
    <div class="stat-card total"><div class="stat-icon">👥</div><div class="stat-number"><?= $stats['total'] ?></div><div class="stat-label">Total</div></div>
    <div class="stat-card" style="border-bottom:3px solid #dc3545;"><div class="stat-icon">🔴</div><div class="stat-number" style="color:#dc3545;"><?= $stats['admins'] ?></div><div class="stat-label">Admins</div></div>
    <div class="stat-card" style="border-bottom:3px solid #ffc107;"><div class="stat-icon">🟡</div><div class="stat-number" style="color:#b8860b;"><?= $stats['bosses'] ?></div><div class="stat-label">Boss</div></div>
    <div class="stat-card" style="border-bottom:3px solid #0d6efd;"><div class="stat-icon">🔵</div><div class="stat-number" style="color:#0d6efd;"><?= $stats['pros'] ?></div><div class="stat-label">Pro</div></div>
    <div class="stat-card" style="border-bottom:3px solid #198754;"><div class="stat-icon">🟢</div><div class="stat-number" style="color:#198754;"><?= $stats['drivers'] ?></div><div class="stat-label">Drivers</div></div>
</div>

<?php if ($mensaje): ?>
    <div class="alert alert-<?= $tipo ?> d-flex align-items-center gap-2">
        <i class="bi bi-<?= $tipo=='success'?'check-circle':'exclamation-triangle' ?>-fill"></i> <?= $mensaje ?>
    </div>
<?php endif; ?>

<!-- FORMULARIO CREAR USUARIO -->
<div class="main-card mb-4">
<div class="card-header" style="background: linear-gradient(135deg, #6f42c1, #5a339e);">
    <h4><i class="bi bi-person-plus-fill"></i> Crear Nuevo Usuario</h4>
</div>
<div class="card-body">
    <form method="POST" class="row g-3">
        <input type="hidden" name="accion" value="crear">
        <div class="col-md-3"><label class="form-label fw-semibold">Usuario</label><input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required></div>
        <div class="col-md-3"><label class="form-label fw-semibold">Nombre Completo</label><input type="text" name="nombre_completo" class="form-control" placeholder="Nombre completo" required></div>
        <div class="col-md-3"><label class="form-label fw-semibold">Correo</label><input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required></div>
        <div class="col-md-2"><label class="form-label fw-semibold">Contraseña</label><input type="password" name="password" class="form-control" placeholder="Contraseña" required></div>
        <div class="col-md-1"><label class="form-label fw-semibold">Rol</label><select name="rol" class="form-select" required><option value="">—</option><option value="admin">Admin</option><option value="boss">Boss</option><option value="pro">Pro</option><option value="driver">Driver</option></select></div>
        <div class="col-12"><button type="submit" class="btn btn-primary" style="background:var(--morado);border:none;"><i class="bi bi-person-plus"></i> Crear Usuario</button></div>
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
<thead><tr><th>Usuario</th><th>Nombre Completo</th><th>Email</th><th>Rol</th><th>Acciones</th></tr></thead>
<tbody>
<?php foreach ($lista as $u): 
    $color = match($u['rol']) {
        'admin' => 'danger', 'boss' => 'warning', 'pro' => 'primary', 'driver' => 'success', default => 'secondary'
    };
?>
<tr>
    <td><strong><?= htmlspecialchars($u['usuario']) ?></strong></td>
    <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
    <td><i class="bi bi-envelope" style="color:#888;"></i> <?= htmlspecialchars($u['email'] ?? '—') ?></td>
    <td><span class="badge bg-<?= $color ?>"><?= strtoupper($u['rol']) ?></span></td>
    <td>
        <div class="d-flex gap-1">
            <!-- EDITAR ROL -->
            <button class="btn btn-action btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#editarRol<?= $u['id'] ?>" title="Editar Rol"><i class="bi bi-person-gear"></i></button>
            <!-- CAMBIAR CONTRASEÑA -->
            <button class="btn btn-action btn-view btn-sm" data-bs-toggle="modal" data-bs-target="#cambiarPass<?= $u['id'] ?>" title="Cambiar Contraseña"><i class="bi bi-lock-fill"></i></button>
            <!-- ELIMINAR -->
            <button class="btn btn-action btn-delete btn-sm" data-bs-toggle="modal" data-bs-target="#eliminar<?= $u['id'] ?>" title="Eliminar"><i class="bi bi-trash3-fill"></i></button>
        </div>

        <!-- Modal EDITAR ROL -->
        <div class="modal fade" id="editarRol<?= $u['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-sm"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-warning"><h6 class="modal-title">Editar Rol: <?= htmlspecialchars($u['usuario']) ?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar_rol">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <select name="rol" class="form-select">
                            <option value="admin" <?= $u['rol']=='admin'?'selected':'' ?>>Admin</option>
                            <option value="boss" <?= $u['rol']=='boss'?'selected':'' ?>>Boss</option>
                            <option value="pro" <?= $u['rol']=='pro'?'selected':'' ?>>Pro</option>
                            <option value="driver" <?= $u['rol']=='driver'?'selected':'' ?>>Driver</option>
                        </select>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-warning btn-sm">Guardar</button></div>
                </form>
            </div></div>
        </div>

        <!-- Modal CAMBIAR CONTRASEÑA -->
        <div class="modal fade" id="cambiarPass<?= $u['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-sm"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-info text-white"><h6 class="modal-title">Contraseña: <?= htmlspecialchars($u['usuario']) ?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="cambiar_password">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <input type="password" name="nueva_password" class="form-control" placeholder="Nueva contraseña" minlength="4" required>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-info btn-sm">Guardar</button></div>
                </form>
            </div></div>
        </div>

        <!-- Modal ELIMINAR -->
        <div class="modal fade" id="eliminar<?= $u['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-sm"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-danger text-white"><h6 class="modal-title">Eliminar Usuario</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <p>¿Eliminar a <strong><?= htmlspecialchars($u['usuario']) ?></strong>?</p>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-danger btn-sm">Eliminar</button></div>
                </form>
            </div></div>
        </div>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="text-center py-5"><i class="bi bi-people" style="font-size:5rem;color:#ccc;"></i><h4 class="text-muted mt-3">No hay usuarios registrados</h4></div>
<?php endif; ?>

</div></div>
</div>

<?php include("../../includes/footer.php"); ?>