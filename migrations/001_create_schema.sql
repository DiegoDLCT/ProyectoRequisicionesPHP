-- Migration: 001_create_schema.sql
-- Crea las tablas mínimas requeridas por el proyecto
-- Ejecutar en MySQL/MariaDB (por ejemplo: mysql -u root -p sistema_requisiciones < 001_create_schema.sql)

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS cotizaciones;
DROP TABLE IF EXISTS requisiciones;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS areas;

SET FOREIGN_KEY_CHECKS = 1;

-- Tabla: areas
CREATE TABLE IF NOT EXISTS areas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cNombre VARCHAR(150) NOT NULL,
  lActivo TINYINT(1) NOT NULL DEFAULT 1,
  INDEX (cNombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cNombre VARCHAR(150) NOT NULL,
  cCorreo VARCHAR(150) NOT NULL UNIQUE,
  cContrasena VARCHAR(255) NOT NULL,
  cPuesto VARCHAR(50) NOT NULL,
  idArea INT NULL,
  lActivo TINYINT(1) NOT NULL DEFAULT 1,
  cTokenInvitacion VARCHAR(255) NULL,
  dExpiracionToken DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  INDEX (idArea),
  FOREIGN KEY (idArea) REFERENCES areas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: requisiciones
CREATE TABLE IF NOT EXISTS requisiciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cFolio VARCHAR(100) NOT NULL UNIQUE,
  dFechaSolicitud DATE NOT NULL,
  idSolicitante INT NULL,
  idArea INT NULL,
  cDescripcion TEXT,
  bRequiereCotizacion TINYINT(1) NOT NULL DEFAULT 0,
  cMaquina VARCHAR(150) NULL,
  cObraUbicacion VARCHAR(150) NULL,
  estado VARCHAR(50) NOT NULL DEFAULT 'Pendiente',
  lActivo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  INDEX (idSolicitante),
  INDEX (idArea),
  FOREIGN KEY (idSolicitante) REFERENCES usuarios(id) ON DELETE SET NULL,
  FOREIGN KEY (idArea) REFERENCES areas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: cotizaciones
CREATE TABLE IF NOT EXISTS cotizaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idRequisicion INT NOT NULL,
  cProveedor VARCHAR(150) NOT NULL,
  cNumcotizacion VARCHAR(100) NULL,
  deMonto DECIMAL(12,2) NULL,
  dFechacotizacion DATE NULL,
  cArchivourl VARCHAR(255) NULL,
  bAprovada TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (idRequisicion),
  FOREIGN KEY (idRequisicion) REFERENCES requisiciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fin de migration
