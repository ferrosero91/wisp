-- ============================================================
-- Migración: Agregar índices para optimizar rendimiento
-- Ejecutar en MariaDB/MySQL
-- ============================================================

-- Índices para tabla bills (consultas frecuentes por clientid y state)
CREATE INDEX IF NOT EXISTS idx_bills_clientid_state ON bills(clientid, state);
CREATE INDEX IF NOT EXISTS idx_bills_clientid_type_state ON bills(clientid, type, state);
CREATE INDEX IF NOT EXISTS idx_bills_state ON bills(state);

-- Índices para tabla payments (consultas frecuentes por clientid y billid)
CREATE INDEX IF NOT EXISTS idx_payments_clientid_state ON payments(clientid, state);
CREATE INDEX IF NOT EXISTS idx_payments_billid_state ON payments(billid, state);

-- Índices para tabla contracts (consultas frecuentes por clientid y state)
CREATE INDEX IF NOT EXISTS idx_contracts_clientid_state ON contracts(clientid, state);
CREATE INDEX IF NOT EXISTS idx_contracts_state ON contracts(state);

-- Índices para tabla detail_contracts (consultas frecuentes por contractid)
CREATE INDEX IF NOT EXISTS idx_detail_contracts_contractid ON detail_contracts(contractid);
CREATE INDEX IF NOT EXISTS idx_detail_contracts_serviceid ON detail_contracts(serviceid);

-- Índices para tabla clients (consultas frecuentes por document)
CREATE INDEX IF NOT EXISTS idx_clients_document ON clients(document);

-- Índices para tabla tickets (consultas frecuentes por clientid y state)
CREATE INDEX IF NOT EXISTS idx_tickets_clientid_state ON tickets(clientid, state);

-- Índices para tabla facility (consultas frecuentes por clientid)
CREATE INDEX IF NOT EXISTS idx_facility_clientid ON facility(clientid);

-- Índices para tabla emails (consultas frecuentes por clientid)
CREATE INDEX IF NOT EXISTS idx_emails_clientid ON emails(clientid);
