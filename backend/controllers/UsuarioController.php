<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $usuario;
    
    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->usuario = new Usuario($db);
    }
    
    // Invitar nuevo usuario (sistema seguro)
    public function invitarUsuario($datos) {
        // Generar contraseña temporal
        $password_temporal = "temp123";
        
        $this->usuario->cNombre = $datos['nombre'];
        $this->usuario->cCorreo = $datos['email'];
        $this->usuario->cContrasena = $password_temporal; // Se hashea en el modelo
        $this->usuario->cPuesto = $datos['puesto'];
        $this->usuario->idArea = $datos['id_area'];
        $this->usuario->cTokenInvitacion = bin2hex(random_bytes(16)); // Token seguro
        
        return $this->usuario->crear();
    }

    // Listar usuarios activos
    public function listarUsuarios() {
        return $this->usuario->obtenerTodosActivos();
    }
}
?>