<?php
// Verificar sesión
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/backend/controllers/RequisicionController.php';
require_once dirname(__DIR__, 3) . '/backend/controllers/AuthController.php';

$usuario = $_SESSION['usuario'];
$requisicionController = new RequisicionController();
$areas = $requisicionController->obtenerAreas();

// Obtener unidades disponibles
$db = new \Database();
$conn = $db->getConnection();
$stmt = $conn->query('SELECT * FROM unidades ORDER BY cNombre');
$unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determinar el área del usuario actual
$area_usuario = null;
foreach ($areas as $area) {
    if ($area['id'] == $usuario['idArea']) {
        $area_usuario = $area;
        break;
    }
}

// Procesar formulario si se envía por POST
$requisicion_creada = false;
$error_mensaje = '';
$dashboard_redireccionar = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $datos = [
            'id_area' => $_POST['id_area'] ?? $usuario['idArea'],
            'descripcion' => $_POST['descripcion'] ?? '',
            'requiere_cotizacion' => 1,
            'id_unidad' => $_POST['id_unidad'] ?? null,
            'maquina' => $_POST['maquina'] ?? null,
            'obra_ubicacion' => $_POST['obra_ubicacion'] ?? null
        ];
        
        $resultado = $requisicionController->crearRequisicion($datos, $usuario['id']);
        
        if ($resultado) {
            $requisicion_creada = true;
            
            // Determinar el dashboard según el rol
            $rol = $usuario['cPuesto'] ?? 'solicitante';
            switch ($rol) {
                case 'admin':
                    $dashboard_redireccionar = '../dashboard/admin.php';
                    break;
                case 'jefe_area':
                    $dashboard_redireccionar = '../dashboard/jefe_area.php';
                    break;
                case 'jefe_mayor':
                    $dashboard_redireccionar = '../dashboard/jefe_mayor.php';
                    break;
                case 'contaduria':
                    $dashboard_redireccionar = '../dashboard/contaduria.php';
                    break;
                default:
                    $dashboard_redireccionar = '../dashboard/solicitante.php';
            }
        } else {
            $error_mensaje = 'Error al crear la requisición';
        }
    } catch (Exception $e) {
        $error_mensaje = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Requisición</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h1 { 
            color: #2563eb; 
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #374151;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .btn {
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-cancel {
            background: #6b7280;
        }
        .btn-cancel:hover {
            background: #4b5563;
        }
        .campo-condicional {
            display: none;
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 400px;
            animation: slideUp 0.3s;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-content h2 {
            color: #2563eb;
            margin-bottom: 15px;
        }
        .modal-content p {
            color: #6b7280;
            margin-bottom: 25px;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .modal-btn-confirm {
            background: #10b981;
            color: white;
        }
        .modal-btn-confirm:hover {
            background: #059669;
        }
        .modal-btn-cancel {
            background: #e5e7eb;
            color: #374151;
        }
        .modal-btn-cancel:hover {
            background: #d1d5db;
        }
        .loading {
            display: none;
            color: #2563eb;
        }
        .loading i {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nueva Requisición</h1>

        <form id="formRequisicion" method="POST" action="">
            <!-- Información automática -->
            <div class="form-group">
                <label>Solicitante</label>
                <input type="text" value="<?php echo $usuario['cNombre']; ?>" readonly>
            </div>
            
            <!-- Área según rol -->
            <div class="form-group">
                <label>Área Solicitante *</label>
                <?php if ($usuario['cPuesto'] === 'jefe_area' && $area_usuario): ?>
                    <!-- Jefe de área: área solo lectura -->
                    <input type="text" value="<?php echo $area_usuario['cNombre']; ?>" readonly>
                    <input type="hidden" name="id_area" value="<?php echo $usuario['idArea']; ?>">
                <?php else: ?>
                    <!-- Admin/otros: selector de área -->
                    <select name="id_area" id="selectArea" required onchange="actualizarCamposPorArea()">
                        <option value="">-- Seleccionar Área --</option>
                        <?php foreach ($areas as $area): ?>
                            <option value="<?php echo $area['id']; ?>" 
                                <?php echo ($area['id'] == $usuario['idArea']) ? 'selected' : ''; ?>>
                                <?php echo $area['cNombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Campos específicos por área -->
            <div class="form-group" id="grupo-maquina" style="display: none;">
                <label>Máquina Específica *</label>
                <input type="text" name="maquina" placeholder="Ej: Excavadora CAT 320, Compresor Atlas...">
            </div>

            <div class="form-group">
                <label>Ubicación/Obra *</label>
                <input type="text" name="obra_ubicacion" placeholder="Ej: Obra Norte, Planta Principal..." required>
            </div>

            <div class="form-group">
                <label>Descripción de la Necesidad *</label>
                <textarea name="descripcion" placeholder="Describa detalladamente para qué necesita los materiales..." required></textarea>
            </div>

            <div class="form-group">
                <label>Unidad de Medida *</label>
                <select name="id_unidad" required>
                    <option value="">-- Seleccionar Unidad --</option>
                    <?php foreach ($unidades as $unidad): ?>
                        <option value="<?php echo $unidad['id']; ?>">
                            <?php echo $unidad['cNombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="button" class="btn" onclick="abrirModal()">
                    <i class="bi bi-check-circle"></i> Enviar Requisición
                </button>
                <a href="javascript:history.back()" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h2>Confirmar Requisición</h2>
            <p>¿Estás seguro de que deseas enviar esta requisición?</p>
            <div class="loading" id="loadingSpinner">
                <i class="bi bi-hourglass-split"></i> Enviando...
            </div>
            <div class="modal-buttons" id="modalButtons">
                <button class="modal-btn modal-btn-confirm" onclick="enviarRequisicion()">
                    <i class="bi bi-check"></i> Confirmar
                </button>
                <button class="modal-btn modal-btn-cancel" onclick="cerrarModal()">
                    <i class="bi bi-x"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <script>
        function actualizarCamposPorArea() {
            const selectArea = document.getElementById('selectArea');
            if (!selectArea) return;
            
            const areaSeleccionada = selectArea.options[selectArea.selectedIndex].text.toLowerCase();
            const grupoMaquina = document.getElementById('grupo-maquina');
            
            if (areaSeleccionada.includes('mecánica') || areaSeleccionada.includes('mecanica') || areaSeleccionada.includes('operador')) {
                grupoMaquina.style.display = 'block';
                grupoMaquina.querySelector('input').required = true;
            } else {
                grupoMaquina.style.display = 'none';
                grupoMaquina.querySelector('input').required = false;
                grupoMaquina.querySelector('input').value = '';
            }
        }

        function abrirModal() {
            const form = document.getElementById('formRequisicion');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            document.getElementById('confirmModal').classList.add('show');
        }

        function cerrarModal() {
            document.getElementById('confirmModal').classList.remove('show');
            document.getElementById('modalButtons').style.display = 'flex';
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        function enviarRequisicion() {
            const form = document.getElementById('formRequisicion');
            const formData = new FormData(form);

            document.getElementById('modalButtons').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'block';

            // Enviar formulario por POST normal
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            actualizarCamposPorArea();
        });
    </script>

    <?php if ($requisicion_creada): ?>
    <script>
        // Redirigir automáticamente al dashboard después de 1.5 segundos
        setTimeout(() => {
            window.location.href = '<?php echo $dashboard_redireccionar; ?>';
        }, 1500);
    </script>
    <?php endif; ?>

    <?php if ($error_mensaje): ?>
    <script>
        alert('Error: <?php echo addslashes($error_mensaje); ?>');
    </script>
    <?php endif; ?>
</body>
</html>