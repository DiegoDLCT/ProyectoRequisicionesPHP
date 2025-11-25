<?php
// Incluir el controlador de autenticación
require_once __DIR__ . '/../../../backend/controllers/AuthController.php';

$error = '';
if ($_POST && !empty($_POST['email']) && !empty($_POST['password'])) {
    $authController = new AuthController();
    $usuario = $authController->login($_POST['email'], $_POST['password']);
    
    if ($usuario) {
    // Iniciar sesión
    session_start();
    $_SESSION['usuario'] = $usuario;
    
    // Redirigir según el puesto
    switch ($usuario['cPuesto']) {
        case 'admin':
            header('Location: ../dashboard/admin.php');
            break;
        case 'jefe_area':
            header('Location: ../dashboard/jefe_area.php');
            break;
        case 'jefe_mayor':
            header('Location: ../dashboard/jefe_mayor.php');
            break;
        default:
            header('Location: ../dashboard/admin.php');
    }
    exit();
}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistema de Requisiciones</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            margin: 0; 
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h1 { 
            color: #2563eb; 
            text-align: center; 
            margin-bottom: 30px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #1d4ed8;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔐 Iniciar Sesión</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required value="admin@admin.com">
            <input type="password" name="password" placeholder="Contraseña" required value="admin123">
            <button type="submit">Ingresar al Sistema</button>
        </form>
    </div>
</body>
</html>