-- ============================================================
-- MÓDULOS DE FACTURACIÓN ELECTRÓNICA
-- Agrega los módulos al sistema de permisos
-- ============================================================

-- Verificar si los módulos ya existen antes de insertar
-- Módulo: Facturación Electrónica (Reportes)
INSERT INTO modules (id, module, state) 
SELECT 19, 'FACTURACIÓN ELECTRÓNICA', 1
WHERE NOT EXISTS (SELECT 1 FROM modules WHERE id = 19);

-- Módulo: Configuración DIAN
INSERT INTO modules (id, module, state) 
SELECT 20, 'CONFIGURACIÓN DIAN', 1
WHERE NOT EXISTS (SELECT 1 FROM modules WHERE id = 20);

-- Asignar permisos por defecto al perfil Administrador (id=1)
-- Permisos completos para facturación electrónica
INSERT INTO permits (profileid, moduleid, r, a, e, v)
SELECT 1, 19, 1, 1, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM permits WHERE profileid = 1 AND moduleid = 19);

-- Permisos completos para configuración DIAN
INSERT INTO permits (profileid, moduleid, r, a, e, v)
SELECT 1, 20, 1, 1, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM permits WHERE profileid = 1 AND moduleid = 20);

-- Permisos para perfil Técnico (id=2) - Solo visualización
INSERT INTO permits (profileid, moduleid, r, a, e, v)
SELECT 2, 19, 0, 0, 0, 1
WHERE NOT EXISTS (SELECT 1 FROM permits WHERE profileid = 2 AND moduleid = 19);

-- Permisos para perfil Cobros (id=3) - Solo visualización
INSERT INTO permits (profileid, moduleid, r, a, e, v)
SELECT 3, 19, 0, 0, 0, 1
WHERE NOT EXISTS (SELECT 1 FROM permits WHERE profileid = 3 AND moduleid = 19);

-- ============================================================
-- TABLA DE RESOLUCIONES ELECTRÓNICAS (si no existe)
-- ============================================================
CREATE TABLE IF NOT EXISTS `electronic_resolutions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type_document_id` INT NOT NULL COMMENT '1=Factura, 4=NC, 5=ND, 11=Doc Soporte',
    `resolution_number` VARCHAR(50) NOT NULL,
    `resolution_date` DATE,
    `prefix` VARCHAR(20) NOT NULL,
    `consecutive_from` INT NOT NULL,
    `consecutive_to` INT NOT NULL,
    `current_consecutive` INT DEFAULT 0,
    `date_from` DATE,
    `date_to` DATE,
    `technical_key` VARCHAR(200),
    `state` INT DEFAULT 1 COMMENT '1=Activa, 0=Inactiva',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_type_state` (`type_document_id`, `state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- TABLA DE FACTURAS ELECTRÓNICAS (si no existe)
-- ============================================================
CREATE TABLE IF NOT EXISTS `electronic_invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `billid` INT NOT NULL,
    `type_document` VARCHAR(20) NOT NULL COMMENT 'invoice, credit-note, debit-note',
    `type_document_id` INT NOT NULL COMMENT '1=Factura, 4=NC, 5=ND',
    `prefix` VARCHAR(20),
    `number` INT,
    `cufe` VARCHAR(200),
    `cude` VARCHAR(200),
    `electronic_state` INT DEFAULT 0 COMMENT '0=Pendiente, 1=Autorizada, 2=Rechazada',
    `xml_filename` VARCHAR(200),
    `pdf_filename` VARCHAR(200),
    `qr_string` TEXT,
    `dian_response` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_billid` (`billid`),
    INDEX `idx_type_state` (`type_document`, `electronic_state`),
    INDEX `idx_number` (`prefix`, `number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- TABLA DE LOG DE FACTURACIÓN ELECTRÓNICA
-- ============================================================
CREATE TABLE IF NOT EXISTS `electronic_invoice_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `billid` INT,
    `action` VARCHAR(50) NOT NULL COMMENT 'send, response, error, cancel',
    `request_data` TEXT,
    `response_data` TEXT,
    `error_message` TEXT,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_billid` (`billid`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- TABLA DE NOTAS CRÉDITO/DÉBITO ELECTRÓNICAS
-- ============================================================
CREATE TABLE IF NOT EXISTS `electronic_credit_notes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `billid` INT NOT NULL COMMENT 'Factura original',
    `credit_billid` INT COMMENT 'Nota crédito creada',
    `reason_code` INT NOT NULL COMMENT 'Código de discrepancia',
    `reason_description` VARCHAR(200),
    `cude` VARCHAR(200),
    `electronic_state` INT DEFAULT 0,
    `xml_filename` VARCHAR(200),
    `pdf_filename` VARCHAR(200),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_billid` (`billid`),
    INDEX `idx_state` (`electronic_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- TABLA DE NOTAS DÉBITO ELECTRÓNICAS
-- ============================================================
CREATE TABLE IF NOT EXISTS `electronic_debit_notes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `billid` INT NOT NULL COMMENT 'Factura original',
    `debit_billid` INT COMMENT 'Nota débito creada',
    `reason_code` INT NOT NULL COMMENT 'Código de discrepancia',
    `reason_description` VARCHAR(200),
    `cude` VARCHAR(200),
    `electronic_state` INT DEFAULT 0,
    `xml_filename` VARCHAR(200),
    `pdf_filename` VARCHAR(200),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_billid` (`billid`),
    INDEX `idx_state` (`electronic_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- TABLA DE CONFIGURACIÓN DIAN POR EMPRESA
-- ============================================================
CREATE TABLE IF NOT EXISTS `dian_configuration` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `business_id` INT NOT NULL,
    `environment` VARCHAR(20) DEFAULT 'habilitacion' COMMENT 'habilitacion, produccion',
    `nit` VARCHAR(20),
    `dv` VARCHAR(5),
    `software_id` VARCHAR(50),
    `software_pin` VARCHAR(50),
    `test_set_id` VARCHAR(50),
    `url_web_service` VARCHAR(200),
    `url_files` VARCHAR(200),
    `token` VARCHAR(500),
    `token_expires` DATETIME,
    `state` INT DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_business` (`business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- TABLA DE EVENTOS DIAN
-- ============================================================
CREATE TABLE IF NOT EXISTS `dian_events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `billid` INT NOT NULL,
    `event_code` VARCHAR(20) NOT NULL,
    `event_name` VARCHAR(100),
    `description` TEXT,
    `xml_filename` VARCHAR(200),
    `dian_response` TEXT,
    `state` INT DEFAULT 0 COMMENT '0=Pendiente, 1=Enviado, 2=Aceptado, 3=Rechazado',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_billid` (`billid`),
    INDEX `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Verificar estructura
SELECT 'Módulos insertados correctamente' AS resultado;
SELECT * FROM modules WHERE id IN (19, 20);
SELECT * FROM permits WHERE moduleid IN (19, 20);
