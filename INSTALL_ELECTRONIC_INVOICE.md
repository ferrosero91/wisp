-- ============================================================
-- SCRIPT MAESTRO: Ejecutar en orden para configurar
-- Facturacion Electronica DIAN
-- ============================================================

-- PASO 1: Ejecutar database_electronic_invoice.sql
-- (Campos en business, clients, detail_bills, electronic_invoices, tax_configurations, electronic_resolutions)

-- PASO 2: Ejecutar database_dian_tables.sql
-- (Tablas de referencia DIAN: municipios, tipos documento, impuestos, etc.)

-- Verificar que las tablas se crearon correctamente:
SHOW TABLES LIKE 'dian_%';
SHOW TABLES LIKE 'electronic_%';
SHOW TABLES LIKE 'tax_%';
