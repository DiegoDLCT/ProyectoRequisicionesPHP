<?php
session_start();
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (!tieneRol('Contaduria')) {
    header('Location: ../../index.php?error=denegado');
    exit;
}

$db = (new Database())->getConnection();

// Obtener requisiciones en estado pago_solicitado
$query = "SELECT r.*, 
          u.cNombre as solicitante_nombre, 
          c.cProveedor as proveedor_nombre,
          c.deMonto as cotizacion_monto,
          c.cArchivourl
          FROM requisiciones r
          LEFT JOIN usuarios u ON u.id = r.idSolicitante
          LEFT JOIN cotizaciones c ON c.idRequisicion = r.id AND c.bAprovada = 1
          WHERE r.lActivo = 1 AND r.estado = 'pago_solicitado'
          ORDER BY r.dFechaSolicitud DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$requisiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensaje_exito = $_SESSION['mensaje_exito'] ?? null;
$mensaje_error = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Pagos - Contaduría</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: #f5f5f7; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header h1 { font-size: 28px; color: #1d1d1f; margin-bottom: 5px; }
        .header p { color: #86868b; font-size: 14px; }
        .btn { display: inline-block; padding: 10px 20px; background: #0071e3; color: white; text-decoration: none; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; transition: all 0.2s; }
        .btn:hover { background: #0077ed; transform: translateY(-1px); }
        .btn-secondary { background: #86868b; }
        .btn-secondary:hover { background: #6e6e73; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f5f5f7; }
        th { padding: 15px; text-align: left; font-weight: 600; color: #1d1d1f; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 15px; border-bottom: 1px solid #f5f5f7; color: #1d1d1f; }
        tr:hover { background: #fafafa; }
        .empty { text-align: center; padding: 60px 20px; color: #86868b; }
        .empty i { font-size: 48px; margin-bottom: 15px; display: block; }
        .monto { font-weight: 600; color: #0071e3; }
        .actions { display: flex; gap: 10px; }
        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        /* Modal */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 400px; width: 90%; }
        .modal h2 { font-size: 20px; color: #1d1d1f; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .modal p { color: #6b7280; margin-bottom: 20px; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
        .modal-actions .btn { padding: 10px 20px; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .btn-cancel { background: #d1d5db; color: #1f2937; }
        .btn-cancel:hover { background: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmar Pagos</h1>
            <p>Requisiciones con pagos solicitados pendientes de confirmación</p>
        </div>

        <?php if ($mensaje_exito): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje_exito) ?></div>
        <?php endif; ?>

        <?php if ($mensaje_error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($mensaje_error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (empty($requisiciones)): ?>
                <div class="empty">
                    <i class="bi bi-inbox"></i>
                    <p>No hay pagos pendientes de confirmar</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Solicitante</th>
                            <th>Descripción</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Fecha Solicitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requisiciones as $req): ?>
                            <tr>
                                <td><?= htmlspecialchars($req['cFolio']) ?></td>
                                <td><?= htmlspecialchars($req['solicitante_nombre']) ?></td>
                                <td><?= htmlspecialchars($req['cDescripcion']) ?></td>
                                <td><?= htmlspecialchars($req['proveedor_nombre']) ?></td>
                                <td class="monto">$<?= number_format($req['cotizacion_monto'], 2) ?></td>
                                <td><?= date('d/m/Y', strtotime($req['dFechaSolicitud'])) ?></td>
                                <td class="actions">
                                    <a href="../requisiciones/ver.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success" onclick="abrirModalConfirmacion(<?= $req['id'] ?>, '<?= htmlspecialchars($req['cFolio']) ?>')">
                                        <i class="bi bi-credit-card"></i> Pagar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <button onclick="history.back()" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver Atrás
                </button>
                <a href="../../index.php" class="btn" style="background: #2563eb; margin-left: 10px;">
                    <i class="bi bi-house"></i> Inicio
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal-overlay" id="modalConfirmacion">
        <div class="modal">
            <h2><i class="bi bi-credit-card"></i> Confirmar Pago</h2>
            <p>Requisición <strong id="folioReq"></strong></p>
            
            <div style="margin: 20px 0;">
                <label style="display: block; margin-bottom: 10px; font-weight: 500; color: #1d1d1f;">Método de Pago *</label>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label style="display: flex; align-items: center; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="metodo_pago" value="efectivo" required style="margin-right: 10px; cursor: pointer;">
                        <span style="flex: 1;"><i class="bi bi-cash-coin" style="margin-right: 8px;"></i> Efectivo</span>
                    </label>
                    <label style="display: flex; align-items: center; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="metodo_pago" value="transferencia" required style="margin-right: 10px; cursor: pointer;">
                        <span style="flex: 1;"><i class="bi bi-bank" style="margin-right: 8px;"></i> Transferencia Bancaria</span>
                    </label>
                    <label style="display: flex; align-items: center; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="metodo_pago" value="tarjeta" required style="margin-right: 10px; cursor: pointer;">
                        <span style="flex: 1;"><i class="bi bi-credit-card-2-front" style="margin-right: 8px;"></i> Tarjeta de Crédito/Débito</span>
                    </label>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmarPago()">Confirmar Pago</button>
            </div>
        </div>
    </div>

    <script>
        let idRequisicionSeleccionada = null;

        function abrirModalConfirmacion(id, folio) {
            idRequisicionSeleccionada = id;
            document.getElementById('folioReq').textContent = folio;
            document.getElementById('modalConfirmacion').classList.add('active');
        }

        function cerrarModal() {
            document.getElementById('modalConfirmacion').classList.remove('active');
            idRequisicionSeleccionada = null;
        }

        function confirmarPago() {
            if (!idRequisicionSeleccionada) return;

            // Validar que se seleccionó un método de pago
            const metodo_pago = document.querySelector('input[name="metodo_pago"]:checked');
            if (!metodo_pago) {
                alert('Por favor, selecciona un método de pago');
                return;
            }

            const formData = new FormData();
            formData.append('id_requisicion', idRequisicionSeleccionada);
            formData.append('metodo_pago', metodo_pago.value);

            fetch('/ProyectoPHP/frontend/services/marcar_pagado_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                cerrarModal();
                console.log('Respuesta recibida:', text);
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        mostrarMensaje('success', data.mensaje);
                        setTimeout(() => { location.reload(); }, 1500);
                    } else {
                        mostrarMensaje('error', data.mensaje);
                    }
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    console.error('Respuesta:', text);
                    mostrarMensaje('error', 'Error al procesar la respuesta: ' + text.substring(0, 100));
                }
            })
            .catch(error => {
                cerrarModal();
                console.error('Error en fetch:', error);
                mostrarMensaje('error', 'Error en la solicitud: ' + error);
            });
        }

        function mostrarMensaje(tipo, texto) {
            const container = document.querySelector('.table-container');
            const alertDiv = document.createElement('div');
            alertDiv.className = tipo === 'success' ? 'alert alert-success' : 'alert alert-error';
            alertDiv.innerHTML = '<i class="bi ' + (tipo === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle') + '"></i> ' + texto;
            container.parentNode.insertBefore(alertDiv, container);
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalConfirmacion').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });
    </script>
</body>
</html>
