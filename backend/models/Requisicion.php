<?php
class Requisicion {
    private $conn;
    private $table_name = "requisiciones";

    public $id;
    public $cFolio;
    public $dFechaSolicitud;
    public $idSolicitante;
    public $idArea;
    public $cDescripcion;
    public $bRequiereCotizacion;
    public $cMaquina;
    public $cObraUbicacion;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Generar folio automático
    private function generarFolio() {
        $prefix = "REQ";
        $year = date('Y');
        $month = date('m');
        
        // Contar requisiciones del mes
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . 
                 " WHERE YEAR(dFechaSolicitud) = :year AND MONTH(dFechaSolicitud) = :month";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":month", $month);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $numero = $resultado['total'] + 1;
        return $prefix . $year . $month . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    // Crear nueva requisición
    public function crear() {
        // Generar folio automático
        $this->cFolio = $this->generarFolio();
        $this->dFechaSolicitud = date('Y-m-d');
        $this->estado = 'pendiente';

        $query = "INSERT INTO " . $this->table_name . " 
                  SET cFolio=:folio, dFechaSolicitud=:fecha_solicitud, 
                      idSolicitante=:id_solicitante, idArea=:id_area,
                      cDescripcion=:descripcion, bRequiereCotizacion=:requiere_cotizacion,
                      cMaquina=:maquina, cObraUbicacion=:obra_ubicacion, estado=:estado";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->cDescripcion = htmlspecialchars(strip_tags($this->cDescripcion));
        $this->cMaquina = $this->cMaquina ? htmlspecialchars(strip_tags($this->cMaquina)) : null;
        $this->cObraUbicacion = htmlspecialchars(strip_tags($this->cObraUbicacion));
        
        $stmt->bindParam(":folio", $this->cFolio);
        $stmt->bindParam(":fecha_solicitud", $this->dFechaSolicitud);
        $stmt->bindParam(":id_solicitante", $this->idSolicitante);
        $stmt->bindParam(":id_area", $this->idArea);
        $stmt->bindParam(":descripcion", $this->cDescripcion);
        $stmt->bindParam(":requiere_cotizacion", $this->bRequiereCotizacion);
        $stmt->bindParam(":maquina", $this->cMaquina);
        $stmt->bindParam(":obra_ubicacion", $this->cObraUbicacion);
        $stmt->bindParam(":estado", $this->estado);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener requisiciones por usuario
    public function obtenerPorUsuario($id_usuario) {
        $query = "SELECT r.*, a.cNombre as area_nombre 
                  FROM " . $this->table_name . " r
                  LEFT JOIN areas a ON r.idArea = a.id
                  WHERE r.idSolicitante = :id_usuario AND r.lActivo = 1
                  ORDER BY r.dFechaSolicitud DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>