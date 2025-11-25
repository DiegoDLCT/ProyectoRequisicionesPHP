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
        return $this->usuario->verificarLogin($email, $password);
    }
    
    public function logout() {
        session_start();
        session_destroy();
        return true;
    }
}
?>