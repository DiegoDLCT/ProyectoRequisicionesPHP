<?php
session_start();
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../controllers/PagoController.php';

if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje_error'] = 'No autorizado.';
    header('Location: /ProyectoPHP/frontend/index.php');
    exit;
}

if (!tieneRol('jefe_mayor') && !tieneRol('admin')) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para realizar esta acción.';
    header('Location: /ProyectoPHP/frontend/index.php?error=denegado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_requisicion'])) {
    $pagoController = new PagoController();
    
    if ($pagoController->solicitarPago($_POST['id_requisicion'])) {
        $_SESSION['mensaje_exito'] = 'Pago solicitado correctamente.';
        header('Location: /ProyectoPHP/frontend/views/pagos/solicitar.php');
    } else {
        $_SESSION['mensaje_error'] = 'Error al solicitar el pago.';
        header('Location: /ProyectoPHP/frontend/views/pagos/solicitar.php');
    }
} else {
    $_SESSION['mensaje_error'] = 'Solicitud inválida.';
    header('Location: /ProyectoPHP/frontend/views/pagos/solicitar.php');
}
exit;
?>
