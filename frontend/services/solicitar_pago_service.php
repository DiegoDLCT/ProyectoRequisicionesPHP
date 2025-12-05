<?php
session_start();
require_once __DIR__ . '/../../backend/utils/auth.php';
require_once __DIR__ . '/../../backend/controllers/PagoController.php';

verificarSesion();
if (!tieneRol(['jefe_mayor', 'admin'])) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para realizar esta acción.';
    header('Location: ../index.php?error=denegado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_requisicion'])) {
    $pagoController = new PagoController();
    
    if ($pagoController->solicitarPago($_POST['id_requisicion'])) {
        $_SESSION['mensaje_exito'] = 'Pago solicitado correctamente.';
        header('Location: ../views/dashboard/jefe_mayor.php');
    } else {
        $_SESSION['mensaje_error'] = 'Error al solicitar el pago.';
        header('Location: ../views/pagos/solicitar.php');
    }
} else {
    $_SESSION['mensaje_error'] = 'Solicitud inválida.';
    header('Location: ../views/pagos/solicitar.php');
}
exit;
?>
