<?php
require_once __DIR__ . '/../../../backend/controllers/AuthController.php';

// Cerrar sesión
$authController = new AuthController();
$authController->logout();

// Redirigir al login
header('Location: login.php');
exit();
?>