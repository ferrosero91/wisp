<?php
/**
 * Script de migración - Facturación Electrónica DIAN
 * Ejecutar desde la raíz del proyecto: php migrate_electronic_invoice.php
 */

require_once("Config/Config.php");

echo "=== MIGRACIÓN: Facturación Electrónica DIAN ===\n\n";

try {
    $conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conexión a base de datos exitosa\n\n";
} catch(PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}

// ============================================================
// PASO 1: Agregar campos a business
// ============================================================
echo "--- PASO 1: Agregar campos de APIDIAN a tabla business ---\n";

$fields_business = [
    'apidian_url' => "ALTER TABLE `business` ADD COLUMN `apidian_url` VARCHAR(500) DEFAULT NULL AFTER `reniec_apikey`",
    'apidian_token' => "ALTER TABLE `business` ADD COLUMN `apidian_token` VARCHAR(500) DEFAULT NULL AFTER `apidian_url`",
    'apidian_nit' => "ALTER TABLE `business` ADD COLUMN `apidian_nit` VARCHAR(15) DEFAULT NULL AFTER `apidian_token`",
    'apidian_dv' => "ALTER TABLE `business` ADD COLUMN `apidian_dv` CHAR(1) DEFAULT NULL AFTER `apidian_nit`",
    'apidian_environment' => "ALTER TABLE `business` ADD COLUMN `apidian_environment` VARCHAR(20) DEFAULT 'habilitacion' AFTER `apidian_dv`",
    'apidian_prefix' => "ALTER TABLE `business` ADD COLUMN `apidian_prefix` VARCHAR(10) DEFAULT NULL AFTER `apidian_environment`",
    'apidian_resolution' => "ALTER TABLE `business` ADD COLUMN `apidian_resolution` VARCHAR(50) DEFAULT NULL AFTER `apidian_prefix`",
    'apidian_resolution_from' => "ALTER TABLE `business` ADD COLUMN `apidian_resolution_from` BIGINT DEFAULT NULL AFTER `apidian_resolution`",
    'apidian_resolution_to' => "ALTER TABLE `business` ADD COLUMN `apidian_resolution_to` BIGINT DEFAULT NULL AFTER `apidian_resolution_from`",
    'apidian_next_number' => "ALTER TABLE `business` ADD COLUMN `apidian_next_number` BIGINT DEFAULT 1 AFTER `apidian_resolution_to`",
    'tax_rate' => "ALTER TABLE `business` ADD COLUMN `tax_rate` DECIMAL(5,2) DEFAULT 19.00 AFTER `apidian_next_number`",
    'tax_name' => "ALTER TABLE `business` ADD COLUMN `tax_name` VARCHAR(50) DEFAULT 'IVA' AFTER `tax_rate`"
];

foreach($fields_business as $name => $sql) {
    try {
        $conexion->exec($sql);
        echo "  ✓ Campo $name agregado\n";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  - Campo $name ya existe\n";
        } else {
            echo "  ✗ Error en $name: " . $e->getMessage() . "\n";
        }
    }
}

// ============================================================
// PASO 2: Agregar campos a clients
// ============================================================
echo "\n--- PASO 2: Agregar campos DIAN a tabla clients ---\n";

$fields_clients = [
    'type_document_identification_id' => "ALTER TABLE `clients` ADD COLUMN `type_document_identification_id` BIGINT DEFAULT NULL AFTER `document`",
    'dv' => "ALTER TABLE `clients` ADD COLUMN `dv` CHAR(1) DEFAULT NULL AFTER `type_document_identification_id`",
    'type_organization_id' => "ALTER TABLE `clients` ADD COLUMN `type_organization_id` BIGINT DEFAULT NULL AFTER `dv`",
    'type_regime_id' => "ALTER TABLE `clients` ADD COLUMN `type_regime_id` BIGINT DEFAULT NULL AFTER `type_organization_id`",
    'tax_id' => "ALTER TABLE `clients` ADD COLUMN `tax_id` BIGINT DEFAULT NULL AFTER `type_regime_id`",
    'municipality_id' => "ALTER TABLE `clients` ADD COLUMN `municipality_id` BIGINT DEFAULT NULL AFTER `tax_id`"
];

foreach($fields_clients as $name => $sql) {
    try {
        $conexion->exec($sql);
        echo "  ✓ Campo $name agregado\n";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  - Campo $name ya existe\n";
        } else {
            echo "  ✗ Error en $name: " . $e->getMessage() . "\n";
        }
    }
}

// ============================================================
// PASO 3: Agregar campos a detail_bills
// ============================================================
echo "\n--- PASO 3: Agregar campos de impuestos a detail_bills ---\n";

$fields_detail = [
    'tax_rate' => "ALTER TABLE `detail_bills` ADD COLUMN `tax_rate` DECIMAL(5,2) DEFAULT 0.00 AFTER `total`",
    'tax_amount' => "ALTER TABLE `detail_bills` ADD COLUMN `tax_amount` DECIMAL(12,2) DEFAULT 0.00 AFTER `tax_rate`"
];

foreach($fields_detail as $name => $sql) {
    try {
        $conexion->exec($sql);
        echo "  ✓ Campo $name agregado\n";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  - Campo $name ya existe\n";
        } else {
            echo "  ✗ Error en $name: " . $e->getMessage() . "\n";
        }
    }
}

// ============================================================
// PASO 4: Crear tabla electronic_invoices
// ============================================================
echo "\n--- PASO 4: Crear tabla electronic_invoices ---\n";

$sql = "CREATE TABLE IF NOT EXISTS `electronic_invoices` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `billid` BIGINT NOT NULL,
    `type_document` VARCHAR(30) NOT NULL COMMENT 'invoice, credit-note, debit-note, support-document',
    `type_document_id` INT NOT NULL COMMENT '1=Factura, 4=NC, 5=ND, 11=DS',
    `prefix` VARCHAR(10) DEFAULT NULL,
    `number` BIGINT DEFAULT NULL,
    `cufe` VARCHAR(200) DEFAULT NULL COMMENT 'CUFE/CUDE/CUDS',
    `electronic_state` INT DEFAULT 0 COMMENT '0=pendiente, 1=autorizado, 2=rechazado',
    `xml_filename` VARCHAR(200) DEFAULT NULL,
    `pdf_filename` VARCHAR(200) DEFAULT NULL,
    `dian_response` TEXT DEFAULT NULL,
    `dian_status_code` VARCHAR(10) DEFAULT NULL,
    `dian_status_message` TEXT DEFAULT NULL,
    `qr_string` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_billid` (`billid`),
    INDEX `idx_cufe` (`cufe`),
    INDEX `idx_state` (`electronic_state`),
    FOREIGN KEY (`billid`) REFERENCES `bills`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

try {
    $conexion->exec($sql);
    echo "  ✓ Tabla electronic_invoices creada\n";
} catch(PDOException $e) {
    if(strpos($e->getMessage(), 'already exists') !== false) {
        echo "  - Tabla electronic_invoices ya existe\n";
    } else {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

// ============================================================
// PASO 5: Crear tabla electronic_resolutions
// ============================================================
echo "\n--- PASO 5: Crear tabla electronic_resolutions ---\n";

$sql = "CREATE TABLE IF NOT EXISTS `electronic_resolutions` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `type_document_id` INT NOT NULL COMMENT '1=Factura, 4=NC, 5=ND, 11=DS',
    `prefix` VARCHAR(10) NOT NULL,
    `resolution_number` VARCHAR(50) NOT NULL,
    `resolution_date` DATE NOT NULL,
    `date_from` DATE NOT NULL,
    `date_to` DATE NOT NULL,
    `consecutive_from` BIGINT NOT NULL,
    `consecutive_to` BIGINT NOT NULL,
    `current_consecutive` BIGINT DEFAULT 1,
    `technical_key` VARCHAR(200) DEFAULT NULL,
    `state` INT DEFAULT 1 COMMENT '1=activo, 0=inactivo',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_type_doc` (`type_document_id`),
    INDEX `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

try {
    $conexion->exec($sql);
    echo "  ✓ Tabla electronic_resolutions creada\n";
} catch(PDOException $e) {
    if(strpos($e->getMessage(), 'already exists') !== false) {
        echo "  - Tabla electronic_resolutions ya existe\n";
    } else {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

// ============================================================
// PASO 6: Crear tablas de referencia DIAN
// ============================================================
echo "\n--- PASO 6: Crear tablas de referencia DIAN ---\n";

// dian_type_documents
$sql = "CREATE TABLE IF NOT EXISTS `dian_type_documents` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_type_documents` (`id`, `name`, `code`) VALUES
        (1, 'Registro civil', '11'),
        (2, 'Tarjeta de identidad', '12'),
        (3, 'Cédula de ciudadanía', '13'),
        (4, 'Tarjeta de extranjería', '21'),
        (5, 'Cédula de extranjería', '22'),
        (6, 'NIT', '31'),
        (7, 'Pasaporte', '41'),
        (8, 'Documento de identificación extranjero', '42'),
        (9, 'NIT de otro país', '50'),
        (10, 'NUIP', '91')");
    echo "  ✓ Tabla dian_type_documents creada\n";
} catch(PDOException $e) {
    echo "  - dian_type_documents: " . $e->getMessage() . "\n";
}

// dian_type_organizations
$sql = "CREATE TABLE IF NOT EXISTS `dian_type_organizations` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_type_organizations` (`id`, `name`, `code`) VALUES
        (1, 'Persona Jurídica y asimiladas', '1'),
        (2, 'Persona Natural y asimiladas', '2')");
    echo "  ✓ Tabla dian_type_organizations creada\n";
} catch(PDOException $e) {
    echo "  - dian_type_organizations: " . $e->getMessage() . "\n";
}

// dian_type_regimes
$sql = "CREATE TABLE IF NOT EXISTS `dian_type_regimes` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_type_regimes` (`id`, `name`, `code`) VALUES
        (1, 'Responsable de IVA', '48'),
        (2, 'No Responsable de IVA', '49')");
    echo "  ✓ Tabla dian_type_regimes creada\n";
} catch(PDOException $e) {
    echo "  - dian_type_regimes: " . $e->getMessage() . "\n";
}

// dian_type_liabilities
$sql = "CREATE TABLE IF NOT EXISTS `dian_type_liabilities` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `code` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_type_liabilities` (`id`, `name`, `code`) VALUES
        (7, 'Gran contribuyente', 'O-13'),
        (9, 'Autorretenedor', 'O-15'),
        (14, 'Agente de retención en el impuesto sobre las ventas', 'O-23'),
        (112, 'Régimen Simple de Tributación – SIMPLE', 'O-47'),
        (117, 'No aplica – Otros', 'R-99-PN')");
    echo "  ✓ Tabla dian_type_liabilities creada\n";
} catch(PDOException $e) {
    echo "  - dian_type_liabilities: " . $e->getMessage() . "\n";
}

// dian_payment_forms
$sql = "CREATE TABLE IF NOT EXISTS `dian_payment_forms` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_payment_forms` (`id`, `name`, `code`) VALUES
        (1, 'Contado', '1'),
        (2, 'Crédito', '2')");
    echo "  ✓ Tabla dian_payment_forms creada\n";
} catch(PDOException $e) {
    echo "  - dian_payment_forms: " . $e->getMessage() . "\n";
}

// dian_payment_methods
$sql = "CREATE TABLE IF NOT EXISTS `dian_payment_methods` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_payment_methods` (`id`, `name`, `code`) VALUES
        (1, 'Instrumento no definido', '1'),
        (10, 'Efectivo', '10'),
        (20, 'Cheque', '20'),
        (30, 'Transferencia Crédito', '30'),
        (42, 'Consignación bancaria', '42'),
        (47, 'Transferencia Débito Bancaria', '47'),
        (48, 'Tarjeta Crédito', '48'),
        (49, 'Tarjeta Débito', '49'),
        (46, 'Transferencia Débito Interbancario', '46'),
        (45, 'Transferencia Crédito Bancario', '45'),
        (54, 'Nota promisoria', '60'),
        (75, 'Acuerdo mutuo', 'ZZZ')");
    echo "  ✓ Tabla dian_payment_methods creada\n";
} catch(PDOException $e) {
    echo "  - dian_payment_methods: " . $e->getMessage() . "\n";
}

// dian_taxes
$sql = "CREATE TABLE IF NOT EXISTS `dian_taxes` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `description` VARCHAR(200) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_taxes` (`id`, `name`, `description`, `code`) VALUES
        (1, 'IVA', 'Impuesto sobre la Ventas', '01'),
        (2, 'IC', 'Impuesto al Consumo Departamental', '02'),
        (3, 'ICA', 'Impuesto de Industria, Comercio y Aviso', '03'),
        (4, 'INC', 'Impuesto Nacional al Consumo', '04'),
        (5, 'ReteIVA', 'Retención sobre el IVA', '05'),
        (6, 'ReteRenta', 'Retención sobre Renta', '06'),
        (7, 'ReteICA', 'Retención sobre el ICA', '07'),
        (15, 'No aplica', 'Otros tributos', 'ZZ')");
    echo "  ✓ Tabla dian_taxes creada\n";
} catch(PDOException $e) {
    echo "  - dian_taxes: " . $e->getMessage() . "\n";
}

// dian_municipalities (principales)
$sql = "CREATE TABLE IF NOT EXISTS `dian_municipalities` (
    `id` INT PRIMARY KEY,
    `department_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `codefacturador` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_municipalities` (`id`, `department_id`, `name`, `code`, `codefacturador`) VALUES
        (1, 2, 'Medellín', '05001', '12601'),
        (100, 1, 'Bogotá D.C.', '11001', '12001'),
        (200, 3, 'Cali', '76001', '13001'),
        (300, 4, 'Barranquilla', '08001', '14001'),
        (400, 5, 'Cartagena', '13001', '15001'),
        (500, 6, 'Bucaramanga', '68001', '16001'),
        (600, 7, 'Pereira', '66001', '17001'),
        (700, 8, 'Manizales', '17001', '18001'),
        (800, 9, 'Armenia', '63001', '19001'),
        (900, 10, 'Ibagué', '73001', '20001'),
        (1000, 11, 'Neiva', '41001', '21001'),
        (1100, 12, 'Villavicencio', '50001', '22001'),
        (1200, 13, 'Montería', '23001', '23001'),
        (1300, 14, 'Sincelejo', '70001', '24001'),
        (1400, 15, 'Riohacha', '44001', '25001'),
        (1500, 16, 'Valledupar', '20001', '26001'),
        (1600, 17, 'Quibdó', '27001', '27001'),
        (1700, 18, 'Tunja', '15001', '28001'),
        (1800, 19, 'Popayán', '19001', '29001'),
        (1900, 20, 'Mocoa', '86001', '30001'),
        (2000, 21, 'Pasto', '52001', '31001'),
        (2100, 22, 'Cúcuta', '54001', '32001'),
        (2200, 23, 'Santa Marta', '47001', '33001'),
        (2400, 25, 'Florencia', '18001', '35001'),
        (2500, 26, 'Yopal', '85001', '36001'),
        (2600, 27, 'Arauca', '81001', '37001'),
        (2700, 28, 'Leticia', '91001', '38001')");
    echo "  ✓ Tabla dian_municipalities creada\n";
} catch(PDOException $e) {
    echo "  - dian_municipalities: " . $e->getMessage() . "\n";
}

// dian_departments
$sql = "CREATE TABLE IF NOT EXISTS `dian_departments` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_departments` (`id`, `name`, `code`) VALUES
        (1, 'Bogotá D.C.', '11'),
        (2, 'Antioquia', '05'),
        (3, 'Valle del Cauca', '76'),
        (4, 'Atlántico', '08'),
        (5, 'Bolívar', '13'),
        (6, 'Santander', '68'),
        (7, 'Risaralda', '66'),
        (8, 'Caldas', '17'),
        (9, 'Quindío', '63'),
        (10, 'Tolima', '73'),
        (11, 'Huila', '41'),
        (12, 'Meta', '50'),
        (13, 'Córdoba', '23'),
        (14, 'Sucre', '70'),
        (15, 'La Guajira', '44'),
        (16, 'Cesar', '20'),
        (17, 'Chocó', '27'),
        (18, 'Boyacá', '15'),
        (19, 'Cauca', '19'),
        (20, 'Putumayo', '86'),
        (21, 'Nariño', '52'),
        (22, 'Norte de Santander', '54'),
        (23, 'Magdalena', '47'),
        (25, 'Caquetá', '18'),
        (26, 'Casanare', '85'),
        (27, 'Arauca', '81'),
        (28, 'Amazonas', '91'),
        (29, 'Guaviare', '95'),
        (30, 'Vaupés', '97'),
        (31, 'Vichada', '99'),
        (32, 'San Andrés y Providencia', '88')");
    echo "  ✓ Tabla dian_departments creada\n";
} catch(PDOException $e) {
    echo "  - dian_departments: " . $e->getMessage() . "\n";
}

// dian_credit_note_reasons
$sql = "CREATE TABLE IF NOT EXISTS `dian_credit_note_reasons` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_credit_note_reasons` (`id`, `name`) VALUES
        (1, 'Devolución parcial de los bienes y/o no aceptación parcial del servicio'),
        (2, 'Anulación de la factura electrónica'),
        (3, 'Rebaja o descuento parcial o total'),
        (4, 'Ajuste de precio'),
        (5, 'Otros')");
    echo "  ✓ Tabla dian_credit_note_reasons creada\n";
} catch(PDOException $e) {
    echo "  - dian_credit_note_reasons: " . $e->getMessage() . "\n";
}

// dian_debit_note_reasons
$sql = "CREATE TABLE IF NOT EXISTS `dian_debit_note_reasons` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `dian_debit_note_reasons` (`id`, `name`) VALUES
        (1, 'Intereses'),
        (2, 'Gastos por cobrar'),
        (3, 'Cambio del valor'),
        (4, 'Otros')");
    echo "  ✓ Tabla dian_debit_note_reasons creada\n";
} catch(PDOException $e) {
    echo "  - dian_debit_note_reasons: " . $e->getMessage() . "\n";
}

// ============================================================
// PASO 7: Crear tabla tax_configurations
// ============================================================
echo "\n--- PASO 7: Crear tabla tax_configurations ---\n";

$sql = "CREATE TABLE IF NOT EXISTS `tax_configurations` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `tax_id` INT NOT NULL COMMENT 'ID del impuesto en APIDIAN (1=IVA, 2=INC, etc.)',
    `tax_name` VARCHAR(50) NOT NULL,
    `rate` DECIMAL(5,2) NOT NULL,
    `is_default` TINYINT(1) DEFAULT 0,
    `state` INT DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
try {
    $conexion->exec($sql);
    $conexion->exec("INSERT IGNORE INTO `tax_configurations` (`tax_id`, `tax_name`, `rate`, `is_default`, `state`) VALUES
        (1, 'IVA 19%', 19.00, 1, 1),
        (1, 'IVA 5%', 5.00, 0, 1),
        (1, 'Exento', 0.00, 0, 1)");
    echo "  ✓ Tabla tax_configurations creada\n";
} catch(PDOException $e) {
    echo "  - tax_configurations: " . $e->getMessage() . "\n";
}

echo "\n=== MIGRACIÓN COMPLETADA ===\n";
echo "Ahora puede acceder a Ajustes → Facturación Electrónica\n";
