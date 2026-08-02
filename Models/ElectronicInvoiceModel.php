<?php
class ElectronicInvoiceModel extends Mysql{
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Buscar factura electrónica por ID de factura
     */
    public function find_by_bill(int $billid){
        $sql = "SELECT * FROM electronic_invoices WHERE billid = ? AND type_document = 'invoice' ORDER BY id DESC LIMIT 1";
        $data = array($billid);
        $request = $this->select($sql, $data);
        return $request;
    }
    
    /**
     * Buscar nota crédito por ID de factura
     */
    public function find_credit_note_by_bill(int $billid){
        $sql = "SELECT * FROM electronic_invoices WHERE billid = ? AND type_document = 'credit-note' AND electronic_state = 1 ORDER BY id DESC LIMIT 1";
        $data = array($billid);
        $request = $this->select($sql, $data);
        return $request;
    }
    
    /**
     * Buscar nota débito por ID de factura
     */
    public function find_debit_note_by_bill(int $billid){
        $sql = "SELECT * FROM electronic_invoices WHERE billid = ? AND type_document = 'debit-note' AND electronic_state = 1 ORDER BY id DESC LIMIT 1";
        $data = array($billid);
        $request = $this->select($sql, $data);
        return $request;
    }
    
    /**
     * Buscar cualquier documento electrónico por ID de factura y tipo
     */
    public function find_by_bill_and_type(int $billid, string $type_document){
        $sql = "SELECT * FROM electronic_invoices WHERE billid = ? AND type_document = ? ORDER BY id DESC LIMIT 1";
        $data = array($billid, $type_document);
        $request = $this->select($sql, $data);
        return $request;
    }
    
    /**
     * Guardar factura electrónica
     */
    public function save_electronic_invoice(int $billid, string $type_document, int $type_document_id, string $prefix, int $number, string $cufe, int $state, string $xml_filename, string $pdf_filename, string $qr_string, string $dian_response){
        // Verificar si ya existe un documento del mismo tipo para esta factura
        $existing = $this->find_by_bill_and_type($billid, $type_document);
        
        if(!empty($existing)){
            // Actualizar
            $sql = "UPDATE electronic_invoices SET cufe=?, electronic_state=?, xml_filename=?, pdf_filename=?, qr_string=?, dian_response=?, updated_at=NOW() WHERE id = ?";
            $data = array($cufe, $state, $xml_filename, $pdf_filename, $qr_string, $dian_response, $existing['id']);
            $request = $this->update($sql, $data);
        }else{
            // Insertar nuevo registro
            $sql = "INSERT INTO electronic_invoices(billid, type_document, type_document_id, prefix, number, cufe, electronic_state, xml_filename, pdf_filename, qr_string, dian_response, created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,NOW())";
            $data = array($billid, $type_document, $type_document_id, $prefix, $number, $cufe, $state, $xml_filename, $pdf_filename, $qr_string, $dian_response);
            $request = $this->insert($sql, $data);
        }
        
        return $request ? 'success' : 'error';
    }
    
    /**
     * Listar facturas electrónicas
     */
    public function list_electronic_invoices(){
        $sql = "SELECT ei.*, b.internal_code, b.total, b.date_issue, c.names, c.surnames, c.document 
                FROM electronic_invoices ei 
                JOIN bills b ON ei.billid = b.id 
                JOIN clients c ON b.clientid = c.id 
                ORDER BY ei.id DESC";
        $request = $this->select_all($sql);
        return $request;
    }
}
