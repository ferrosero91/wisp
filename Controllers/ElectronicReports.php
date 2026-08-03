<?php
/**
 * Controlador para Reportes de Facturación Electrónica
 * Módulo de reportes dentro de Finanzas
 */
require 'Libraries/dompdf/vendor/autoload.php';
require 'Libraries/spreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Dompdf\Dompdf;

class ElectronicReports extends Controllers {
    
    public function __construct() {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        // Usar permisos del módulo Bills
        consent_permission(BILLS);
    }
    
    /**
     * Vista principal del reporte de facturación electrónica
     */
    public function index() {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
            die();
        }
        
        $data['page_name'] = "Reportes Facturación Electrónica";
        $data['page_title'] = "Reportes de Facturación Electrónica";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Finanzas";
        $data['actual_page'] = "Reportes Electrónicos";
        $data['page_functions_js'] = "electronic_reports.js";
        
        $this->views->getView($this, "index", $data);
    }
    
    /**
     * API: Lista de facturas electrónicas con filtros
     */
    public function list_records() {
        if (empty($_SESSION['permits_module']['v'])) {
            send_json([]);
            die();
        }
        
        $start = isset($_GET['start']) ? $_GET['start'] : date("Y-m-01");
        $end = isset($_GET['end']) ? $_GET['end'] : date("Y-m-t");
        $state = isset($_GET['state']) ? $_GET['state'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        
        // Convertir fechas si vienen en formato d/m/Y
        if (!empty($start) && strpos($start, '/') !== false) {
            $dateStart = DateTime::createFromFormat('d/m/Y', $start);
            $start = $dateStart->format('Y-m-d');
        }
        if (!empty($end) && strpos($end, '/') !== false) {
            $dateEnd = DateTime::createFromFormat('d/m/Y', $end);
            $end = $dateEnd->format('Y-m-d');
        }
        
        $data = $this->model->list_electronic_invoices($start, $end, $state, $type);
        
        for ($i = 0; $i < count($data); $i++) {
            // Encriptar ID para uso en JavaScript
            $data[$i]['encrypted_id'] = encrypt($data[$i]['id']);
            
            // Formatear estado
            switch (intval($data[$i]['electronic_state'])) {
                case EINVOICE_PENDING:
                    $data[$i]['state_label'] = '<span class="label label-warning">PENDIENTE</span>';
                    break;
                case EINVOICE_AUTHORIZED:
                    $data[$i]['state_label'] = '<span class="label label-success">AUTORIZADA</span>';
                    break;
                case EINVOICE_REJECTED:
                    $data[$i]['state_label'] = '<span class="label label-danger">RECHAZADA</span>';
                    break;
                default:
                    $data[$i]['state_label'] = '<span class="label label-default">DESCONOCIDO</span>';
            }
            
            // Formatear tipo de documento
            switch ($data[$i]['type_document']) {
                case 'invoice':
                    $data[$i]['type_label'] = '<span class="label label-info">FACTURA</span>';
                    break;
                case 'credit-note':
                    $data[$i]['type_label'] = '<span class="label label-warning">NOTA CRÉDITO</span>';
                    break;
                case 'debit-note':
                    $data[$i]['type_label'] = '<span class="label label-danger">NOTA DÉBITO</span>';
                    break;
                default:
                    $data[$i]['type_label'] = '<span class="label label-default">' . strtoupper($data[$i]['type_document']) . '</span>';
            }
            
            // Formatear montos
            $data[$i]['total_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($data[$i]['bill_total']);
            
            // Formatear fechas
            $data[$i]['date_issue_formatted'] = date("d/m/Y", strtotime($data[$i]['date_issue']));
            $data[$i]['created_at_formatted'] = date("d/m/Y H:i", strtotime($data[$i]['created_at']));
            
            // Número de documento formateado
            $data[$i]['document_number'] = $data[$i]['prefix'] . '-' . str_pad($data[$i]['number'], 7, "0", STR_PAD_LEFT);
            
            // CUFE truncado
            if (!empty($data[$i]['cufe'])) {
                $data[$i]['cufe_short'] = substr($data[$i]['cufe'], 0, 20) . '...';
            } else {
                $data[$i]['cufe_short'] = 'N/A';
            }
            
            // Botones de acción
            $data[$i]['options'] = $this->generate_action_buttons($data[$i]);
        }
        
        send_json($data);
        die();
    }
    
    /**
     * Genera los botones de acción para cada registro
     */
    private function generate_action_buttons(array $record): string {
        $buttons = '<div class="action-buttons">';
        
        // Ver detalle (usar ID encriptado)
        $buttons .= '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Ver detalle" onclick="viewDetail(\'' . $record['encrypted_id'] . '\')"><i class="fa fa-eye"></i></a>';
        
        // Ver factura electrónica
        if ($record['electronic_state'] == EINVOICE_AUTHORIZED) {
            if ($record['type_document'] == 'invoice') {
                $buttons .= '<a href="javascript:;" class="green" data-toggle="tooltip" data-original-title="Ver factura electrónica" onclick="viewElectronic(\'' . encrypt($record['billid']) . '\')"><i class="fas fa-file-invoice"></i></a>';
            } elseif ($record['type_document'] == 'credit-note') {
                $buttons .= '<a href="javascript:;" class="green" data-toggle="tooltip" data-original-title="Ver nota crédito" onclick="viewCreditNote(\'' . encrypt($record['billid']) . '\')"><i class="fas fa-file-medical"></i></a>';
            }
        }
        
        // Descargar PDF
        if (!empty($record['pdf_filename'])) {
            $pdf_url = apidian_get_view_url($record['pdf_filename']);
            $buttons .= '<a href="' . $pdf_url . '" target="_blank" class="red" data-toggle="tooltip" data-original-title="Descargar PDF"><i class="fas fa-file-pdf"></i></a>';
        }
        
        // Descargar XML
        if (!empty($record['xml_filename'])) {
            $xml_url = apidian_get_view_url($record['xml_filename']);
            $buttons .= '<a href="' . $xml_url . '" target="_blank" class="black" data-toggle="tooltip" data-original-title="Descargar XML"><i class="fas fa-file-code"></i></a>';
        }
        
        $buttons .= '</div>';
        
        return $buttons;
    }
    
    /**
     * API: Estadísticas de facturación electrónica
     */
    public function get_stats() {
        if (empty($_SESSION['permits_module']['v'])) {
            send_json(['status' => 'error', 'msg' => 'Sin permisos']);
            die();
        }
        
        $start = isset($_GET['start']) ? $_GET['start'] : date("Y-m-01");
        $end = isset($_GET['end']) ? $_GET['end'] : date("Y-m-t");
        
        // Convertir fechas si vienen en formato d/m/Y
        if (!empty($start) && strpos($start, '/') !== false) {
            $dateStart = DateTime::createFromFormat('d/m/Y', $start);
            $start = $dateStart->format('Y-m-d');
        }
        if (!empty($end) && strpos($end, '/') !== false) {
            $dateEnd = DateTime::createFromFormat('d/m/Y', $end);
            $end = $dateEnd->format('Y-m-d');
        }
        
        $stats = $this->model->get_electronic_stats($start, $end);
        $stats['total_amount_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($stats['total_amount']);
        
        send_json(['status' => 'success', 'data' => $stats]);
        die();
    }
    
    /**
     * API: Detalle de una factura electrónica
     */
    public function get_detail(string $id) {
        if (empty($_SESSION['permits_module']['v'])) {
            send_json(['status' => 'error', 'msg' => 'Sin permisos']);
            die();
        }
        
        $id = intval(decrypt($id));
        if ($id <= 0) {
            send_json(['status' => 'error', 'msg' => 'ID inválido']);
            die();
        }
        
        $detail = $this->model->get_electronic_invoice_detail($id);
        if (empty($detail)) {
            send_json(['status' => 'error', 'msg' => 'Registro no encontrado']);
            die();
        }
        
        // Obtener items de la factura
        $items = $this->model->get_bill_items($detail['billid']);
        $detail['items'] = $items;
        
        // Obtener pagos de la factura
        $payments = $this->model->get_bill_payments($detail['billid']);
        $detail['payments'] = $payments;
        
        // Construir URLs de APIDIAN para PDF y XML
        $detail['pdf_url'] = '';
        $detail['xml_url'] = '';
        if (!empty($detail['pdf_filename'])) {
            $detail['pdf_url'] = apidian_get_view_url($detail['pdf_filename']);
        }
        if (!empty($detail['xml_filename'])) {
            $detail['xml_url'] = apidian_get_view_url($detail['xml_filename']);
        }
        
        // ID encriptado de la factura para botones
        $detail['encrypted_billid'] = encrypt($detail['billid']);
        
        // Formatear montos
        $detail['total_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($detail['bill_total']);
        $detail['subtotal_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($detail['bill_subtotal']);
        $detail['discount_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($detail['bill_discount']);
        
        // Formatear fechas
        $detail['date_issue_formatted'] = date("d/m/Y", strtotime($detail['date_issue']));
        $detail['created_at_formatted'] = date("d/m/Y H:i:s", strtotime($detail['created_at']));
        
        // Número de documento formateado
        $detail['document_number'] = $detail['prefix'] . '-' . str_pad($detail['number'], 7, "0", STR_PAD_LEFT);
        
        // Estado
        switch (intval($detail['electronic_state'])) {
            case EINVOICE_PENDING:
                $detail['state_label'] = 'PENDIENTE';
                $detail['state_class'] = 'warning';
                break;
            case EINVOICE_AUTHORIZED:
                $detail['state_label'] = 'AUTORIZADA';
                $detail['state_class'] = 'success';
                break;
            case EINVOICE_REJECTED:
                $detail['state_label'] = 'RECHAZADA';
                $detail['state_class'] = 'danger';
                break;
            default:
                $detail['state_label'] = 'DESCONOCIDO';
                $detail['state_class'] = 'default';
        }
        
        // Tipo de documento
        switch ($detail['type_document']) {
            case 'invoice':
                $detail['type_label'] = 'FACTURA ELECTRÓNICA';
                break;
            case 'credit-note':
                $detail['type_label'] = 'NOTA CRÉDITO ELECTRÓNICA';
                break;
            case 'debit-note':
                $detail['type_label'] = 'NOTA DÉBITO ELECTRÓNICA';
                break;
            default:
                $detail['type_label'] = strtoupper($detail['type_document']);
        }
        
        send_json(['status' => 'success', 'data' => $detail]);
        die();
    }
    
    /**
     * Ver representación gráfica de factura electrónica
     */
    public function view_electronic($idbill) {
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        
        // Redirigir al controlador ElectronicInvoice
        header('Location: ' . base_url() . '/electronicinvoice/view_electronic/' . $idbill);
        die();
    }
    
    /**
     * Ver representación gráfica de nota crédito electrónica
     */
    public function view_credit_note($idbill) {
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        
        // Redirigir al controlador ElectronicInvoice
        header('Location: ' . base_url() . '/electronicinvoice/view_credit_note/' . $idbill);
        die();
    }
    
    /**
     * API: Resumen mensual
     */
    public function get_monthly_summary() {
        if (empty($_SESSION['permits_module']['v'])) {
            send_json(['status' => 'error', 'msg' => 'Sin permisos']);
            die();
        }
        
        $year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
        
        $summary = $this->model->get_monthly_summary($year);
        
        // Formatear montos
        for ($i = 0; $i < count($summary); $i++) {
            $summary[$i]['total_amount_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($summary[$i]['total_amount']);
            $summary[$i]['month_name'] = months()[intval($summary[$i]['month']) - 1];
        }
        
        send_json(['status' => 'success', 'data' => $summary, 'year' => $year]);
        die();
    }
    
    /**
     * API: Documentos por tipo
     */
    public function get_documents_by_type() {
        if (empty($_SESSION['permits_module']['v'])) {
            send_json(['status' => 'error', 'msg' => 'Sin permisos']);
            die();
        }
        
        $start = isset($_GET['start']) ? $_GET['start'] : date("Y-m-01");
        $end = isset($_GET['end']) ? $_GET['end'] : date("Y-m-t");
        
        // Convertir fechas si vienen en formato d/m/Y
        if (!empty($start) && strpos($start, '/') !== false) {
            $dateStart = DateTime::createFromFormat('d/m/Y', $start);
            $start = $dateStart->format('Y-m-d');
        }
        if (!empty($end) && strpos($end, '/') !== false) {
            $dateEnd = DateTime::createFromFormat('d/m/Y', $end);
            $end = $dateEnd->format('Y-m-d');
        }
        
        $documents = $this->model->get_documents_by_type($start, $end);
        
        // Formatear montos y nombres
        for ($i = 0; $i < count($documents); $i++) {
            $documents[$i]['total_amount_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($documents[$i]['total_amount']);
            
            switch ($documents[$i]['type_document']) {
                case 'invoice':
                    $documents[$i]['type_name'] = 'Facturas';
                    break;
                case 'credit-note':
                    $documents[$i]['type_name'] = 'Notas Crédito';
                    break;
                case 'debit-note':
                    $documents[$i]['type_name'] = 'Notas Débito';
                    break;
                default:
                    $documents[$i]['type_name'] = $documents[$i]['type_document'];
            }
        }
        
        send_json(['status' => 'success', 'data' => $documents]);
        die();
    }
    
    /**
     * API: Últimos documentos
     */
    public function get_recent() {
        if (empty($_SESSION['permits_module']['v'])) {
            send_json(['status' => 'error', 'msg' => 'Sin permisos']);
            die();
        }
        
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        
        $documents = $this->model->get_recent_documents($limit);
        
        for ($i = 0; $i < count($documents); $i++) {
            $documents[$i]['total_formatted'] = $_SESSION['businessData']['symbol'] . ' ' . format_money($documents[$i]['bill_total']);
            $documents[$i]['created_at_formatted'] = date("d/m/Y H:i", strtotime($documents[$i]['created_at']));
            $documents[$i]['document_number'] = $documents[$i]['prefix'] . '-' . str_pad($documents[$i]['number'], 7, "0", STR_PAD_LEFT);
            
            switch (intval($documents[$i]['electronic_state'])) {
                case EINVOICE_PENDING:
                    $documents[$i]['state_label'] = '<span class="label label-warning">PENDIENTE</span>';
                    break;
                case EINVOICE_AUTHORIZED:
                    $documents[$i]['state_label'] = '<span class="label label-success">AUTORIZADA</span>';
                    break;
                case EINVOICE_REJECTED:
                    $documents[$i]['state_label'] = '<span class="label label-danger">RECHAZADA</span>';
                    break;
                default:
                    $documents[$i]['state_label'] = '<span class="label label-default">DESCONOCIDO</span>';
            }
        }
        
        send_json(['status' => 'success', 'data' => $documents]);
        die();
    }
    
    /**
     * Exportar reporte a Excel
     */
    public function export() {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
            die();
        }
        
        $start = isset($_GET['start']) ? $_GET['start'] : date("Y-m-01");
        $end = isset($_GET['end']) ? $_GET['end'] : date("Y-m-t");
        $state = isset($_GET['state']) ? $_GET['state'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        
        $data = $this->model->list_electronic_invoices($start, $end, $state, $type);
        
        $spreadsheet = new Spreadsheet();
        $active_sheet = $spreadsheet->getActiveSheet();
        
        // Estilo del encabezado
        $style_header = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'color' => array('rgb' => 'ffffff'),
            ),
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('rgb' => '2D3036'),
                ),
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('rgb' => '2D3036'),
            ),
        );
        
        $active_sheet->getStyle('A1:J1')->applyFromArray($style_header);
        
        // Configurar columnas
        $active_sheet->setTitle("Facturación Electrónica");
        $active_sheet->getColumnDimension('A')->setAutoSize(true);
        $active_sheet->getColumnDimension('B')->setAutoSize(true);
        $active_sheet->getColumnDimension('C')->setAutoSize(true);
        $active_sheet->getColumnDimension('D')->setAutoSize(true);
        $active_sheet->getColumnDimension('E')->setAutoSize(true);
        $active_sheet->getColumnDimension('F')->setAutoSize(true);
        $active_sheet->getColumnDimension('G')->setAutoSize(true);
        $active_sheet->getColumnDimension('H')->setAutoSize(true);
        $active_sheet->getColumnDimension('I')->setAutoSize(true);
        $active_sheet->getColumnDimension('J')->setAutoSize(true);
        
        // Encabezados
        $active_sheet->setCellValue('A1', 'Nº DOCUMENTO');
        $active_sheet->setCellValue('B1', 'TIPO');
        $active_sheet->setCellValue('C1', 'CLIENTE');
        $active_sheet->setCellValue('D1', 'DOCUMENTO');
        $active_sheet->setCellValue('E1', 'FECHA EMISIÓN');
        $active_sheet->setCellValue('F1', 'TOTAL');
        $active_sheet->setCellValue('G1', 'ESTADO');
        $active_sheet->setCellValue('H1', 'CUFE');
        $active_sheet->setCellValue('I1', 'FECHA DIAN');
        $active_sheet->setCellValue('J1', 'FACTURA');
        
        // Datos
        $row = 2;
        foreach ($data as $record) {
            $active_sheet->setCellValue('A' . $row, $record['prefix'] . '-' . str_pad($record['number'], 7, "0", STR_PAD_LEFT));
            
            switch ($record['type_document']) {
                case 'invoice':
                    $active_sheet->setCellValue('B' . $row, 'FACTURA');
                    break;
                case 'credit-note':
                    $active_sheet->setCellValue('B' . $row, 'NOTA CRÉDITO');
                    break;
                case 'debit-note':
                    $active_sheet->setCellValue('B' . $row, 'NOTA DÉBITO');
                    break;
                default:
                    $active_sheet->setCellValue('B' . $row, strtoupper($record['type_document']));
            }
            
            $active_sheet->setCellValue('C' . $row, $record['client_name']);
            $active_sheet->setCellValue('D' . $row, $record['client_document']);
            $active_sheet->setCellValue('E' . $row, date("d/m/Y", strtotime($record['date_issue'])));
            $active_sheet->setCellValue('F' . $row, floatval($record['bill_total']));
            
            switch (intval($record['electronic_state'])) {
                case EINVOICE_PENDING:
                    $active_sheet->setCellValue('G' . $row, 'PENDIENTE');
                    break;
                case EINVOICE_AUTHORIZED:
                    $active_sheet->setCellValue('G' . $row, 'AUTORIZADA');
                    break;
                case EINVOICE_REJECTED:
                    $active_sheet->setCellValue('G' . $row, 'RECHAZADA');
                    break;
                default:
                    $active_sheet->setCellValue('G' . $row, 'DESCONOCIDO');
            }
            
            $active_sheet->setCellValue('H' . $row, $record['cufe'] ?? 'N/A');
            $active_sheet->setCellValue('I' . $row, date("d/m/Y H:i", strtotime($record['created_at'])));
            $active_sheet->setCellValue('J' . $row, $record['bill_code']);
            
            $row++;
        }
        
        // Configurar formato de moneda
        $style_currency = array(
            'numberFormat' => array(
                'formatCode' => '#,##0.00',
            ),
        );
        $active_sheet->getStyle('F2:F' . ($row - 1))->applyFromArray($style_currency);
        
        // Descargar archivo
        $title = 'Reporte_Facturacion_Electronica_' . date('Y-m-d');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
