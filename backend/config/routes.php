<?php
/**
 * Configuración de rutas dinámicas
 * Se detecta automáticamente el nombre de la carpeta del proyecto
 */

// Obtener el nombre de la carpeta del proyecto dinámicamente
$uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$projectFolder = $uriParts[0] ?? '';

// Constantes globales de rutas
define('PROJECT_FOLDER', $projectFolder);
define('BASE_URL', '/' . $projectFolder);
define('BACKEND_URL', BASE_URL . '/backend');
define('FRONTEND_URL', BASE_URL . '/frontend');
define('ASSETS_URL', FRONTEND_URL . '/assets');
define('SERVICES_URL', BACKEND_URL . '/services');

// Rutas del servidor (file system)
define('PROJECT_ROOT', dirname(dirname(__FILE__)));
define('BACKEND_PATH', PROJECT_ROOT . '/backend');
define('FRONTEND_PATH', PROJECT_ROOT . '/frontend');
define('UPLOADS_PATH', PROJECT_ROOT . '/uploads');
?>
