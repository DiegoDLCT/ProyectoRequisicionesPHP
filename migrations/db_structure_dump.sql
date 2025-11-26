-- DB structure dump generated: 2025-11-26T05:07:49+01:00

SET FOREIGN_KEY_CHECKS = 0;

-- Table: areas
CREATE TABLE `areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(100) DEFAULT NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: compras
CREATE TABLE `compras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idRequisicion` int(11) NOT NULL,
  `idTipoPago` int(11) NOT NULL,
  `cFormapago` varchar(100) DEFAULT NULL,
  `deMontoTotal` decimal(12,2) DEFAULT NULL,
  `dFechaCompra` date DEFAULT NULL,
  `cFacturaurl` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idRequisicion` (`idRequisicion`),
  KEY `idTipoPago` (`idTipoPago`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`idRequisicion`) REFERENCES `requisiciones` (`id`),
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`idTipoPago`) REFERENCES `tipos_pago` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: cotizaciones
CREATE TABLE `cotizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idRequisicion` int(11) NOT NULL,
  `cProveedor` varchar(150) NOT NULL,
  `cNumcotizacion` varchar(50) DEFAULT NULL,
  `deMonto` decimal(12,2) DEFAULT NULL,
  `dFechacotizacion` date DEFAULT NULL,
  `cArchivourl` varchar(255) DEFAULT NULL,
  `bAprovada` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idRequisicion` (`idRequisicion`),
  CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`idRequisicion`) REFERENCES `requisiciones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: detalle_requisicion
CREATE TABLE `detalle_requisicion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idRequisicion` int(11) NOT NULL,
  `cArticulo` varchar(150) NOT NULL,
  `iCantidad` int(11) NOT NULL,
  `idUnidad` int(11) NOT NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idRequisicion` (`idRequisicion`),
  KEY `idUnidad` (`idUnidad`),
  CONSTRAINT `detalle_requisicion_ibfk_1` FOREIGN KEY (`idRequisicion`) REFERENCES `requisiciones` (`id`),
  CONSTRAINT `detalle_requisicion_ibfk_2` FOREIGN KEY (`idUnidad`) REFERENCES `unidades` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: requisiciones
CREATE TABLE `requisiciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cFolio` varchar(50) NOT NULL,
  `dFechaSolicitud` date NOT NULL,
  `idSolicitante` int(11) NOT NULL,
  `idArea` int(11) NOT NULL,
  `cDescripcion` text DEFAULT NULL,
  `bRequiereCotizacion` tinyint(1) DEFAULT 0,
  `estado` varchar(50) DEFAULT 'Pendiente',
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cFolio` (`cFolio`),
  KEY `idSolicitante` (`idSolicitante`),
  KEY `idArea` (`idArea`),
  CONSTRAINT `requisiciones_ibfk_1` FOREIGN KEY (`idSolicitante`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `requisiciones_ibfk_2` FOREIGN KEY (`idArea`) REFERENCES `areas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: tipos_pago
CREATE TABLE `tipos_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CNombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: transferencias
CREATE TABLE `transferencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idCompra` int(11) NOT NULL,
  `dFechaTransferencia` date DEFAULT NULL,
  `cFolioTransferencia` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idCompra` (`idCompra`),
  CONSTRAINT `transferencias_ibfk_1` FOREIGN KEY (`idCompra`) REFERENCES `compras` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: unidades
CREATE TABLE `unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: usuarios
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cNombre` varchar(100) NOT NULL,
  `cCorreo` varchar(150) NOT NULL,
  `cPuesto` varchar(100) DEFAULT NULL,
  `idArea` int(11) DEFAULT NULL,
  `lActivo` tinyint(1) NOT NULL DEFAULT 1,
  `cContrasena` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cCorreo` (`cCorreo`),
  KEY `area_id` (`idArea`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idArea`) REFERENCES `areas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
