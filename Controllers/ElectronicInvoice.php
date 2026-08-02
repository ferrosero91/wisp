<?php
class ElectronicInvoice extends Controllers{
    public function __construct(){
        parent::__construct();
        session_start();
        if(empty($_SESSION['login'])){
            header('Location: '.base_url().'/login');
            die();
        }
    }
    
    /**
     * Enviar factura electrónica a DIAN via APIDIAN
     */
    public function send_to_dian(){
        if($_POST){
            try {
                $idbill = intval(decrypt($_POST['idbill']));
                
                if($idbill <= 0){
                    $response = array('status' => 'error', 'msg' => 'ID de factura inválido.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                // Verificar configuración
                $token = $_SESSION['businessData']['apidian_token'] ?? '';
                if(empty($token)){
                    $response = array('status' => 'error', 'msg' => 'Primero configure la empresa en APIDIAN.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                if(($_SESSION['businessData']['apidian_software_configured'] ?? 0) == 0){
                    $response = array('status' => 'error', 'msg' => 'Primero configure el software DIAN.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                // Obtener datos de la factura
                require_once("Models/BillsModel.php");
                $billsModel = new BillsModel();
                
                $bill_data = $billsModel->view_bill($idbill);
                if(empty($bill_data)){
                    $response = array('status' => 'error', 'msg' => 'Factura no encontrada.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                $bill = $bill_data['bill'];
                $details = $bill_data['detail'];
                
                // Verificar si ya fue enviada
                require_once("Models/ElectronicInvoiceModel.php");
                $eiModel = new ElectronicInvoiceModel();
                $existing = $eiModel->find_by_bill($idbill);
                if(!empty($existing) && $existing['electronic_state'] == 1){
                    $response = array('status' => 'error', 'msg' => 'Esta factura ya fue enviada y autorizada.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                // Obtener datos del cliente
                $client = $billsModel->select_client($bill['clientid']);
                if(empty($client)){
                    $response = array('status' => 'error', 'msg' => 'Cliente no encontrado.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                // Verificar que hay detalles
                if(empty($details)){
                    $response = array('status' => 'error', 'msg' => 'La factura no tiene detalles.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                // Obtener resolución activa para facturas
                require_once("Models/ElectronicResolutionModel.php");
                $resModel = new ElectronicResolutionModel();
                $resolution = $resModel->get_active_by_type(1); // Tipo 1 = Factura
                
                if(empty($resolution)){
                    $response = array('status' => 'error', 'msg' => 'No hay una resolución de facturación electrónica activa.');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                // Construir datos de factura
                $invoice_data = apidian_build_invoice_data($bill, $client, $details, $resolution);
                
                // Enviar a APIDIAN (modo síncrono)
                $result = apidian_send_invoice($token, $invoice_data);
                
                $api_success = isset($result['response']['success']) && $result['response']['success'];
                $api_msg = $result['response']['message'] ?? ($result['response']['msg'] ?? '');
                if (!$api_success && !empty($api_msg) && preg_match('/(generad[ao]|exitos[ao]|autorizad[ao]|cread[ao]|éxito|exito)/i', $api_msg)) {
                    $api_success = true;
                }
                
                if($result['code'] == 200 && $api_success){
                    // Guardar en BD
                    $eiModel->save_electronic_invoice(
                        $idbill,
                        'invoice',
                        1,
                        $resolution['prefix'],
                        $resolution['current_consecutive'],
                        $result['response']['cufe'] ?? '',
                        1, // Autorizado
                        $result['response']['urlinvoicexml'] ?? '',
                        $result['response']['urlinvoicepdf'] ?? '',
                        $result['response']['QRStr'] ?? '',
                        json_encode($result['response'])
                    );
                    
                    // Incrementar consecutivo
                    $resModel->increment_consecutive($resolution['id']);
                    
                    $msg_success = !empty($api_msg) ? $api_msg : 'Factura electrónica autorizada exitosamente.';
                    
                    $response = array(
                        'status' => 'success',
                        'msg' => $msg_success,
                        'cufe' => $result['response']['cufe'] ?? '',
                        'qr' => $result['response']['QRStr'] ?? '',
                        'xml' => $result['response']['urlinvoicexml'] ?? '',
                        'pdf' => $result['response']['urlinvoicepdf'] ?? ''
                    );
                }else{
                    $message = 'Error al enviar factura a DIAN.';
                    if(isset($result['response']['message'])){
                        $message = $result['response']['message'];
                    }
                    if(isset($result['response']['error'])){
                        $message .= ': ' . $result['response']['error'];
                    }
                    
                    // Guardar intento fallido
                    $eiModel->save_electronic_invoice(
                        $idbill,
                        'invoice',
                        1,
                        $resolution['prefix'],
                        $resolution['current_consecutive'],
                        '',
                        2, // Rechazado
                        '',
                        '',
                        '',
                        json_encode($result['response'])
                    );
                    
                    $response = array('status' => 'error', 'msg' => $message);
                }
                
            } catch(Exception $e) {
                $response = array('status' => 'error', 'msg' => 'Error: ' . $e->getMessage());
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    
    /**
     * Obtener estado de factura electrónica
     */
    public function get_status($idbill){
        if($_SESSION['permits_module']['v']){
            $idbill = intval(decrypt($idbill));
            
            require_once("Models/ElectronicInvoiceModel.php");
            $eiModel = new ElectronicInvoiceModel();
            $data = $eiModel->find_by_bill($idbill);
            
            if(!empty($data)){
                $response = array('status' => 'success', 'data' => $data);
            }else{
                $response = array('status' => 'error', 'msg' => 'No se encontró información de facturación electrónica.');
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    
    /**
     * Ver representación gráfica de factura electrónica
     */
    public function view_electronic($idbill){
        if(empty($_SESSION['login'])){
            header('Location: '.base_url().'/login');
            die();
        }
        
        $idbill = intval(decrypt($idbill));
        
        if($idbill <= 0){
            header('Location: '.base_url().'/bills');
            die();
        }
        
        // Obtener datos de la factura
        require_once("Models/BillsModel.php");
        $billsModel = new BillsModel();
        $bill_data = $billsModel->view_bill($idbill);
        
        if(empty($bill_data)){
            header('Location: '.base_url().'/bills');
            die();
        }
        
        // Obtener datos de factura electrónica
        require_once("Models/ElectronicInvoiceModel.php");
        $eiModel = new ElectronicInvoiceModel();
        $ei_data = $eiModel->find_by_bill($idbill);
        
        if(empty($ei_data)){
            header('Location: '.base_url().'/bills');
            die();
        }
        
        // Obtener resolución vigente
        require_once("Models/ElectronicResolutionModel.php");
        $resModel = new ElectronicResolutionModel();
        $resolution = $resModel->select_record_by_type(1); // Factura electrónica
        
        // Construir URLs de APIDIAN para PDF y XML
        $pdf_url = '';
        $xml_url = '';
        if(!empty($ei_data['pdf_filename'])){
            $pdf_url = apidian_get_view_url($ei_data['pdf_filename']);
        }
        if(!empty($ei_data['xml_filename'])){
            $xml_url = apidian_get_view_url($ei_data['xml_filename']);
        }
        
        $data['page_name'] = "Factura Electrónica";
        $data['page_title'] = "Representación Gráfica - Factura Electrónica";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Facturas";
        $data['actual_page'] = "Factura Electrónica";
        $data['bill'] = $bill_data;
        $data['electronic'] = $ei_data;
        $data['business'] = $_SESSION['businessData'];
        $data['pdf_url'] = $pdf_url;
        $data['xml_url'] = $xml_url;
        $data['resolution'] = $resolution;
        
        $this->views->getView($this,"electronic_view",$data);
    }
    
    /**
     * Ver representación gráfica de nota crédito electrónica
     */
    public function view_credit_note($idbill){
        if(empty($_SESSION['login'])){
            header('Location: '.base_url().'/login');
            die();
        }
        
        $idbill = intval(decrypt($idbill));
        
        if($idbill <= 0){
            header('Location: '.base_url().'/bills');
            die();
        }
        
        // Obtener datos de la factura
        require_once("Models/BillsModel.php");
        $billsModel = new BillsModel();
        $bill_data = $billsModel->view_bill($idbill);
        
        if(empty($bill_data)){
            header('Location: '.base_url().'/bills');
            die();
        }
        
        // Obtener datos de nota crédito electrónica
        require_once("Models/ElectronicInvoiceModel.php");
        $eiModel = new ElectronicInvoiceModel();
        $ei_data = $eiModel->find_credit_note_by_bill($idbill);
        
        if(empty($ei_data)){
            header('Location: '.base_url().'/bills');
            die();
        }
        
        // Obtener resolución de notas crédito
        require_once("Models/ElectronicResolutionModel.php");
        $resModel = new ElectronicResolutionModel();
        $resolution = $resModel->select_record_by_type(4); // Tipo 4 = Nota Crédito
        
        // Construir URLs de APIDIAN para PDF y XML
        $pdf_url = '';
        $xml_url = '';
        if(!empty($ei_data['pdf_filename'])){
            $pdf_url = apidian_get_view_url($ei_data['pdf_filename']);
        }
        if(!empty($ei_data['xml_filename'])){
            $xml_url = apidian_get_view_url($ei_data['xml_filename']);
        }
        
        // Obtener datos de la factura original (billing_reference)
        $ei_original = $eiModel->find_by_bill($idbill);
        
        $data['page_name'] = "Nota Crédito Electrónica";
        $data['page_title'] = "Representación Gráfica - Nota Crédito";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Facturas";
        $data['actual_page'] = "Nota Crédito Electrónica";
        $data['bill'] = $bill_data;
        $data['electronic'] = $ei_data;
        $data['ei_original'] = $ei_original;
        $data['business'] = $_SESSION['businessData'];
        $data['pdf_url'] = $pdf_url;
        $data['xml_url'] = $xml_url;
        $data['resolution'] = $resolution;
        
        $this->views->getView($this,"credit_note_view",$data);
    }
    
    /**
     * Descargar PDF desde APIDIAN
     */
    public function download_pdf(){
        if(empty($_SESSION['login'])){
            echo json_encode(['status' => 'error', 'msg' => 'No autorizado']);
            die();
        }
        
        $idbill = intval(decrypt($_POST['idbill'] ?? ''));
        
        if($idbill <= 0){
            echo json_encode(['status' => 'error', 'msg' => 'ID inválido']);
            die();
        }
        
        require_once("Models/ElectronicInvoiceModel.php");
        $eiModel = new ElectronicInvoiceModel();
        $ei_data = $eiModel->find_by_bill($idbill);
        
        if(empty($ei_data) || empty($ei_data['pdf_filename'])){
            echo json_encode(['status' => 'error', 'msg' => 'PDF no disponible']);
            die();
        }
        
        // Descargar PDF desde APIDIAN
        $result = apidian_download_file($ei_data['pdf_filename'], 'BASE64');
        
        if($result['code'] == 200 && isset($result['response']['success']) && $result['response']['success']){
            echo json_encode([
                'status' => 'success',
                'filebase64' => $result['response']['filebase64'] ?? ''
            ]);
        }else{
            echo json_encode(['status' => 'error', 'msg' => 'No se pudo descargar el PDF']);
        }
        die();
    }
    
    /**
     * Descargar XML desde APIDIAN
     */
    public function download_xml(){
        if(empty($_SESSION['login'])){
            echo json_encode(['status' => 'error', 'msg' => 'No autorizado']);
            die();
        }
        
        $idbill = intval(decrypt($_POST['idbill'] ?? ''));
        
        if($idbill <= 0){
            echo json_encode(['status' => 'error', 'msg' => 'ID inválido']);
            die();
        }
        
        require_once("Models/ElectronicInvoiceModel.php");
        $eiModel = new ElectronicInvoiceModel();
        $ei_data = $eiModel->find_by_bill($idbill);
        
        if(empty($ei_data) || empty($ei_data['xml_filename'])){
            echo json_encode(['status' => 'error', 'msg' => 'XML no disponible']);
            die();
        }
        
        // Descargar XML desde APIDIAN
        $result = apidian_download_file($ei_data['xml_filename'], 'BASE64');
        
        if($result['code'] == 200 && isset($result['response']['success']) && $result['response']['success']){
            echo json_encode([
                'status' => 'success',
                'filebase64' => $result['response']['filebase64'] ?? ''
            ]);
        }else{
            echo json_encode(['status' => 'error', 'msg' => 'No se pudo descargar el XML']);
        }
        die();
    }
    
    /**
     * Enviar factura electrónica por correo
     */
    public function send_email(){
        if($_POST){
            try {
                $idbill = intval(decrypt($_POST['idbill']));
                $email = strtolower(strClean($_POST['email']));
                
                if(empty($email)){
                    echo json_encode(['status' => 'error', 'msg' => 'Email no válido']);
                    die();
                }
                
                // Obtener datos de factura electrónica
                require_once("Models/ElectronicInvoiceModel.php");
                $eiModel = new ElectronicInvoiceModel();
                $ei_data = $eiModel->find_by_bill($idbill);
                
                if(empty($ei_data)){
                    echo json_encode(['status' => 'error', 'msg' => 'Factura electrónica no encontrada']);
                    die();
                }
                
                // Preparar datos para envío
                $pdf_url = !empty($ei_data['pdf_filename']) ? apidian_get_pdf_url($ei_data['pdf_filename']) : '';
                $xml_url = !empty($ei_data['xml_filename']) ? apidian_get_pdf_url($ei_data['xml_filename']) : '';
                
                $business = $_SESSION['businessData'];
                
                // Enviar correo usando la función sendMail existente
                $information = [
                    'name_sender' => $business['business_name'],
                    'sender' => $business['email'],
                    'password' => $business['password'],
                    'host' => $business['server_host'],
                    'port' => $business['port'],
                    'affair' => 'Factura Electrónica - ' . $ei_data['prefix'] . str_pad($ei_data['number'], 7, "0", STR_PAD_LEFT),
                    'addressee' => $email,
                    'name_addressee' => 'Cliente',
                    'add_pdf' => false
                ];
                
                // Guardar datos en sesión para la plantilla de correo
                $_SESSION['invoice_email_data'] = [
                    'business' => $business,
                    'ei' => $ei_data,
                    'pdf_url' => $pdf_url,
                    'xml_url' => $xml_url
                ];
                
                $result = sendMail($information, 'electronic_invoice');
                
                if($result === true){
                    echo json_encode(['status' => 'success', 'msg' => 'Correo enviado exitosamente']);
                }else{
                    echo json_encode(['status' => 'error', 'msg' => 'Error al enviar correo']);
                }
                
            } catch(Exception $e) {
                echo json_encode(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()]);
            }
        }
        die();
    }
    
    /**
     * Enviar nota crédito electrónica
     */
    public function send_credit_note(){
        if($_POST){
            try {
                $idbill = intval(decrypt($_POST['idbill']));
                $discrepancy_code = intval($_POST['discrepancy_code']);
                $discrepancy_desc = strClean($_POST['discrepancy_desc']);
                $notes = strClean($_POST['notes'] ?? '');
                
                // Verificar configuración
                $token = $_SESSION['businessData']['apidian_token'] ?? '';
                if(empty($token)){
                    echo json_encode(['status' => 'error', 'msg' => 'Primero configure la empresa en APIDIAN']);
                    die();
                }
                
                // Obtener datos de la factura original
                require_once("Models/BillsModel.php");
                $billsModel = new BillsModel();
                $bill_data = $billsModel->view_bill($idbill);
                
                if(empty($bill_data)){
                    echo json_encode(['status' => 'error', 'msg' => 'Factura no encontrada']);
                    die();
                }
                
                $bill = $bill_data['bill'];
                $details = $bill_data['detail'];
                $client = $billsModel->select_client($bill['clientid']);
                
                // Obtener factura electrónica original
                require_once("Models/ElectronicInvoiceModel.php");
                $eiModel = new ElectronicInvoiceModel();
                $ei_original = $eiModel->find_by_bill($idbill);
                
                if(empty($ei_original) || empty($ei_original['cufe'])){
                    echo json_encode(['status' => 'error', 'msg' => 'La factura original no tiene CUFE. Debe estar autorizada primero.']);
                    die();
                }
                
                // Obtener resolución de notas crédito
                require_once("Models/ElectronicResolutionModel.php");
                $resModel = new ElectronicResolutionModel();
                $resolution = $resModel->get_active_by_type(4); // Tipo 4 = Nota Crédito
                
                if(empty($resolution)){
                    echo json_encode(['status' => 'error', 'msg' => 'No hay resolución de notas crédito activa.']);
                    die();
                }
                
                // Construir datos
                $credit_note_data = apidian_build_credit_note_data(
                    $bill, $client, $details, $resolution, $ei_original,
                    $discrepancy_code, $discrepancy_desc, $notes
                );
                
                // Enviar a APIDIAN
                $result = apidian_send_credit_note($token, $credit_note_data);
                
                $api_success = isset($result['response']['success']) && $result['response']['success'];
                $api_msg = $result['response']['message'] ?? ($result['response']['msg'] ?? '');
                if (!$api_success && !empty($api_msg) && preg_match('/(generad[ao]|exitos[ao]|autorizad[ao]|cread[ao]|éxito|exito)/i', $api_msg)) {
                    $api_success = true;
                }
                
                if($result['code'] == 200 && $api_success){
                    // Guardar en BD
                    $eiModel->save_electronic_invoice(
                        $idbill,
                        'credit-note',
                        4,
                        $resolution['prefix'],
                        $resolution['current_consecutive'],
                        $result['response']['cude'] ?? '',
                        1,
                        $result['response']['urlinvoicexml'] ?? '',
                        $result['response']['urlinvoicepdf'] ?? '',
                        $result['response']['QRStr'] ?? '',
                        json_encode($result['response'])
                    );
                    
                    // Incrementar consecutivo
                    $resModel->increment_consecutive($resolution['id']);
                    
                    $nc_number = $resolution['prefix'] . str_pad($resolution['current_consecutive'], 7, "0", STR_PAD_LEFT);
                    $msg_success = !empty($api_msg) ? $api_msg : 'Nota Crédito #'.$nc_number.' autorizada exitosamente por la DIAN.';
                    
                    echo json_encode([
                        'status' => 'success',
                        'msg' => $msg_success,
                        'cude' => $result['response']['cude'] ?? '',
                        'number' => $nc_number
                    ]);
                }else{
                    $message = 'Error al enviar nota crédito a DIAN.';
                    if(isset($result['response']['message'])){
                        $message = $result['response']['message'];
                    }
                    if(isset($result['response']['error'])){
                        $message .= ': ' . $result['response']['error'];
                    }
                    echo json_encode(['status' => 'error', 'msg' => $message]);
                }
                
            } catch(Exception $e) {
                echo json_encode(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()]);
            }
        }
        die();
    }
    
    /**
     * Enviar nota débito electrónica
     */
    public function send_debit_note(){
        if($_POST){
            try {
                $idbill = intval(decrypt($_POST['idbill']));
                $discrepancy_code = intval($_POST['discrepancy_code']);
                $discrepancy_desc = strClean($_POST['discrepancy_desc']);
                $notes = strClean($_POST['notes'] ?? '');
                
                // Verificar configuración
                $token = $_SESSION['businessData']['apidian_token'] ?? '';
                if(empty($token)){
                    echo json_encode(['status' => 'error', 'msg' => 'Primero configure la empresa en APIDIAN']);
                    die();
                }
                
                // Obtener datos de la factura original
                require_once("Models/BillsModel.php");
                $billsModel = new BillsModel();
                $bill_data = $billsModel->view_bill($idbill);
                
                if(empty($bill_data)){
                    echo json_encode(['status' => 'error', 'msg' => 'Factura no encontrada']);
                    die();
                }
                
                $bill = $bill_data['bill'];
                $details = $bill_data['detail'];
                $client = $billsModel->select_client($bill['clientid']);
                
                // Obtener factura electrónica original
                require_once("Models/ElectronicInvoiceModel.php");
                $eiModel = new ElectronicInvoiceModel();
                $ei_original = $eiModel->find_by_bill($idbill);
                
                if(empty($ei_original) || empty($ei_original['cufe'])){
                    echo json_encode(['status' => 'error', 'msg' => 'La factura original no tiene CUFE. Debe estar autorizada primero.']);
                    die();
                }
                
                // Obtener resolución de notas débito
                require_once("Models/ElectronicResolutionModel.php");
                $resModel = new ElectronicResolutionModel();
                $resolution = $resModel->get_active_by_type(5); // Tipo 5 = Nota Débito
                
                if(empty($resolution)){
                    echo json_encode(['status' => 'error', 'msg' => 'No hay resolución de notas débito activa.']);
                    die();
                }
                
                // Construir datos
                $debit_note_data = apidian_build_debit_note_data(
                    $bill, $client, $details, $resolution, $ei_original,
                    $discrepancy_code, $discrepancy_desc, $notes
                );
                
                // Enviar a APIDIAN
                $result = apidian_send_debit_note($token, $debit_note_data);
                
                $api_success = isset($result['response']['success']) && $result['response']['success'];
                $api_msg = $result['response']['message'] ?? ($result['response']['msg'] ?? '');
                if (!$api_success && !empty($api_msg) && preg_match('/(generad[ao]|exitos[ao]|autorizad[ao]|cread[ao]|éxito|exito)/i', $api_msg)) {
                    $api_success = true;
                }
                
                if($result['code'] == 200 && $api_success){
                    // Guardar en BD
                    $eiModel->save_electronic_invoice(
                        $idbill,
                        'debit-note',
                        5,
                        $resolution['prefix'],
                        $resolution['current_consecutive'],
                        $result['response']['cude'] ?? '',
                        1,
                        $result['response']['urlinvoicexml'] ?? '',
                        $result['response']['urlinvoicepdf'] ?? '',
                        $result['response']['QRStr'] ?? '',
                        json_encode($result['response'])
                    );
                    
                    // Incrementar consecutivo
                    $resModel->increment_consecutive($resolution['id']);
                    
                    $nd_number = $resolution['prefix'] . str_pad($resolution['current_consecutive'], 7, "0", STR_PAD_LEFT);
                    $msg_success = !empty($api_msg) ? $api_msg : 'Nota Débito #'.$nd_number.' autorizada exitosamente por la DIAN.';
                    
                    echo json_encode([
                        'status' => 'success',
                        'msg' => $msg_success,
                        'cude' => $result['response']['cude'] ?? '',
                        'number' => $nd_number
                    ]);
                }else{
                    $message = 'Error al enviar nota débito a DIAN.';
                    if(isset($result['response']['message'])){
                        $message = $result['response']['message'];
                    }
                    if(isset($result['response']['error'])){
                        $message .= ': ' . $result['response']['error'];
                    }
                    echo json_encode(['status' => 'error', 'msg' => $message]);
                }
                
            } catch(Exception $e) {
                echo json_encode(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()]);
            }
        }
        die();
    }
    
    /**
     * Enviar nota crédito electrónica por correo
     */
    public function send_credit_note_email(){
        if($_POST){
            try {
                $idbill = intval(decrypt($_POST['idbill']));
                $email = strtolower(strClean($_POST['email']));
                
                if(empty($email)){
                    echo json_encode(['status' => 'error', 'msg' => 'Email no válido']);
                    die();
                }
                
                // Obtener datos de nota crédito electrónica
                require_once("Models/ElectronicInvoiceModel.php");
                $eiModel = new ElectronicInvoiceModel();
                $ei_data = $eiModel->find_credit_note_by_bill($idbill);
                
                if(empty($ei_data)){
                    echo json_encode(['status' => 'error', 'msg' => 'Nota crédito electrónica no encontrada']);
                    die();
                }
                
                // Preparar datos para envío
                $pdf_url = !empty($ei_data['pdf_filename']) ? apidian_get_pdf_url($ei_data['pdf_filename']) : '';
                $xml_url = !empty($ei_data['xml_filename']) ? apidian_get_pdf_url($ei_data['xml_filename']) : '';
                
                $business = $_SESSION['businessData'];
                
                // Enviar correo usando la plantilla de nota crédito
                $information = [
                    'name_sender' => $business['business_name'],
                    'sender' => $business['email'],
                    'password' => $business['password'],
                    'host' => $business['server_host'],
                    'port' => $business['port'],
                    'affair' => 'Nota Crédito Electrónica - ' . $ei_data['prefix'] . str_pad($ei_data['number'], 7, "0", STR_PAD_LEFT),
                    'addressee' => $email,
                    'name_addressee' => 'Cliente',
                    'add_pdf' => false
                ];
                
                // Guardar datos en sesión para la plantilla de correo
                $_SESSION['invoice_email_data'] = [
                    'business' => $business,
                    'ei' => $ei_data,
                    'pdf_url' => $pdf_url,
                    'xml_url' => $xml_url
                ];
                
                $result = sendMail($information, 'credit_note_email');
                
                if($result === true){
                    echo json_encode(['status' => 'success', 'msg' => 'Correo enviado exitosamente']);
                }else{
                    echo json_encode(['status' => 'error', 'msg' => 'Error al enviar correo']);
                }
                
            } catch(Exception $e) {
                echo json_encode(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()]);
            }
        }
        die();
    }
    
    /**
     * Obtener motivos de nota crédito
     */
    public function get_credit_note_reasons(){
        require_once("Libraries/Core/Mysql.php");
        $con = new Mysql();
        $sql = "SELECT * FROM dian_credit_note_reasons ORDER BY id";
        $data = $con->select_all($sql);
        echo json_encode($data);
        die();
    }
    
    /**
     * Obtener motivos de nota débito
     */
    public function get_debit_note_reasons(){
        require_once("Libraries/Core/Mysql.php");
        $con = new Mysql();
        $sql = "SELECT * FROM dian_debit_note_reasons ORDER BY id";
        $data = $con->select_all($sql);
        echo json_encode($data);
        die();
    }
}
