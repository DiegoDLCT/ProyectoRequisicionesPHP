<?php
session_start();
$base_path = dirname(__FILE__, 2);
require_once $base_path . '/config/database.php';
require_once $base_path . '/utils/auth.php';
require_once $base_path . '/models/Proveedor.php';

// Validar sesión
if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = 'Sesión no válida';
    header('Location: /ProyectoPHP/frontend/views/proveedores/listar.php');
    exit();
}

// Validar rol (solo admin)
if (!tieneRol('admin')) {
    $_SESSION['mensaje_error'] = 'Acceso denegado';
    header('Location: /ProyectoPHP/frontend/views/proveedores/listar.php');
    exit();
}

// Validar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje_error'] = 'Solicitud no válida';
    header('Location: /ProyectoPHP/frontend/views/proveedores/listar.php');
    exit();
}

$id = $_POST['id'] ?? null;

if (!$id) {
    $_SESSION['mensaje_error'] = 'ID de proveedor no proporcionado';
    header('Location: /ProyectoPHP/frontend/views/proveedores/listar.php');
    exit();
}

try {
    $db = (new Database())->getConnection();
    $proveedorModel = new Proveedor($db);
    
    if ($proveedorModel->desactivar($id)) {
        $_SESSION['mensaje_exito'] = 'Proveedor eliminado correctamente';
    } else {
        $_SESSION['mensaje_error'] = 'Error al eliminar el proveedor';
    }
} catch (Exception $e) {
    $_SESSION['mensaje_error'] = 'Error: ' . $e->getMessage();
}

header('Location: /ProyectoPHP/frontend/views/proveedores/listar.php');
exit();
?>
