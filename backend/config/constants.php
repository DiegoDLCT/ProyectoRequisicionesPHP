<?php
// Roles de usuario
define('ROL_JEFE_AREA', 'jefe_area');
define('ROL_ADMIN', 'admin');
define('ROL_JEFE_MAYOR', 'jefe_mayor');
define('ROL_CONTADURIA', 'contaduria');
define('ROL_SOLICITANTE', 'solicitante');

// Estados de REQUISICIÓN
define('ESTADO_PENDIENTE', 'pendiente');
define('ESTADO_COTIZADO', 'cotizado');
define('ESTADO_SOLICITAR_PAGO', 'solicitar_pago');
define('ESTADO_PAGO_SOLICITADO', 'pago_solicitado');
define('ESTADO_PAGADO', 'pagado');
define('ESTADO_POR_ENTREGAR', 'por_entregar');
define('ESTADO_ENTREGADO', 'entregado');

// Estados de COTIZACIÓN
define('COTIZACION_PENDIENTE', 'pendiente');
define('COTIZACION_APROBADA', 'aprobada');
define('COTIZACION_RECHAZADA', 'rechazada');

// Paths del sistema
define('UPLOADS_PATH', __DIR__ . '/../../uploads/');
?>