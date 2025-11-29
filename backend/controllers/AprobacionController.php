<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/auth.php';

class AprobacionController {
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
    }
    
public function aprobarCotizacion($idCotizacion, $idAprobador) {
    try {
        error_log("=== INICIANDO APROBACIÓN ===");
        error_log("Cotización: $idCotizacion, Aprobador: $idAprobador");
        
        $this->db->beginTransaction();
        
        // 1. Quitar aprobación previa
        error_log("1. Quitando aprobación previa...");
        $sqlReset = "UPDATE cotizaciones c1
                    JOIN cotizaciones c2 ON c1.idRequisicion = c2.idRequisicion
                    SET c1.bAprovada = 0, c1.idAprobador = NULL, c1.dFechaAprobacion = NULL
                    WHERE c2.id = ? AND c1.bAprovada = 1";
        $stmtReset = $this->db->prepare($sqlReset);
        $stmtReset->execute([$idCotizacion]);
        error_log("✅ Aprobación previa removida");
        
        // 2. Aprobar la cotización seleccionada
        error_log("2. Aprobando cotización $idCotizacion...");
        $sqlAprobar = "UPDATE cotizaciones 
                      SET bAprovada = 1, idAprobador = ?, dFechaAprobacion = NOW()
                      WHERE id = ?";
        $stmtAprobar = $this->db->prepare($sqlAprobar);
        $stmtAprobar->execute([$idAprobador, $idCotizacion]);
        error_log("✅ Cotización aprobada");
        
        // 3. Actualizar estado de la requisición
        error_log("3. Actualizando estado de requisición...");
        $sqlRequisicion = "UPDATE requisiciones 
                          SET estado = 'cotizado' 
                          WHERE id = (SELECT idRequisicion FROM cotizaciones WHERE id = ?)";
        $stmtReq = $this->db->prepare($sqlRequisicion);
        $stmtReq->execute([$idCotizacion]);
        error_log("✅ Estado de requisición actualizado");
        
        $this->db->commit();
        error_log("=== APROBACIÓN EXITOSA ===");
        return ["success" => true, "message" => "Cotización aprobada correctamente"];
        
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("❌ ERROR: " . $e->getMessage());
        return ["success" => false, "message" => "Error: " . $e->getMessage()];
    }
}
    
    public function obtenerAprobacionesPendientes() {
        $sql = "SELECT r.id, r.cFolio, r.cDescripcion, r.estado, u.cNombre as solicitante,
                       COUNT(c.id) as total_cotizaciones
                FROM requisiciones r
                JOIN usuarios u ON r.idSolicitante = u.id
                JOIN cotizaciones c ON r.id = c.idRequisicion
                WHERE c.bAprovada = 0
                GROUP BY r.id
                HAVING total_cotizaciones > 0
                ORDER BY r.dFechaSolicitud DESC";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerCotizacionesPorRequisicion($idRequisicion) {
        $sql = "SELECT * FROM cotizaciones 
               WHERE idRequisicion = ? AND bAprovada = 0
               ORDER BY deMonto ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idRequisicion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>