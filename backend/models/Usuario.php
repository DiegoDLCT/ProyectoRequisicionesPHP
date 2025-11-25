<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $cNombre;
    public $cCorreo;
    public $cContrasena;
    public $cPuesto;
    public $idArea;
    public $lActivo;
    public $cTokenInvitacion;
    public $dExpiracionToken;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Buscar usuario por email
    public function buscarPorEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE cCorreo = :email AND lActivo = 1 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar login
    public function verificarLogin($email, $password) {
        $usuario = $this->buscarPorEmail($email);
        
        if ($usuario && password_verify($password, $usuario['cContrasena'])) {
            return $usuario;
        }
        return false;
    }

    // Crear nuevo usuario (para invitaciones)
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET cNombre=:nombre, cCorreo=:correo, cContrasena=:contrasena,
                      cPuesto=:puesto, idArea=:id_area, cTokenInvitacion=:token";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar y hashear contraseña
        $this->cNombre = htmlspecialchars(strip_tags($this->cNombre));
        $this->cCorreo = htmlspecialchars(strip_tags($this->cCorreo));
        $hashed_password = password_hash($this->cContrasena, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":nombre", $this->cNombre);
        $stmt->bindParam(":correo", $this->cCorreo);
        $stmt->bindParam(":contrasena", $hashed_password);
        $stmt->bindParam(":puesto", $this->cPuesto);
        $stmt->bindParam(":id_area", $this->idArea);
        $stmt->bindParam(":token", $this->cTokenInvitacion);
        
        return $stmt->execute();
    }
}
?>