-- ============================================================
-- MIGRACION: Tablas de referencia DIAN para Facturacion Electronica
-- Fecha: 2026-07-31
-- Descripcion: Crea las tablas parametricas de APIDIAN/DIAN
-- ============================================================

-- 1. Tipos de documento de identificacion
CREATE TABLE IF NOT EXISTS `dian_type_documents` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_type_documents` (`id`, `name`, `code`) VALUES
(1, 'Registro civil', '11'),
(2, 'Tarjeta de identidad', '12'),
(3, 'Cédula de ciudadanía', '13'),
(4, 'Tarjeta de extranjería', '21'),
(5, 'Cédula de extranjería', '22'),
(6, 'NIT', '31'),
(7, 'Pasaporte', '41'),
(8, 'Documento de identificación extranjero', '42'),
(9, 'NIT de otro país', '50'),
(10, 'NUIP', '91');

-- 2. Tipos de organizacion
CREATE TABLE IF NOT EXISTS `dian_type_organizations` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_type_organizations` (`id`, `name`, `code`) VALUES
(1, 'Persona Jurídica y asimiladas', '1'),
(2, 'Persona Natural y asimiladas', '2');

-- 3. Tipos de regimen
CREATE TABLE IF NOT EXISTS `dian_type_regimes` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_type_regimes` (`id`, `name`, `code`) VALUES
(1, 'Responsable de IVA', '48'),
(2, 'No Responsable de IVA', '49');

-- 4. Responsabilidades tributarias
CREATE TABLE IF NOT EXISTS `dian_type_liabilities` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `code` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_type_liabilities` (`id`, `name`, `code`) VALUES
(7, 'Gran contribuyente', 'O-13'),
(9, 'Autorretenedor', 'O-15'),
(14, 'Agente de retención en el impuesto sobre las ventas', 'O-23'),
(112, 'Régimen Simple de Tributación – SIMPLE', 'O-47'),
(117, 'No aplica – Otros', 'R-99-PN');

-- 5. Formas de pago
CREATE TABLE IF NOT EXISTS `dian_payment_forms` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_payment_forms` (`id`, `name`, `code`) VALUES
(1, 'Contado', '1'),
(2, 'Crédito', '2');

-- 6. Metodos de pago
CREATE TABLE IF NOT EXISTS `dian_payment_methods` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_payment_methods` (`id`, `name`, `code`) VALUES
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
(75, 'Acuerdo mutuo', 'ZZZ');

-- 7. Impuestos
CREATE TABLE IF NOT EXISTS `dian_taxes` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `description` VARCHAR(200) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_taxes` (`id`, `name`, `description`, `code`) VALUES
(1, 'IVA', 'Impuesto sobre la Ventas', '01'),
(2, 'IC', 'Impuesto al Consumo Departamental', '02'),
(3, 'ICA', 'Impuesto de Industria, Comercio y Aviso', '03'),
(4, 'INC', 'Impuesto Nacional al Consumo', '04'),
(5, 'ReteIVA', 'Retención sobre el IVA', '05'),
(6, 'ReteRenta', 'Retención sobre Renta', '06'),
(7, 'ReteICA', 'Retención sobre el ICA', '07'),
(8, 'FtoHorticultura', 'Cuota de Fomento Hortifrutícula', '20'),
(9, 'Timbre', 'Impuesto de Timbre', '21'),
(10, 'INC Bolsas', 'Impuesto al Consumo de Bolsa Plástica', '22'),
(11, 'INCarbono', 'Impuesto Nacional al Carbono', '23'),
(12, 'INCombustibles', 'Impuesto Nacional a los Combustibles', '24'),
(13, 'Sobretasa Combustibles', 'Sobretasa a los combustibles', '25'),
(14, 'Sordicom', 'Contribución minoristas (Combustibles)', '26'),
(15, 'No aplica', 'Otros tributos, tasas, contribuciones, y similares', 'ZZ');

-- 8. Unidades de medida
CREATE TABLE IF NOT EXISTS `dian_unit_measures` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_unit_measures` (`id`, `name`, `code`) VALUES
(1, 'spray pequeño', '04'),
(2, 'levantar', '05'),
(3, 'Lote calor', '08'),
(4, 'grupo', '10'),
(5, 'equipar', '11'),
(6, 'ración', '13'),
(7, 'Disparo', '14'),
(8, 'palo', '15'),
(9, 'tambor de ciento quince kg', '16'),
(10, 'tambor de cien libras', '17'),
(11, 'tambor de cincuenta y cinco galones (US)', '18'),
(12, 'camión cisterna', '19'),
(13, 'contenedor de veinte pies', '20'),
(14, 'contenedor de cuarenta pies', '21'),
(15, 'decilitro por gramo', '22'),
(16, 'gramo por centímetro cúbico', '23'),
(17, 'libra teórica', '24'),
(18, 'gramo por centímetro cuadrado', '25'),
(19, 'tonelada real', '26'),
(20, 'tonelada teórica', '27'),
(21, 'kilogramo por metro cuadrado', '28'),
(22, 'libra por mil pies cuadrados', '29'),
(23, 'Día de potencia del caballo por tonelada métrica seca al aire', '30'),
(24, 'coger peso', '31'),
(25, 'kilogramo por aire seco tonelada métrica', '32'),
(26, 'kilopascales metros cuadrados por gramo', '33'),
(27, 'kilopascales por milímetro', '34'),
(28, 'mililitros por centímetro cuadrado segundo', '35'),
(29, 'pies cúbicos por minuto por pie cuadrado', '36'),
(30, 'onza por pie cuadrado', '37'),
(31, 'galón', '48'),
(32, 'megajoule', '53'),
(33, 'libra por pulgada cuadrada', '54'),
(34, 'pinta', '55'),
(35, 'cuarto de galón', '56'),
(36, 'yarda cuadrada', '58'),
(37, 'pulgada cúbica', '59'),
(38, 'pie cúbico', '60'),
(39, 'pulgada cuadrada', '61'),
(40, 'campo', '62'),
(41, 'acre', '63'),
(42, 'metro cúbico', '64'),
(43, 'kilogramo', '65'),
(44, 'libra', '66'),
(45, 'metro cuadrado', '67'),
(46, 'pie cuadrado', '68'),
(47, 'yarda', '69'),
(48, 'Unidad', '70'),
(49, 'Docena', '71'),
(50, 'Resma', '72'),
(51, 'Hora', '73'),
(52, 'Kilómetro', '74'),
(53, 'Metro', '75'),
(54, 'Litro', '76'),
(55, 'Tonelada', '77'),
(56, 'Barril', '78'),
(57, 'Botella', '79'),
(58, 'Centímetro', '80'),
(59, 'Caja', '81'),
(60, 'Ciento', '82'),
(61, 'Gramo', '83'),
(62, 'Millar', '84'),
(63, 'Megawatt', '85'),
(64, 'Paquete', '86'),
(65, 'Saco', '87'),
(66, 'Frasco', '88'),
(67, 'Tambor', '89'),
(68, 'Whar', '90'),
(69, 'Cajón', '91'),
(70, 'Lata', '92'),
(71, 'Rollo', '93'),
(72, 'Par', '94'),
(73, 'Kilogramo fuerza', '95'),
(74, 'Kilovatio hora', '96'),
(75, 'Juego', '97'),
(76, 'Pulgada', '98'),
(77, 'Pie', '99'),
(78, 'Onza', 'OZ'),
(79, 'Kilowatt', 'KW'),
(80, 'Gramo', 'GRM'),
(81, 'Megawatt hora', 'MWH'),
(82, 'Miligramo', 'MGM'),
(83, 'Mililitro', 'MLT'),
(84, 'Milímetro', 'MMT'),
(85, 'Centímetro cuadrado', 'CMK'),
(86, 'Centímetro cúbico', 'CMQ'),
(87, 'Kilogramo metro', 'KTM'),
(88, 'Kilogramo fuerza por centímetro cuadrado', 'KPC'),
(89, 'Kilopondio', 'KPD'),
(90, 'Kilovatio', 'KVT'),
(91, 'Microfaradio', 'MC'),
(92, 'Microgramo', 'MCG'),
(93, 'Micrómetro', 'MMK'),
(94, 'Microsecondo', 'MS'),
(95, 'Miliampere', 'MA'),
(96, 'Milibar', 'MB'),
(97, 'Miligramo por metro', 'MGM'),
(98, 'Mililitro por segundo', 'MLS'),
(99, 'Milímetro cúbico', 'MMQ'),
(100, 'Milímetro cuadrado', 'MMK');

-- 9. Municipios (solo los principales para no saturar)
CREATE TABLE IF NOT EXISTS `dian_municipalities` (
    `id` INT PRIMARY KEY,
    `department_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `codefacturador` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertar los 32 departamentos principales con sus municipios cabecera
INSERT INTO `dian_municipalities` (`id`, `department_id`, `name`, `code`, `codefacturador`) VALUES
(1, 2, 'Medellín', '05001', '12601'),
(2, 2, 'Abejorral', '05002', '12533'),
(3, 2, 'Abriaquí', '05004', '12534'),
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
(2300, 24, 'Girón', '68001', '34001'),
(2400, 25, 'Florencia', '18001', '35001'),
(2500, 26, 'Yopal', '85001', '36001'),
(2600, 27, 'Arauca', '81001', '37001'),
(2700, 28, 'Leticia', '91001', '38001'),
(2800, 29, 'San José del Guaviare', '95001', '39001'),
(2900, 30, 'Mitú', '97001', '40001'),
(3000, 31, 'Puerto Carreño', '99001', '41001'),
(3100, 32, 'San Andrés', '88001', '42001');

-- 10. Departamentos
CREATE TABLE IF NOT EXISTS `dian_departments` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_departments` (`id`, `name`, `code`) VALUES
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
(24, 'Santander', '68'),
(25, 'Caquetá', '18'),
(26, 'Casanare', '85'),
(27, 'Arauca', '81'),
(28, 'Amazonas', '91'),
(29, 'Guaviare', '95'),
(30, 'Vaupés', '97'),
(31, 'Vichada', '99'),
(32, 'San Andrés y Providencia', '88');

-- 11. Tipos de documentos tributarios (factura, NC, ND, etc.)
CREATE TABLE IF NOT EXISTS `dian_type_tax_documents` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_type_tax_documents` (`id`, `name`, `code`) VALUES
(1, 'Factura electrónica de venta', '01'),
(4, 'Nota Crédito', '91'),
(5, 'Nota Débito', '92'),
(11, 'Documento Soporte en Adquisiciones', '05');

-- 12. Discrepancias para notas credito
CREATE TABLE IF NOT EXISTS `dian_credit_note_reasons` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_credit_note_reasons` (`id`, `name`) VALUES
(1, 'Devolución parcial de los bienes y/o no aceptación parcial del servicio'),
(2, 'Anulación de la factura electrónica'),
(3, 'Rebaja o descuento parcial o total'),
(4, 'Ajuste de precio'),
(5, 'Otros');

-- 13. Discrepancias para notas debito
CREATE TABLE IF NOT EXISTS `dian_debit_note_reasons` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dian_debit_note_reasons` (`id`, `name`) VALUES
(1, 'Intereses'),
(2, 'Gastos por cobrar'),
(3, 'Cambio del valor'),
(4, 'Otros');
