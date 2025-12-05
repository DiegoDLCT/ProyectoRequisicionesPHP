<?php
class Proveedor {
    private $conn;
    private $table = 'proveedores';

    public $id;
    public $cNombre;
    public $cCorreo;
    public $cTelefono;
    public $cDireccion;
    public $cCiudad;
    public $cPais;
    public $cContacto;
    public $dFechaRegistro;
    public $lActivo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los proveedores activos
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table . " WHERE lActivo = 1 ORDER BY cNombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener proveedor por ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND lActivo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo proveedor
    public function crear() {
        $query = "INSERT INTO " . $this->table . "
                  (cNombre, cCorreo, cTelefono, cDireccion, cCiudad, cPais, cContacto)
                  VALUES (:nombre, :correo, :telefono, :direccion, :ciudad, :pais, :contacto)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar
        $this->cNombre = htmlspecialchars(strip_tags($this->cNombre));
        $this->cCorreo = htmlspecialchars(strip_tags($this->cCorreo));
        $this->cTelefono = htmlspecialchars(strip_tags($this->cTelefono));
        $this->cDireccion = htmlspecialchars(strip_tags($this->cDireccion));
        $this->cCiudad = htmlspecialchars(strip_tags($this->cCiudad));
        $this->cPais = htmlspecialchars(strip_tags($this->cPais));
        $this->cContacto = htmlspecialchars(strip_tags($this->cContacto));
        
        $stmt->bindParam(':nombre', $this->cNombre);
        $stmt->bindParam(':correo', $this->cCorreo);
        $stmt->bindParam(':telefono', $this->cTelefono);
        $stmt->bindParam(':direccion', $this->cDireccion);
        $stmt->bindParam(':ciudad', $this->cCiudad);
        $stmt->bindParam(':pais', $this->cPais);
        $stmt->bindParam(':contacto', $this->cContacto);
        
        return $stmt->execute();
    }

    // Actualizar proveedor
    public function actualizar() {
        $query = "UPDATE " . $this->table . "
                  SET cNombre = :nombre, cCorreo = :correo, cTelefono = :telefono,
                      cDireccion = :direccion, cCiudad = :ciudad, cPais = :pais,
                      cContacto = :contacto
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->cNombre = htmlspecialchars(strip_tags($this->cNombre));
        $this->cCorreo = htmlspecialchars(strip_tags($this->cCorreo));
        $this->cTelefono = htmlspecialchars(strip_tags($this->cTelefono));
        $this->cDireccion = htmlspecialchars(strip_tags($this->cDireccion));
        $this->cCiudad = htmlspecialchars(strip_tags($this->cCiudad));
        $this->cPais = htmlspecialchars(strip_tags($this->cPais));
        $this->cContacto = htmlspecialchars(strip_tags($this->cContacto));
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nombre', $this->cNombre);
        $stmt->bindParam(':correo', $this->cCorreo);
        $stmt->bindParam(':telefono', $this->cTelefono);
        $stmt->bindParam(':direccion', $this->cDireccion);
        $stmt->bindParam(':ciudad', $this->cCiudad);
        $stmt->bindParam(':pais', $this->cPais);
        $stmt->bindParam(':contacto', $this->cContacto);
        
        return $stmt->execute();
    }

    // Desactivar proveedor
    public function desactivar($id) {
        $query = "UPDATE " . $this->table . " SET lActivo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
