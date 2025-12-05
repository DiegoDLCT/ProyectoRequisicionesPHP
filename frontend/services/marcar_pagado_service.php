<?php
session_start();
require_once __DIR__ . '/../../backend/utils/auth.php';
require_once __DIR__ . '/../../backend/controllers/PagoController.php';

verificarSesion();
if (!tieneRol(['contaduria'])) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para realizar esta acción.';
    header('Location: ../index.php?error=denegado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_requisicion'])) {
    $pagoController = new PagoController();
    
    if ($pagoController->marcarPagado($_POST['id_requisicion'])) {
        $_SESSION['mensaje_exito'] = 'Pago confirmado correctamente.';
    } else {
        $_SESSION['mensaje_error'] = 'Error al confirmar el pago.';
    }
    header('Location: ../views/pagos/confirmar.php');
} else {
    $_SESSION['mensaje_error'] = 'Solicitud inválida.';
    header('Location: ../views/pagos/confirmar.php');
}
exit;
?>
