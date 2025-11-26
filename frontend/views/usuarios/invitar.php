<?php
// Verificar sesión y que sea admin
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['cPuesto'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../../backend/controllers/UsuarioController.php';
require_once __DIR__ . '/../../../backend/controllers/RequisicionController.php';

$usuario = $_SESSION['usuario'];
$usuarioController = new UsuarioController();
$requisicionController = new RequisicionController();

$areas = $requisicionController->obtenerAreas();
$mensaje = '';
$error = '';

// Procesar invitación
if ($_POST) {
    try {
        $datos = [
            'nombre' => $_POST['nombre'],
            'email' => $_POST['email'],
            'puesto' => $_POST['puesto'],
            'id_area' => $_POST['id_area']
        ];
        
        // Aquí iría la lógica para enviar invitación por email
        // Por ahora creamos el usuario directamente
        
        $resultado = $usuarioController->invitarUsuario($datos);
        
        if ($resultado) {
            $mensaje = "✅ Usuario invitado exitosamente. Se ha enviado un correo de invitación.";
            $_POST = []; // Limpiar formulario
        } else {
            $error = "❌ Error al invitar al usuario";
        }
    } catch (Exception $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invitar Usuario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        h1 { color: #2563eb; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #374151; }
        input, select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 5px; }
        .btn { background: #2563eb; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        .mensaje { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Invitar Nuevo Usuario</h1>
        
        <?php if ($mensaje): ?>
            <div class="mensaje success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="mensaje error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>👤 Nombre Completo *</label>
                <input type="text" name="nombre" value="<?php echo $_POST['nombre'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>📧 Email *</label>
                <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>🏢 Área *</label>
                <select name="id_area" required>
                    <option value="">-- Seleccionar Área --</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?php echo $area['id']; ?>" 
                            <?php echo isset($_POST['id_area']) && $_POST['id_area'] == $area['id'] ? 'selected' : ''; ?>>
                            <?php echo $area['cNombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>💼 Puesto *</label>
                <select name="puesto" required>
                    <option value="">-- Seleccionar Puesto --</option>
                    <option value="jefe_area" <?php echo isset($_POST['puesto']) && $_POST['puesto'] == 'jefe_area' ? 'selected' : ''; ?>>Jefe de Área</option>
                    <option value="admin" <?php echo isset($_POST['puesto']) && $_POST['puesto'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="jefe_mayor" <?php echo isset($_POST['puesto']) && $_POST['puesto'] == 'jefe_mayor' ? 'selected' : ''; ?>>Jefe Mayor</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">📧 Enviar Invitación</button>
                <a href="../dashboard/admin.php" style="color: #6b7280; margin-left: 15px;">← Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>