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
    public $idUnidad;
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
                      idUnidad=:id_unidad, cMaquina=:maquina, cObraUbicacion=:obra_ubicacion, estado=:estado";
        
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
        $stmt->bindParam(":id_unidad", $this->idUnidad);
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
        $query = "SELECT r.*, a.cNombre as area_nombre, u.cNombre as solicitante_nombre,
                  COALESCE(un.cNombre, 'N/A') as unidad_nombre
                  FROM " . $this->table_name . " r
                  LEFT JOIN areas a ON r.idArea = a.id
                  LEFT JOIN usuarios u ON r.idSolicitante = u.id
                  LEFT JOIN unidades un ON r.idUnidad = un.id
                  WHERE r.idSolicitante = :id_usuario AND r.lActivo = 1
                  ORDER BY r.cFolio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cambiar estado de requisición
    public function cambiarEstado($id, $nuevoEstado) {
        $query = "UPDATE " . $this->table_name . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Obtener requisiciones por estado
    public function obtenerPorEstado($estado) {
        $query = "SELECT r.*, 
                         u.cNombre as solicitante_nombre,
                         a.cNombreArea as area_nombre
                  FROM " . $this->table_name . " r
                  LEFT JOIN usuarios u ON r.idSolicitante = u.id
                  LEFT JOIN areas a ON r.idArea = a.id
                  WHERE r.estado = :estado AND r.lActivo = 1
                  ORDER BY r.dFechaSolicitud DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener requisiciones por estado con cotización aprobada
    public function obtenerPorEstadoConCotizacion($estado) {
        $query = "SELECT r.*, 
                         u.cNombre as solicitante_nombre,
                         a.cNombre as area_nombre,
                         c.id as cotizacion_id,
                         c.cArchivourl as cotizacion_archivo,
                         c.deMonto as cotizacion_monto,
                         c.cProveedor as proveedor_nombre
                  FROM " . $this->table_name . " r
                  LEFT JOIN usuarios u ON r.idSolicitante = u.id
                  LEFT JOIN areas a ON r.idArea = a.id
                  LEFT JOIN cotizaciones c ON r.id = c.idRequisicion AND c.bAprovada = 1
                  WHERE r.estado = :estado AND r.lActivo = 1
                  ORDER BY r.dFechaSolicitud DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>