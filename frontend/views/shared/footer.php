<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<footer style="border-top:1px solid #e5e7eb;padding:12px 20px;margin-top:30px;background:#fafafa;">
	<div style="max-width:1100px;margin:0 auto;text-align:center;color:#6b7280;font-size:14px;">
		&copy; <?php echo date('Y'); ?> Sistema de Requisiciones. Todos los derechos reservados.
	</div>
</footer>
