<?php
session_start();
require_once dirname(__DIR__, 3) . '/backend/config/database.php';

$db = (new Database())->getConnection();

// Si viene id_requisicion, filtrar solo esas cotizaciones (sin importar estado)
$id_requisicion = $_GET['id_requisicion'] ?? null;
if ($id_requisicion) {
    $sql = "SELECT c.*, r.cFolio, r.cDescripcion
            FROM cotizaciones c
            JOIN requisiciones r ON c.idRequisicion = r.id
            WHERE c.idRequisicion = :id_requisicion
            ORDER BY c.id DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_requisicion', $id_requisicion);
    $stmt->execute();
} else {
    $sql = "SELECT c.*, r.cFolio, r.cDescripcion
            FROM cotizaciones c
            JOIN requisiciones r ON c.idRequisicion = r.id
            WHERE r.lActivo = 1
            ORDER BY c.id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
}

// Agrupar cotizaciones por idRequisicion
$cotizacionesRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cotizacionesAgrupadas = [];
foreach ($cotizacionesRaw as $cot) {
    $cotizacionesAgrupadas[$cot['idRequisicion']][] = $cot;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de Cotizaciones</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        h2 { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center; font-size: 16px; }
        th { background: #f0f9ff; font-size: 16px; }
        th.id, td.id { width: 6%; min-width: 40px; font-size: 14px; }
        th, td { width: 15%; word-break: break-word; }
        .aprobada { background: #d1fae5; color: #065f46; font-weight: bold; }
        .pendiente { background: #fff3cd; color: #856404; font-weight: bold; }
        .flag { padding: 6px 12px; border-radius: 6px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../../index.php" style="display:inline-block; margin-bottom:20px; padding:10px 20px; background:#2563eb; color:#fff; border-radius:6px; text-decoration:none; font-weight:bold;">Volver</a>
        <h2>Seguimiento de Cotizaciones</h2>
        <div style="margin-bottom:20px;">
            <label for="gruposPorPagina">Mostrar:</label>
            <select id="gruposPorPagina" onchange="cambiarPaginacion()">
                <option value="10">10</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select> grupos por página
        </div>
        <div id="gruposCotizaciones"></div>
        <div id="paginacion" style="margin-top:30px; text-align:center;"></div>
        <script>
        const grupos = <?php echo json_encode(array_values($cotizacionesAgrupadas)); ?>;
        let paginaActual = 1;
        let gruposPorPagina = 10;

        function renderGrupos() {
            const inicio = (paginaActual - 1) * gruposPorPagina;
            const fin = inicio + gruposPorPagina;
            const gruposPagina = grupos.slice(inicio, fin);
            let html = '';
            gruposPagina.forEach(grupo => {
                html += `<h3 style="margin-top:40px; color:#2563eb;">Folio: ${grupo[0].cFolio} | Descripción: ${grupo[0].cDescripcion}</h3>`;
                html += `<table><thead><tr><th>#</th><th>Proveedor</th><th>Monto</th><th>Estado</th></tr></thead><tbody>`;
                grupo.forEach((cot, idx) => {
                    const estaAprobada = parseInt(cot.bAprovada) === 1;
                    const estado = estaAprobada ? 'Aprobada' : 'En revisión';
                    html += `<tr class="${estaAprobada ? 'aprobada' : 'pendiente'}">
                        <td>${idx + 1}</td>
                        <td>${cot.cProveedor}</td>
                        <td>$${parseFloat(cot.deMonto).toFixed(2)}</td>
                        <td><span class="flag">${estado}</span></td>
                    </tr>`;
                });
                html += `</tbody></table>`;
            });
            document.getElementById('gruposCotizaciones').innerHTML = html;
            renderPaginacion();
        }

        function renderPaginacion() {
            const totalPaginas = Math.ceil(grupos.length / gruposPorPagina);
            let html = '';
            for (let i = 1; i <= totalPaginas; i++) {
                html += `<button onclick="irPagina(${i})" style="margin:0 5px; padding:8px 16px; border-radius:5px; border:none; background:${i === paginaActual ? '#2563eb' : '#e5e7eb'}; color:${i === paginaActual ? '#fff' : '#333'}; font-weight:bold; cursor:pointer;">${i}</button>`;
            }
            document.getElementById('paginacion').innerHTML = html;
        }

        function irPagina(num) {
            paginaActual = num;
            renderGrupos();
        }

        function cambiarPaginacion() {
            gruposPorPagina = parseInt(document.getElementById('gruposPorPagina').value);
            paginaActual = 1;
            renderGrupos();
        }

        // Inicializar
        renderGrupos();
        </script>
    </div>
</body>
</html>
