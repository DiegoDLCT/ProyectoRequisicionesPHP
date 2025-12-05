<?php
session_start();

// Si no está autenticado, enviar al login
if (!isset($_SESSION['usuario'])) {
    header('Location: auth/login.php');
    exit();
}

require_once __DIR__ . '/../backend/utils/auth.php';

// Redirigir por rol
if (tieneRol('admin')) {
    header('Location: views/dashboard/admin.php');
    exit();
}

if (tieneRol('jefe_mayor')) {
    header('Location: views/dashboard/jefe_mayor.php');
    exit();
}

if (tieneRol('Contaduria')) {
    header('Location: views/dashboard/contaduria.php');
    exit();
}

// Si es jefe de área -> dashboard jefe_area
if (tieneRol('jefe_area')) {
    header('Location: views/dashboard/jefe_area.php');
    exit();
}

// Otros usuarios: listar requisiciones
header('Location: views/requisiciones/listar.php');
exit();
?>