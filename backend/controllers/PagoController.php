<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Requisicion.php';

class PagoController {
    private $db;
    private $requisicion;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->requisicion = new Requisicion($this->db);
    }

    // Jefe mayor solicita pago
    public function solicitarPago($id_requisicion) {
        return $this->requisicion->cambiarEstado($id_requisicion, 'pago_solicitado');
    }

    // Contaduría marca como pagado
    public function marcarPagado($id_requisicion) {
        return $this->requisicion->cambiarEstado($id_requisicion, 'pagado');
    }

    // Admin marca como por entregar
    public function marcarPorEntregar($id_requisicion) {
        return $this->requisicion->cambiarEstado($id_requisicion, 'por_entregar');
    }

    // Admin marca como entregado (finaliza ciclo)
    public function marcarEntregado($id_requisicion) {
        return $this->requisicion->cambiarEstado($id_requisicion, 'entregado');
    }

    // Obtener requisiciones por estado
    public function obtenerPorEstado($estado) {
        return $this->requisicion->obtenerPorEstado($estado);
    }

    // Obtener requisiciones por estado con cotización
    public function obtenerPorEstadoConCotizacion($estado) {
        return $this->requisicion->obtenerPorEstadoConCotizacion($estado);
    }
}
?>
