<?php
session_start();
require_once dirname(__DIR__, 3) . '/backend/utils/auth.php';
require_once dirname(__DIR__, 3) . '/backend/config/database.php';
require_once dirname(__DIR__, 3) . '/backend/config/routes.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (!tieneRol('Contaduria')) {
    header('Location: ../../index.php?error=denegado');
    exit;
}

// Obtener ID de requisición
$id_requisicion = $_GET['id'] ?? null;

if (!$id_requisicion) {
    header('Location: ./confirmar.php');
    exit;
}

$db = (new Database())->getConnection();

// Obtener datos de la requisición
$query = "SELECT r.*, 
          u.cNombre as solicitante_nombre, 
          c.cProveedor as proveedor_nombre,
          c.deMonto as cotizacion_monto
          FROM requisiciones r
          LEFT JOIN usuarios u ON u.id = r.idSolicitante
          LEFT JOIN cotizaciones c ON c.idRequisicion = r.id AND c.bAprovada = 1
          WHERE r.id = :id AND r.lActivo = 1 AND r.estado = 'pago_solicitado'";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_requisicion);
$stmt->execute();
$requisicion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$requisicion) {
    header('Location: ./confirmar.php');
    exit;
}

// Obtener tipos de pago
$stmt_tipos = $db->query('SELECT * FROM tipos_pago ORDER BY id');
$tipos_pago = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Método de Pago</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: #f5f5f7; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header h1 { font-size: 28px; color: #1d1d1f; margin-bottom: 5px; }
        .header p { color: #86868b; font-size: 14px; }
        .requisicion-info { background: #e5f3ff; padding: 20px; border-radius: 12px; margin-bottom: 30px; border-left: 4px solid #0071e3; }
        .requisicion-info h3 { color: #1d4ed8; margin-bottom: 10px; font-size: 16px; }
        .info-row { display: flex; justify-content: space-between; margin: 8px 0; font-size: 14px; color: #1d4ed8; }
        .info-label { font-weight: 600; }
        .info-value { font-weight: normal; }
        .monto { font-size: 24px; font-weight: 700; color: #0071e3; margin-top: 10px; }
        .metodos-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .metodo-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #1d1d1f;
        }
        .metodo-card:hover {
            border-color: #0071e3;
            box-shadow: 0 4px 12px rgba(0,113,227,0.15);
            transform: translateY(-2px);
        }
        .metodo-card.selected {
            border-color: #0071e3;
            background: #e5f3ff;
            box-shadow: 0 4px 12px rgba(0,113,227,0.15);
        }
        .metodo-icon { font-size: 48px; margin-bottom: 15px; display: block; }
        .metodo-nombre { font-size: 18px; font-weight: 600; margin-bottom: 10px; }
        .metodo-desc { font-size: 13px; color: #86868b; }
        .actions { display: flex; gap: 10px; justify-content: center; }
        .btn { display: inline-block; padding: 12px 30px; background: #0071e3; color: white; text-decoration: none; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn:hover { background: #0077ed; transform: translateY(-1px); }
        .btn-secondary { background: #86868b; }
        .btn-secondary:hover { background: #6e6e73; }
        .btn:disabled { background: #d1d5db; cursor: not-allowed; }
        .error { color: #ef4444; font-size: 14px; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Seleccionar Método de Pago</h1>
            <p>Elige cómo deseas procesar el pago</p>
        </div>

        <div class="requisicion-info">
            <h3>📋 Detalles de la Requisición</h3>
            <div class="info-row">
                <span class="info-label">Folio:</span>
                <span class="info-value"><?= htmlspecialchars($requisicion['cFolio']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Proveedor:</span>
                <span class="info-value"><?= htmlspecialchars($requisicion['proveedor_nombre']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Descripción:</span>
                <span class="info-value"><?= htmlspecialchars($requisicion['cDescripcion']) ?></span>
            </div>
            <div class="monto">
                Monto a Pagar: $<?= number_format($requisicion['cotizacion_monto'], 2) ?>
            </div>
        </div>

        <form method="POST" action="<?= SERVICES_URL ?>/confirmar_pago_service.php">
            <input type="hidden" name="id_requisicion" value="<?= $id_requisicion ?>">
            
            <h2 style="margin-bottom: 20px; font-size: 18px; color: #1d1d1f;">Métodos de Pago Disponibles</h2>
            <div class="metodos-container">
                <?php foreach ($tipos_pago as $tipo): ?>
                    <label style="cursor: pointer;">
                        <div class="metodo-card" id="metodo-<?= $tipo['id'] ?>">
                            <?php 
                                $iconos = [
                                    'Efectivo' => 'bi-cash',
                                    'Transferencia' => 'bi-bank',
                                    'Tarjeta de débito' => 'bi-credit-card'
                                ];
                                $icono = $iconos[$tipo['CNombre']] ?? 'bi-question-circle';
                            ?>
                            <i class="bi <?= $icono ?> metodo-icon"></i>
                            <div class="metodo-nombre"><?= htmlspecialchars($tipo['CNombre']) ?></div>
                            <div class="metodo-desc">
                                <?php
                                    $descripciones = [
                                        'Efectivo' => 'Pago en efectivo',
                                        'Transferencia' => 'Transferencia bancaria',
                                        'Tarjeta de débito' => 'Tarjeta de débito bancaria'
                                    ];
                                    echo $descripciones[$tipo['CNombre']] ?? '';
                                ?>
                            </div>
                            <input type="radio" name="id_tipo_pago" value="<?= $tipo['id'] ?>" style="display: none;" onchange="seleccionarMetodo(<?= $tipo['id'] ?>)">
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="actions">
                <button type="submit" class="btn" id="btn-confirmar" disabled>
                    <i class="bi bi-check-circle"></i> Confirmar Pago
                </button>
                <button type="button" class="btn btn-secondary" onclick="history.back()">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </button>
            </div>
            <div class="error" id="error-msg"></div>
        </form>
    </div>

    <script>
        function seleccionarMetodo(id) {
            // Desseleccionar todos
            document.querySelectorAll('.metodo-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Seleccionar el clickeado
            document.getElementById('metodo-' + id).classList.add('selected');
            
            // Marcar el radio
            document.querySelector('input[value="' + id + '"]').checked = true;
            
            // Habilitar botón
            document.getElementById('btn-confirmar').disabled = false;
            document.getElementById('error-msg').textContent = '';
        }

        // Click en las tarjetas
        document.querySelectorAll('.metodo-card').forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    seleccionarMetodo(radio.value);
                }
            });
        });

        // Validar antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const tipoSeleccionado = document.querySelector('input[name="id_tipo_pago"]:checked');
            if (!tipoSeleccionado) {
                e.preventDefault();
                document.getElementById('error-msg').textContent = 'Por favor, selecciona un método de pago';
            }
        });
    </script>
</body>
</html>
