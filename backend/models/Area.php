<?php
class Area {
    private $conn;
    private $table_name = "areas";

    public $id;
    public $cNombre;
    public $lActivo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las áreas activas
    public function obtenerTodas() {
        $query = "SELECT id, cNombre FROM " . $this->table_name . " 
                  WHERE lActivo = 1 ORDER BY cNombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener área por ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id AND lActivo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>