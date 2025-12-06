-- ============================================================================
-- SISTEMA DE REQUISICIONES v1.0
-- Script completo de instalación de la base de datos
-- ============================================================================
-- Este script crea todas las tablas necesarias y carga datos iniciales
-- Compatibilidad: MySQL 5.7+, MariaDB 10.2+
-- ============================================================================

-- Desactivar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. CREAR BASE DE DATOS
-- ============================================================================
DROP DATABASE IF EXISTS `sistema_requisiciones`;
CREATE DATABASE `sistema_requisiciones` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `sistema_requisiciones`;

-- ============================================================================
-- 2. TABLAS BASE
-- ============================================================================

-- Tabla: areas
DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(100) NOT NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cNombre` (`cNombre`),
  INDEX `lActivo` (`lActivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(150) NOT NULL,
  `cCorreo` varchar(150) NOT NULL UNIQUE,
  `cContrasena` varchar(255) NOT NULL,
  `cPuesto` varchar(50) NOT NULL,
  `idArea` int(11) NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  `cTokenInvitacion` varchar(255) NULL,
  `dExpiracionToken` datetime NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cCorreo` (`cCorreo`),
  INDEX `idArea` (`idArea`),
  INDEX `cPuesto` (`cPuesto`),
  INDEX `lActivo` (`lActivo`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idArea`) REFERENCES `areas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: unidades
DROP TABLE IF EXISTS `unidades`;
CREATE TABLE `unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(50) NOT NULL,
  `cAbreviatura` varchar(10) NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cNombre` (`cNombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: requisiciones
DROP TABLE IF EXISTS `requisiciones`;
CREATE TABLE `requisiciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cFolio` varchar(50) NOT NULL UNIQUE,
  `dFechaSolicitud` date NOT NULL,
  `idSolicitante` int(11) NOT NULL,
  `idArea` int(11) NOT NULL,
  `cDescripcion` text NULL,
  `cMaquina` varchar(150) NULL,
  `cObraUbicacion` varchar(150) NULL,
  `idUnidad` int(11) NULL,
  `iCantidad` int(11) NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente',
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cFolio` (`cFolio`),
  INDEX `idSolicitante` (`idSolicitante`),
  INDEX `idArea` (`idArea`),
  INDEX `estado` (`estado`),
  INDEX `lActivo` (`lActivo`),
  CONSTRAINT `requisiciones_ibfk_1` FOREIGN KEY (`idSolicitante`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `requisiciones_ibfk_2` FOREIGN KEY (`idArea`) REFERENCES `areas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `requisiciones_ibfk_3` FOREIGN KEY (`idUnidad`) REFERENCES `unidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: proveedores
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(150) NOT NULL,
  `cContacto` varchar(150) NULL,
  `cTelefono` varchar(20) NULL,
  `cEmail` varchar(100) NULL,
  `cDireccion` text NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cNombre` (`cNombre`),
  INDEX `lActivo` (`lActivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: cotizaciones
DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE `cotizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idRequisicion` int(11) NOT NULL,
  `cProveedor` varchar(150) NOT NULL,
  `cNumcotizacion` varchar(100) NULL,
  `deMonto` decimal(12,2) NULL,
  `dFechacotizacion` date NULL,
  `iDiasEntrega` int(11) NULL,
  `cArchivourl` varchar(255) NULL,
  `bAprovada` tinyint(1) DEFAULT 0,
  `cMetodoPago` varchar(50) NULL,
  `dFechaPago` datetime NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idRequisicion` (`idRequisicion`),
  INDEX `bAprovada` (`bAprovada`),
  CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`idRequisicion`) REFERENCES `requisiciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: tipos_pago
DROP TABLE IF EXISTS `tipos_pago`;
CREATE TABLE `tipos_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(100) NOT NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cNombre` (`cNombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: compras
DROP TABLE IF EXISTS `compras`;
CREATE TABLE `compras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idRequisicion` int(11) NOT NULL,
  `idTipoPago` int(11) NULL,
  `cFormapago` varchar(100) NULL,
  `deMontoTotal` decimal(12,2) NULL,
  `dFechaCompra` date NULL,
  `cFacturaurl` varchar(255) NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idRequisicion` (`idRequisicion`),
  INDEX `idTipoPago` (`idTipoPago`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`idRequisicion`) REFERENCES `requisiciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`idTipoPago`) REFERENCES `tipos_pago` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. INSERTAR DATOS INICIALES
-- ============================================================================

-- Áreas
INSERT INTO `areas` (`cNombre`, `lActivo`) VALUES
('Administrativo', 1),
('Operaciones', 1),
('Mantenimiento', 1),
('Recursos Humanos', 1);

-- Unidades
INSERT INTO `unidades` (`cNombre`, `cAbreviatura`) VALUES
('Unidad', 'Un'),
('Kilogramo', 'kg'),
('Litro', 'L'),
('Metro', 'm'),
('Metro Cuadrado', 'm²'),
('Caja', 'Caja'),
('Docena', 'Doc'),
('Pares', 'Pares'),
('Piezas', 'Pz');

-- Tipos de Pago
INSERT INTO `tipos_pago` (`cNombre`, `lActivo`) VALUES
('Efectivo', 1),
('Transferencia Bancaria', 1),
('Cheque', 1),
('Tarjeta de Crédito', 1),
('Otro', 1);

-- Reactivar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- 4. INFORMACIÓN IMPORTANTE
-- ============================================================================
/*
INSTRUCCIONES DE USO:

1. Para ejecutar este script:
   - Opción A (Terminal/CMD): 
     mysql -u root -p < setup_complete.sql
   
   - Opción B (Desde PHP):
     $conn = new PDO('mysql:host=localhost', 'root', '');
     $sql = file_get_contents('setup_complete.sql');
     $conn->exec($sql);

2. El script creará automáticamente:
   ✓ Base de datos: sistema_requisiciones
   ✓ 8 tablas con relaciones
   ✓ Datos iniciales (áreas, unidades, tipos de pago)
   ✓ Índices para optimizar consultas

3. Después de ejecutar, criar usuarios usando el panel de admin
   - Email: admin@admin.com
   - Contraseña: password (cambiar después)

4. CARACTERÍSTICAS DE SEGURIDAD:
   - Claves foráneas habilitadas
   - Índices en columnas frecuentes
   - Timestamps automáticos (created_at, updated_at)
   - Caracteres UTF-8 para idiomas internacionales

5. ESTADOS DE REQUISICIÓN VÁLIDOS:
   - 'pendiente': Esperando cotización
   - 'cotizado': Cotización recibida
   - 'solicitar_pago': Listo para solicitar pago
   - 'pago_solicitado': Pago en solicitud
   - 'pagado': Pago confirmado
   - 'por_entregar': En tránsito
   - 'entregado': Completado
*/
