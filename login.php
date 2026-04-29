<?php
session_start();
include("config/conexion.php");

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        // REGISTRO
        $usuario        = trim($_POST['usuario'] ?? '');
        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $password       = $_POST['password'] ?? '';
        $rol            = $_POST['rol'] ?? 'pro';

        if (empty($usuario) || empty($nombre_completo) || empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Ingresa un correo electrónico válido.";
        } else {
            $check = pg_query_params($conexion, "SELECT id FROM usuarios WHERE usuario=$1 OR email=$2", [$usuario, $email]);
            if (pg_num_rows($check) > 0) {
                $error = "El usuario o correo ya están registrados.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $result = pg_query_params($conexion, "INSERT INTO usuarios (usuario,nombre_completo,email,password,rol) VALUES ($1,$2,$3,$4,$5)", [$usuario, $nombre_completo, $email, $hash, $rol]);
                if ($result) {
                    $success = "✅ Registro exitoso. Ahora puedes iniciar sesión.";
                } else {
                    $error = "Error al registrar.";
                }
            }
        }
    } else {
        // LOGIN
        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($usuario) || empty($password)) {
            $error = "Completa todos los campos.";
        } else {
            $result = pg_query_params($conexion, "SELECT usuario,password,rol FROM usuarios WHERE usuario=$1", [$usuario]);
            if ($result && pg_num_rows($result) > 0) {
                $data = pg_fetch_assoc($result);
                if (password_verify($password, $data['password']) || md5($password) === $data['password']) {
                    session_regenerate_id(true);
                    $_SESSION['usuario'] = $data['usuario'];
                    $_SESSION['rol']     = $data['rol'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Usuario o contraseña incorrectos.";
                }
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión | Pequeña Roma Tours</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root { --rojo: #c8102e; --rojo-oscuro: #a00d24; --morado: #6f42c1; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    background: linear-gradient(135deg, #1a0000 0%, #3d0c0c 40%, #5c1010 100%);
    min-height: 100vh; display: flex; align-items: center; justify-content: center;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; padding: 20px;
    position: relative; overflow: hidden;
}
body::before, body::after {
    content: ''; position: absolute; background: rgba(255,255,255,0.03); border-radius: 50%; pointer-events: none;
}
body::before { width: 600px; height: 600px; top: -200px; right: -200px; }
body::after { width: 400px; height: 400px; bottom: -150px; left: -150px; }
.login-wrapper { position: relative; z-index: 1; width: 100%; max-width: 440px; }
.logo-container { text-align: center; margin-bottom: 20px; }
.logo-img { width: 140px; border-radius: 12px; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.4)); transition: transform 0.3s ease; }
.logo-img:hover { transform: scale(1.05); }
.empresa-nombre { color: white; font-size: 1.1rem; font-weight: 600; margin-top: 8px; text-shadow: 0 2px 4px rgba(0,0,0,0.3); letter-spacing: 1px; }
.card {
    border: none; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    background: white; padding: 2.5rem 2rem; animation: slideUp 0.5s ease;
}
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.card-title { color: #1a1a2e; font-weight: 700; font-size: 1.5rem; margin-bottom: 0.3rem; }
.card-subtitle { color: #888; font-size: 0.9rem; margin-bottom: 1.5rem; }
.input-group-text { background: transparent; border-right: none; color: #aaa; padding-left: 15px; }
.form-control { border-left: none; padding: 12px 15px; border-radius: 12px; font-size: 0.95rem; transition: all 0.3s ease; }
.form-control:focus { border-color: var(--rojo); box-shadow: 0 0 0 0.25rem rgba(200,16,46,0.1); }
.input-group { margin-bottom: 1rem; }
.input-group .form-control { border-radius: 0 12px 12px 0; }
.input-group-text { border-radius: 12px 0 0 12px; border: 1px solid #dee2e6; border-right: none; }
.btn-login {
    background: linear-gradient(135deg, var(--rojo), var(--rojo-oscuro)); border: none; font-weight: 700;
    padding: 14px; border-radius: 14px; font-size: 1.05rem; letter-spacing: 0.5px; color: white; transition: all 0.3s ease;
}
.btn-login:hover { background: linear-gradient(135deg, var(--rojo-oscuro), #8b0a1e); transform: translateY(-2px); box-shadow: 0 10px 25px rgba(200,16,46,0.4); }
.forgot-link { color: var(--rojo); text-decoration: none; font-size: 0.9rem; font-weight: 500; }
.forgot-link:hover { color: var(--rojo-oscuro); text-decoration: underline; }
.divider { display: flex; align-items: center; text-align: center; color: #bbb; font-size: 0.85rem; margin: 1.5rem 0; }
.divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e0e0e0; }
.divider span { padding: 0 15px; background: white; }
.btn-register {
    background: var(--morado); border: none; font-weight: 600; padding: 14px; border-radius: 14px;
    font-size: 0.95rem; color: white; transition: all 0.3s ease;
}
.btn-register:hover { background: #5a339e; transform: translateY(-2px); box-shadow: 0 10px 25px rgba(111,66,193,0.3); color: white; }
.alert { border-radius: 12px; font-weight: 500; font-size: 0.9rem; border: none; animation: shake 0.4s ease; }
@keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-5px)} 75%{transform:translateX(5px)} }
.alert-danger { background: #fce4ec; color: #c62828; }
.alert-success { background: #d1fae5; color: #065f46; }
.modal-content { border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { background: linear-gradient(135deg, var(--rojo), var(--rojo-oscuro)); color: white; border-radius: 20px 20px 0 0; padding: 1.2rem 1.5rem; border: none; }
.modal-title { font-weight: 700; display: flex; align-items: center; gap: 8px; }
.modal-header .btn-close { filter: brightness(0) invert(1); }
.modal-body { padding: 1.5rem; }
.modal-body .form-control, .modal-body .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #dee2e6; margin-bottom: 0.8rem; }
.modal-body .form-control:focus, .modal-body .form-select:focus { border-color: var(--rojo); box-shadow: 0 0 0 0.25rem rgba(200,16,46,0.1); }
.modal-footer { border-top: 1px solid #f0f0f0; padding: 1rem 1.5rem 1.5rem; }
.btn-cancel { background: #f0f0f0; color: #555; border: none; font-weight: 600; border-radius: 12px; padding: 10px 20px; }
.btn-submit-register { background: linear-gradient(135deg, var(--rojo), var(--rojo-oscuro)); color: white; border: none; font-weight: 700; border-radius: 12px; padding: 10px 25px; }
.btn-submit-register:hover { background: linear-gradient(135deg, var(--rojo-oscuro), #8b0a1e); }
.footer-text { text-align: center; color: rgba(255,255,255,0.7); margin-top: 20px; font-size: 0.85rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3); }
@media (max-width: 480px) { .card { padding: 1.8rem 1.2rem; border-radius: 20px; } .logo-img { width: 110px; } .card-title { font-size: 1.3rem; } }
</style>
</head>
<body>

<div class="login-wrapper">
    <div class="logo-container">
        <img src="assets/image/logo_Pr.png" alt="Pequeña Roma Tours" class="logo-img">
        <p class="empresa-nombre">PEQUEÑA ROMA TOURS S.R.L.</p>
    </div>

    <div class="card">
        <h4 class="card-title text-center"><i class="bi bi-box-arrow-in-right" style="color:var(--rojo);"></i> Iniciar Sesión</h4>
        <p class="card-subtitle text-center">Ingresa tus credenciales para acceder</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="login">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" required autofocus>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-login w-100 mt-3"><i class="bi bi-box-arrow-in-right"></i> Ingresar al Sistema</button>
        </form>

        <div class="text-center mt-3">
            <a href="recuperar_contraseña.php" class="forgot-link"><i class="bi bi-question-circle"></i> ¿Olvidaste tu contraseña?</a>
        </div>
        <div class="divider"><span>o</span></div>
        <button class="btn btn-register w-100" data-bs-toggle="modal" data-bs-target="#registerModal"><i class="bi bi-person-plus-fill"></i> Crear Nueva Cuenta</button>
    </div>

    <p class="footer-text">© <?= date('Y') ?> Pequeña Roma Tours S.R.L. | Cusco - Perú</p>
</div>

<!-- MODAL REGISTRO -->
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill"></i> Crear Nueva Cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="register">
                    <div class="input-group mb-3"><span class="input-group-text"><i class="bi bi-person"></i></span><input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required></div>
                    <div class="input-group mb-3"><span class="input-group-text"><i class="bi bi-person-vcard"></i></span><input type="text" name="nombre_completo" class="form-control" placeholder="Nombre completo" required></div>
                    <div class="input-group mb-3"><span class="input-group-text"><i class="bi bi-envelope"></i></span><input type="email" name="email" class="form-control" placeholder="Correo electrónico" required></div>
                    <div class="input-group mb-3"><span class="input-group-text"><i class="bi bi-lock"></i></span><input type="password" name="password" class="form-control" placeholder="Contraseña" required></div>
                    <label class="form-label fw-semibold"><i class="bi bi-shield-check"></i> Tipo de usuario</label>
                    <select name="rol" class="form-select" required>
                        <option value="pro">🔧 Pro (Administrador)</option>
                        <option value="driver">🚌 Driver (Conductor)</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                    <button type="submit" class="btn btn-submit-register"><i class="bi bi-check-circle"></i> Registrarse</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if (!empty($error) && isset($_POST['action']) && $_POST['action']==='register'): ?>
document.addEventListener('DOMContentLoaded',function(){new bootstrap.Modal(document.getElementById('registerModal')).show();});
<?php endif; ?>
<?php if (!empty($success)): ?>
document.addEventListener('DOMContentLoaded',function(){var m=bootstrap.Modal.getInstance(document.getElementById('registerModal'));if(m)m.hide();});
<?php endif; ?>
</script>
</body>
</html>