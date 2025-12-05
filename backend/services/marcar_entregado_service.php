<?php
session_start();
$base_path = dirname(__FILE__, 2);
require_once $base_path . '/config/database.php';
require_once $base_path . '/utils/auth.php';

// Validar sesión
if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = 'Sesión no válida';
    header('Location: /ProyectoPHP/frontend/views/pagos/finalizar_entregas.php');
    exit();
}

// Validar rol (solo admin)
if (!tieneRol('admin')) {
    $_SESSION['mensaje_error'] = 'Acceso denegado';
    header('Location: /ProyectoPHP/frontend/views/pagos/finalizar_entregas.php');
    exit();
}

// Validar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje_error'] = 'Solicitud no válida';
    header('Location: /ProyectoPHP/frontend/views/pagos/finalizar_entregas.php');
    exit();
}

// Obtener ID de requisición
$id_requisicion = $_POST['id_requisicion'] ?? null;

if (!$id_requisicion) {
    $_SESSION['mensaje_error'] = 'ID de requisición no proporcionado';
    header('Location: /ProyectoPHP/frontend/views/pagos/finalizar_entregas.php');
    exit();
}

try {
    $db = (new Database())->getConnection();
    
    // Cambiar estado de por_entregar a entregado
    $query = "UPDATE requisiciones SET estado = 'entregado' WHERE id = :id AND estado = 'por_entregar'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_requisicion);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = 'Requisición marcada como entregada';
    } else {
        $_SESSION['mensaje_error'] = 'Error al marcar la requisición como entregada';
    }
} catch (Exception $e) {
    $_SESSION['mensaje_error'] = 'Error: ' . $e->getMessage();
}

header('Location: /ProyectoPHP/frontend/views/pagos/finalizar_entregas.php');
exit();
?>
