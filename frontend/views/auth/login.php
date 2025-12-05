<?php
session_start();
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $db = (new Database())->getConnection();
    $sql = "SELECT * FROM usuarios WHERE cCorreo = ? AND lActivo = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($password, $usuario['cContrasena'])) {
        $_SESSION['usuario'] = $usuario;
        header('Location: ../../index.php');
        exit();
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión - Sistema de Requisiciones</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 40px 24px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .submit-btn {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error-alert {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .demo-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 14px;
            margin-top: 20px;
            font-size: 12px;
            color: #1e40af;
        }

        .demo-info strong {
            display: block;
            margin-bottom: 4px;
            color: #1e3a8a;
        }

        .demo-info code {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Monaco', 'Courier New', monospace;
        }

        .version-info {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #9ca3af;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 16px;
            }

            .login-header {
                padding: 32px 16px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .login-body {
                padding: 32px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="bi bi-lock"></i> Sistema de Requisiciones</h1>
            </div>

            <div class="login-body">
                <?php if (isset($error)): ?>
                    <div class="error-alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            placeholder="correo@ejemplo.com"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="bi bi-box-arrow-in-right"></i> Ingresar
                    </button>
                </form>
            </div>
        </div>

        <div class="version-info">
            Sistema de Requisiciones v1.0
        </div>
    </div>
</body>
</html>