<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuario;
    
    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->usuario = new Usuario($db);
    }
    
    public function login($email, $password) {
        session_start();
        
        // Verificar credenciales
        $usuarioValido = $this->usuario->verificarLogin($email, $password);
    
    if ($usuarioValido) {
        $_SESSION['usuario'] = $usuarioValido;
        
        // Redirección basada en rol - RUTA DINÁMICA
        // Obtiene el nombre de la carpeta del proyecto dinámicamente
        $projectFolder = basename(dirname(dirname(__DIR__)));
        $baseUrl = '/' . $projectFolder;
        if ($usuarioValido['cPuesto'] == 'jefe_mayor') {
            header('Location: ' . $baseUrl . '/frontend/views/dashboard/jefe_mayor.php');
        } else {
            header('Location: ' . $baseUrl . '/frontend/views/dashboard/admin.php');
        }
        exit;
    }
    
    return false;

    }
    
    public function logout() {
        session_start();
        session_destroy();
        return true;
    }
}
?>