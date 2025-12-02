<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Requisicion.php';
require_once __DIR__ . '/../models/Area.php';

class RequisicionController {
    private $requisicion;
    private $area;
    
    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->requisicion = new Requisicion($db);
        $this->area = new Area($db);
    }
    
    // Crear nueva requisición
    public function crearRequisicion($datos, $id_usuario) {
    $this->requisicion->idSolicitante = $id_usuario;
    $this->requisicion->idArea = $datos['id_area'];
    $this->requisicion->cDescripcion = $datos['descripcion'];
    $this->requisicion->bRequiereCotizacion = $datos['requiere_cotizacion'] ?? 0;
    $this->requisicion->cMaquina = $datos['maquina'] ?? null;
    $this->requisicion->cObraUbicacion = $datos['obra_ubicacion'] ?? null;
    
    // Establecer el estado según si requiere cotización
    if (isset($datos['requiere_cotizacion']) && $datos['requiere_cotizacion'] == 1) {
        $this->requisicion->idEstado = 'cotizado'; // O el valor que corresponda en tu sistema
        // Si usas un ID numérico para estados, podría ser algo como:
        // $this->requisicion->idEstado = 2; // donde 2 = "cotizado"
    } else {
        // Si no requiere cotización, establecer el estado por defecto (ej: "pendiente")
        $this->requisicion->idEstado = 'pendiente'; // O el valor por defecto de tu sistema
    }
    
    return $this->requisicion->crear();
}
    
    // Obtener áreas para el dropdown
    public function obtenerAreas() {
        return $this->area->obtenerTodas();
    }
    
    // Obtener requisiciones del usuario
    public function obtenerRequisicionesUsuario($id_usuario) {
        return $this->requisicion->obtenerPorUsuario($id_usuario);
    }

    // Obtener TODAS las requisiciones (para admin)
    public function obtenerTodas() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT r.*, u.cNombre as solicitante_nombre, a.cNombre as area_nombre 
                  FROM requisiciones r
                  LEFT JOIN usuarios u ON r.idSolicitante = u.id
                  LEFT JOIN areas a ON r.idArea = a.id
                  WHERE r.lActivo = 1
                  ORDER BY r.dFechaSolicitud DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener una requisición específica por ID 
    public function obtenerPorId($id) {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT r.*, u.cNombre as solicitante_nombre, a.cNombre as area_nombre 
              FROM requisiciones r
              LEFT JOIN usuarios u ON r.idSolicitante = u.id
              LEFT JOIN areas a ON r.idArea = a.id
              WHERE r.id = :id AND r.lActivo = 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
?>