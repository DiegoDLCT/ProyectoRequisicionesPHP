<?php
session_start();

// DEBUG COMPLETO
error_log("=== INICIANDO APROBAR_SERVICE ===");
error_log("POST: " . print_r($_POST, true));
error_log("SESSION: " . print_r($_SESSION, true));

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../controllers/AprobacionController.php';

if (!tieneRol('jefe_mayor')) {
    error_log("❌ ACCESO DENEGADO - No es jefe_mayor");
    header('Location: /ProyectoPHP/frontend/views/auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cotizacion_aprobada'])) {
    $idCotizacion = $_POST['id_cotizacion_aprobada'];
    $idAprobador = $_SESSION['usuario']['id'];
    error_log("📝 Procesando aprobación - Cotización: $idCotizacion, Aprobador: $idAprobador");
    $aprobacionController = new AprobacionController();
    $result = $aprobacionController->aprobarCotizacion($idCotizacion, $idAprobador);
    error_log("📊 Resultado: " . print_r($result, true));
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        // Respuesta para AJAX/fetch
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        // Respuesta para petición normal
        if ($result['success']) {
            $_SESSION['mensaje'] = $result['message'];
            error_log("✅ " . $result['message']);
        } else {
            $_SESSION['error'] = $result['message'];
            error_log("❌ " . $result['message']);
        }
        // Redirigir a la vista correcta usando la carpeta del proyecto
        $projectFolder = basename(dirname(dirname(__DIR__)));
        header("Location: /$projectFolder/frontend/views/dashboard/jefe_mayor.php");
        exit;
    }
} else {
    error_log("❌ DATOS INCOMPLETOS");
    echo "Datos incompletos";
}
?>