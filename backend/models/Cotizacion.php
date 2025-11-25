<?php
class Cotizacion {
    private $conn;
    private $table_name = "cotizaciones";

    public $id;
    public $idRequisicion;
    public $cProveedor;
    public $cNumCotizacion;
    public $deMonto;
    public $dFechaCotizacion;
    public $cArchivoURI;
    public $bAprovada;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nueva cotización
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET idRequisicion=:id_requisicion, cProveedor=:proveedor, 
                      cNumCotizacion=:num_cotizacion, deMonto=:monto,
                      dFechaCotizacion=:fecha_cotizacion, cArchivoURI=:archivo_uri,
                      bAprovada=:aprovada";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->cProveedor = htmlspecialchars(strip_tags($this->cProveedor));
        $this->cNumCotizacion = $this->cNumCotizacion ? htmlspecialchars(strip_tags($this->cNumCotizacion)) : null;
        $this->cArchivoURI = $this->cArchivoURI ? htmlspecialchars(strip_tags($this->cArchivoURI)) : null;
        
        $stmt->bindParam(":id_requisicion", $this->idRequisicion);
        $stmt->bindParam(":proveedor", $this->cProveedor);
        $stmt->bindParam(":num_cotizacion", $this->cNumCotizacion);
        $stmt->bindParam(":monto", $this->deMonto);
        $stmt->bindParam(":fecha_cotizacion", $this->dFechaCotizacion);
        $stmt->bindParam(":archivo_uri", $this->cArchivoURI);
        $stmt->bindParam(":aprovada", $this->bAprovada);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener cotizaciones por requisición
    public function obtenerPorRequisicion($id_requisicion) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE idRequisicion = :id_requisicion 
                  ORDER BY dFechaCotizacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_requisicion", $id_requisicion);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aprobar una cotización
    public function aprobar($id_cotizacion) {
        // Primero, desaprobar todas las cotizaciones de esta requisición
        $query = "UPDATE " . $this->table_name . " 
                  SET bAprovada = 0 
                  WHERE idRequisicion = (SELECT idRequisicion FROM " . $this->table_name . " WHERE id = :id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_cotizacion);
        $stmt->execute();

        // Luego, aprobar la cotización seleccionada
        $query = "UPDATE " . $this->table_name . " 
                  SET bAprovada = 1 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_cotizacion);
        
        return $stmt->execute();
    }
}
?>