<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Cotizacion.php';
require_once __DIR__ . '/../models/Requisicion.php';

class CotizacionController {
    private $cotizacion;
    private $requisicion;
    
    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->cotizacion = new Cotizacion($db);
        $this->requisicion = new Requisicion($db);
    }
    
    // Subir nueva cotización
    public function subirCotizacion($datos) {
        $this->cotizacion->idRequisicion = $datos['id_requisicion'];
        $this->cotizacion->cProveedor = $datos['proveedor'];
        $this->cotizacion->cNumCotizacion = $datos['num_cotizacion'] ?? null;
        $this->cotizacion->deMonto = $datos['monto'] ?? null;
        $this->cotizacion->dFechaCotizacion = $datos['fecha_cotizacion'] ?? date('Y-m-d');
        $this->cotizacion->iDiasEntrega = $datos['dias_entrega'] ?? null;
        $this->cotizacion->cArchivoURI = $datos['archivo_uri'] ?? null;
        $this->cotizacion->bAprovada = 0; // Por defecto no aprobada
        
        $resultado = $this->cotizacion->crear();
        
        // Si se creó la cotización correctamente, cambiar estado de requisición a 'cotizado'
        if ($resultado) {
            $db = (new Database())->getConnection();
            $sql = "UPDATE requisiciones SET estado = 'cotizado' WHERE id = ? AND estado = 'pendiente'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$datos['id_requisicion']]);
        }
        
        return $resultado;
    }
    
    // Obtener cotizaciones por requisición
    public function obtenerCotizacionesRequisicion($id_requisicion) {
        return $this->cotizacion->obtenerPorRequisicion($id_requisicion);
    }
    
    // Aprobar cotización
    public function aprobarCotizacion($id_cotizacion) {
        return $this->cotizacion->aprobar($id_cotizacion);
    }
    
    // Obtener requisiciones pendientes de cotización
    public function obtenerRequisicionesPendientes() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT r.*, u.cNombre as solicitante_nombre, a.cNombre as area_nombre 
                  FROM requisiciones r
                  LEFT JOIN usuarios u ON r.idSolicitante = u.id
                  LEFT JOIN areas a ON r.idArea = a.id
                  WHERE r.estado = 'pendiente' AND r.lActivo = 1
                  ORDER BY r.dFechaSolicitud DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener requisiciones con cotizaciones para aprobación
    public function obtenerRequisicionesConCotizaciones() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT r.*, u.cNombre as solicitante_nombre, a.cNombre as area_nombre,
        COUNT(c.id) as total_cotizaciones
        FROM requisiciones r
        LEFT JOIN usuarios u ON r.idSolicitante = u.id
        LEFT JOIN areas a ON r.idArea = a.id
        LEFT JOIN cotizaciones c ON r.id = c.idRequisicion
        WHERE r.estado = 'cotizado' AND r.lActivo = 1
        GROUP BY r.id
        HAVING total_cotizaciones > 0
        ORDER BY r.dFechaSolicitud DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        $requisiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Para cada requisición, obtener sus cotizaciones
    foreach ($requisiciones as &$requisicion) {
        $requisicion['cotizaciones'] = $this->obtenerCotizacionesRequisicion($requisicion['id']);
    }
    
    return $requisiciones;
}
}
?>