<?php
// recuperar_contraseña.php

session_start();
include("config/conexion.php");

$mensaje = '';
$tipo = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $mensaje = "Por favor ingresa tu correo electrónico.";
        $tipo = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Por favor ingresa un correo electrónico válido.";
        $tipo = "danger";
    } else {
        // Buscar el usuario por email
        $sql = "SELECT id, usuario, nombre_completo FROM usuarios WHERE email = $1";
        $result = pg_query_params($conexion, $sql, [$email]);

        if ($result && pg_num_rows($result) > 0) {
            $data = pg_fetch_assoc($result);
            
            // Mensaje simulado (más adelante podrás enviar email real)
            $mensaje = "✅ Hemos enviado un enlace de recuperación a:<br>
                        <strong>" . htmlspecialchars($email) . "</strong><br><br>
                        <small>Por favor revisa tu bandeja de entrada y la carpeta de spam.</small>";
            $tipo = "success";
        } else {
            $mensaje = "No encontramos ninguna cuenta registrada con ese correo electrónico.";
            $tipo = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Pequeña Roma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FF6B00 0%, #FF8C00 50%, #FF4500 100%);
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            background: white;
            max-width: 440px;
            margin: auto;
        }
        .btn-primary {
            background: #c8102e;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #a00d24;
        }
        h3 {
            color: #c8102e;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center">

<div class="card p-4">
    <h3 class="text-center mb-4 fw-bold">Recuperar Contraseña</h3>
    
    <p class="text-center text-muted mb-4">
        Ingresa el correo electrónico asociado a tu cuenta.<br>
        Te enviaremos un enlace para restablecer tu contraseña.
    </p>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?= $tipo ?> text-center">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <input type="email" 
                   name="email" 
                   class="form-control form-control-lg" 
                   placeholder="ejemplo@correo.com" 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                   required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
            Enviar enlace de recuperación
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="login.php" class="text-decoration-none" style="color: #c8102e;">
            ← Volver al inicio de sesión
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>