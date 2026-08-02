<?php
/**
 * Modelo para Reportes de Facturación Electrónica
 * Proporciona consultas optimizadas para el módulo de reportes
 */
class ElectronicReportsModel extends Mysql {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lista todas las facturas electrónicas con filtros
     */
    public function list_electronic_invoices(string $start = '', string $end = '', string $state = '', string $type = ''): array {
        $sql = "SELECT 
                    ei.id,
                    ei.billid,
                    ei.type_document,
                    ei.type_document_id,
                    ei.prefix,
                    ei.number,
                    ei.cufe,
                    ei.electronic_state,
                    ei.xml_filename,
                    ei.pdf_filename,
                    ei.created_at,
                    b.internal_code AS bill_code,
                    b.total AS bill_total,
                    b.subtotal AS bill_subtotal,
                    b.discount AS bill_discount,
                    b.date_issue,
                    b.expiration_date,
                    b.billed_month,
                    b.state AS bill_state,
                    CONCAT_WS(' ', c.names, c.surnames) AS client_name,
                    c.document AS client_document,
                    c.email AS client_email,
                    c.mobile AS client_mobile,
                    d.document AS document_type
                FROM electronic_invoices ei
                JOIN bills b ON ei.billid = b.id
                JOIN clients c ON b.clientid = c.id
                LEFT JOIN document_type d ON c.documentid = d.id
                WHERE 1=1";
        
        $data = array();
        
        if (!empty($start) && !empty($end)) {
            $sql .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data[] = $start;
            $data[] = $end;
        }
        
        if (!empty($state) && $state !== '0') {
            $sql .= " AND ei.electronic_state = ?";
            $data[] = intval($state);
        }
        
        if (!empty($type) && $type !== '0') {
            $sql .= " AND ei.type_document = ?";
            $data[] = $type;
        }
        
        $sql .= " ORDER BY ei.id DESC";
        
        return $this->select_all($sql, !empty($data) ? $data : null);
    }
    
    /**
     * Obtiene estadísticas de facturación electrónica
     */
    public function get_electronic_stats(string $start = '', string $end = ''): array {
        $stats = array();
        
        // Total de documentos electrónicos
        $sql_total = "SELECT COUNT(*) as total FROM electronic_invoices ei WHERE 1=1";
        $data_total = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_total .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_total[] = $start;
            $data_total[] = $end;
        }
        
        $result_total = $this->select($sql_total, !empty($data_total) ? $data_total : null);
        $stats['total_documents'] = $result_total['total'] ?? 0;
        
        // Documentos autorizados
        $sql_authorized = "SELECT COUNT(*) as total FROM electronic_invoices ei WHERE ei.electronic_state = 1";
        $data_authorized = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_authorized .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_authorized[] = $start;
            $data_authorized[] = $end;
        }
        
        $result_authorized = $this->select($sql_authorized, !empty($data_authorized) ? $data_authorized : null);
        $stats['authorized'] = $result_authorized['total'] ?? 0;
        
        // Documentos pendientes
        $sql_pending = "SELECT COUNT(*) as total FROM electronic_invoices ei WHERE ei.electronic_state = 0";
        $data_pending = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_pending .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_pending[] = $start;
            $data_pending[] = $end;
        }
        
        $result_pending = $this->select($sql_pending, !empty($data_pending) ? $data_pending : null);
        $stats['pending'] = $result_pending['total'] ?? 0;
        
        // Documentos rechazados
        $sql_rejected = "SELECT COUNT(*) as total FROM electronic_invoices ei WHERE ei.electronic_state = 2";
        $data_rejected = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_rejected .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_rejected[] = $start;
            $data_rejected[] = $end;
        }
        
        $result_rejected = $this->select($sql_rejected, !empty($data_rejected) ? $data_rejected : null);
        $stats['rejected'] = $result_rejected['total'] ?? 0;
        
        // Total facturado electrónicamente
        $sql_total_amount = "SELECT COALESCE(SUM(b.total), 0) as total 
                            FROM electronic_invoices ei 
                            JOIN bills b ON ei.billid = b.id 
                            WHERE ei.electronic_state = 1";
        $data_total_amount = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_total_amount .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_total_amount[] = $start;
            $data_total_amount[] = $end;
        }
        
        $result_total_amount = $this->select($sql_total_amount, !empty($data_total_amount) ? $data_total_amount : null);
        $stats['total_amount'] = $result_total_amount['total'] ?? 0;
        
        // Facturas electrónicas
        $sql_invoices = "SELECT COUNT(*) as total FROM electronic_invoices ei WHERE ei.type_document = 'invoice'";
        $data_invoices = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_invoices .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_invoices[] = $start;
            $data_invoices[] = $end;
        }
        
        $result_invoices = $this->select($sql_invoices, !empty($data_invoices) ? $data_invoices : null);
        $stats['invoices'] = $result_invoices['total'] ?? 0;
        
        // Notas crédito
        $sql_credit_notes = "SELECT COUNT(*) as total FROM electronic_invoices ei WHERE ei.type_document = 'credit-note'";
        $data_credit_notes = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_credit_notes .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_credit_notes[] = $start;
            $data_credit_notes[] = $end;
        }
        
        $result_credit_notes = $this->select($sql_credit_notes, !empty($data_credit_notes) ? $data_credit_notes : null);
        $stats['credit_notes'] = $result_credit_notes['total'] ?? 0;
        
        // Notas débito
        $sql_debit_notes = "SELECT COUNT(*) as total FROM electronic_invoices ei WHERE ei.type_document = 'debit-note'";
        $data_debit_notes = array();
        
        if (!empty($start) && !empty($end)) {
            $sql_debit_notes .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data_debit_notes[] = $start;
            $data_debit_notes[] = $end;
        }
        
        $result_debit_notes = $this->select($sql_debit_notes, !empty($data_debit_notes) ? $data_debit_notes : null);
        $stats['debit_notes'] = $result_debit_notes['total'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Obtiene el detalle completo de una factura electrónica
     */
    public function get_electronic_invoice_detail(int $id): array {
        $sql = "SELECT 
                    ei.*,
                    b.internal_code AS bill_code,
                    b.total AS bill_total,
                    b.subtotal AS bill_subtotal,
                    b.discount AS bill_discount,
                    b.date_issue,
                    b.expiration_date,
                    b.billed_month,
                    b.state AS bill_state,
                    b.observation,
                    CONCAT_WS(' ', c.names, c.surnames) AS client_name,
                    c.document AS client_document,
                    c.email AS client_email,
                    c.mobile AS client_mobile,
                    c.address AS client_address,
                    d.document AS document_type
                FROM electronic_invoices ei
                JOIN bills b ON ei.billid = b.id
                JOIN clients c ON b.clientid = c.id
                LEFT JOIN document_type d ON c.documentid = d.id
                WHERE ei.id = ?";
        
        $data = array($id);
        return $this->select($sql, $data);
    }
    
    /**
     * Obtiene el detalle de items de una factura
     */
    public function get_bill_items(int $billid): array {
        $sql = "SELECT 
                    db.description,
                    db.quantity,
                    db.price,
                    db.total,
                    db.type
                FROM detail_bills db
                WHERE db.billid = ?
                ORDER BY db.id ASC";
        
        $data = array($billid);
        return $this->select_all($sql, $data);
    }
    
    /**
     * Obtiene los pagos de una factura
     */
    public function get_bill_payments(int $billid): array {
        $sql = "SELECT 
                    p.id,
                    p.amount_paid,
                    p.payment_date,
                    p.comment,
                    fp.payment_type
                FROM payments p
                JOIN forms_payment fp ON p.paytypeid = fp.id
                WHERE p.billid = ? AND p.state = 1
                ORDER BY p.payment_date DESC";
        
        $data = array($billid);
        return $this->select_all($sql, $data);
    }
    
    /**
     * Obtiene resumen mensual de facturación electrónica
     */
    public function get_monthly_summary(int $year): array {
        $sql = "SELECT 
                    MONTH(ei.created_at) AS month,
                    COUNT(*) AS total_documents,
                    SUM(CASE WHEN ei.electronic_state = 1 THEN 1 ELSE 0 END) AS authorized,
                    SUM(CASE WHEN ei.electronic_state = 0 THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN ei.electronic_state = 2 THEN 1 ELSE 0 END) AS rejected,
                    COALESCE(SUM(CASE WHEN ei.electronic_state = 1 THEN b.total ELSE 0 END), 0) AS total_amount
                FROM electronic_invoices ei
                JOIN bills b ON ei.billid = b.id
                WHERE YEAR(ei.created_at) = ?
                GROUP BY MONTH(ei.created_at)
                ORDER BY MONTH(ei.created_at)";
        
        $data = array($year);
        return $this->select_all($sql, $data);
    }
    
    /**
     * Obtiene los documentos electrónicos por tipo
     */
    public function get_documents_by_type(string $start = '', string $end = ''): array {
        $sql = "SELECT 
                    ei.type_document,
                    COUNT(*) AS total,
                    COALESCE(SUM(CASE WHEN ei.electronic_state = 1 THEN b.total ELSE 0 END), 0) AS total_amount
                FROM electronic_invoices ei
                JOIN bills b ON ei.billid = b.id
                WHERE 1=1";
        
        $data = array();
        
        if (!empty($start) && !empty($end)) {
            $sql .= " AND DATE(ei.created_at) >= ? AND DATE(ei.created_at) <= ?";
            $data[] = $start;
            $data[] = $end;
        }
        
        $sql .= " GROUP BY ei.type_document ORDER BY total DESC";
        
        return $this->select_all($sql, !empty($data) ? $data : null);
    }
    
    /**
     * Obtiene los últimos documentos electrónicos
     */
    public function get_recent_documents(int $limit = 10): array {
        $sql = "SELECT 
                    ei.id,
                    ei.billid,
                    ei.type_document,
                    ei.prefix,
                    ei.number,
                    ei.cufe,
                    ei.electronic_state,
                    ei.created_at,
                    b.total AS bill_total,
                    CONCAT_WS(' ', c.names, c.surnames) AS client_name
                FROM electronic_invoices ei
                JOIN bills b ON ei.billid = b.id
                JOIN clients c ON b.clientid = c.id
                ORDER BY ei.id DESC
                LIMIT ?";
        
        $data = array($limit);
        return $this->select_all($sql, $data);
    }
    
    /**
     * Verifica si una factura ya tiene documento electrónico
     */
    public function has_electronic_document(int $billid): bool {
        $sql = "SELECT COUNT(*) as total FROM electronic_invoices WHERE billid = ?";
        $data = array($billid);
        $result = $this->select($sql, $data);
        return intval($result['total']) > 0;
    }
    
    /**
     * Obtiene el estado de un documento electrónico
     */
    public function get_document_state(int $billid): array {
        $sql = "SELECT 
                    type_document,
                    electronic_state,
                    cufe,
                    prefix,
                    number,
                    created_at
                FROM electronic_invoices 
                WHERE billid = ?
                ORDER BY id DESC";
        
        $data = array($billid);
        return $this->select_all($sql, $data);
    }
}
