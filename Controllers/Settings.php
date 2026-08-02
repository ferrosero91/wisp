<?php
    class Settings extends Controllers{
        public function __construct(){
            parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(BUSINESS);
        }
        public function settings(){
            $data['page_name'] = "Ajustes";
            $data['page_title'] = "Ajustes del Sistema";
            $data['home_page'] = "Dashboard";
            $data['actual_page'] = "Ajustes";
            $this->views->getView($this,"settings",$data);
        }
        public function general(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Ajustes generales";
            $data['page_title'] = "Ajustes Generales";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Ajustes";
            $data['actual_page'] = "General";
            $data['options'] = business_options();
            $data['dian_type_documents'] = $this->model->get_dian_type_documents();
            $data['dian_type_organizations'] = $this->model->get_dian_type_organizations();
            $data['dian_type_regimes'] = $this->model->get_dian_type_regimes();
            $data['dian_type_liabilities'] = $this->model->get_dian_type_liabilities();
            $data['dian_municipalities'] = $this->model->get_dian_municipalities();
            $data['page_functions_js'] = "general.js";
            $this->views->getView($this,"general",$data);
        }
        public function database(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Backup";
            $data['page_title'] = "Copias de Seguridad";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Ajustes";
            $data['actual_page'] = "Backup";
            $data['page_functions_js'] = "database.js";
            $this->views->getView($this,"database",$data);
        }
        public function zones(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Zonas";
            $data['page_title'] = "Gestión de Zonas";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Ajustes";
            $data['actual_page'] = "Zonas";
            $data['page_functions_js'] = "zones.js";
            $this->views->getView($this,"zones",$data);
        }
        public function client_portfolio(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Caetera de clientes";
            $data['page_title'] = "Gestión de Cartera";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Ajustes";
            $data['actual_page'] = "Cartera";
            $data['page_functions_js'] = "wallet.js";
            $this->views->getView($this,"wallet",$data);
        }
        public function update_apidian_connection(){
            if($_POST){
                if($_SESSION['permits_module']['a']){
                    $id = intval($_SESSION['businessData']['id']);
                    $apidian_url = strClean($_POST['apidian_url']);
                    $apidian_token = strClean($_POST['apidian_token']);
                    $apidian_environment = strClean($_POST['apidian_environment']);
                    
                    $request = $this->model->update_apidian_connection($id, $apidian_url, $apidian_token, $apidian_environment);
                    
                    if($request == 'success'){
                        $this->model->show_business();
                        $response = array('status' => 'success', 'msg' => 'Conexión APIDIAN actualizada.');
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No se pudo actualizar la conexión.');
                    }
                }else{
                    $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function update_taxes(){
            if($_POST){
                if($_SESSION['permits_module']['a']){
                    $id = intval($_SESSION['businessData']['id']);
                    $tax_rate = floatval($_POST['tax_rate']);
                    $tax_name = strClean($_POST['tax_name']);
                    
                    $request = $this->model->update_taxes($id, $tax_rate, $tax_name);
                    
                    if($request == 'success'){
                        $this->model->show_business();
                        $response = array('status' => 'success', 'msg' => 'Configuración de impuestos actualizada.');
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No se pudo actualizar.');
                    }
                }else{
                    $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function configure_apidian_company(){
            if($_POST){
                try {
                    if($_SESSION['permits_module']['a']){
                        $nit = strClean($_POST['config_nit']);
                        $dv = strClean($_POST['config_dv']);
                        $business_name = strClean($_POST['config_business_name']);
                        $address = strClean($_POST['config_address']);
                        $phone = strClean($_POST['config_phone']);
                        $email = strtolower(strClean($_POST['config_email']));
                        $municipality_id = intval($_POST['config_municipality']);
                        $type_document_identification_id = intval($_POST['config_type_doc']);
                        $type_organization_id = intval($_POST['config_type_org']);
                        $type_regime_id = intval($_POST['config_type_regime']);
                        $type_liability_id = intval($_POST['config_type_liability']);
                        $merchant_registration = strClean($_POST['config_merchant']);
                        $apidian_url = $_SESSION['businessData']['apidian_url'];
                        
                        if(empty($apidian_url)){
                            $response = array('status' => 'error', 'msg' => 'Primero configure la URL de APIDIAN en la sección Conexión API.');
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        
                        // Obtener datos SMTP de la configuración PHPMailer del negocio
                        $mail_host = $_SESSION['businessData']['server_host'] ?? '';
                        $mail_port = $_SESSION['businessData']['port'] ?? '587';
                        $mail_username = $_SESSION['businessData']['email'] ?? '';
                        $mail_password = $_SESSION['businessData']['password'] ?? '';
                        $mail_encryption = ($mail_port == '465') ? 'ssl' : 'tls';
                        
                        $config_data = [
                            'type_document_identification_id' => $type_document_identification_id,
                            'type_organization_id' => $type_organization_id,
                            'type_regime_id' => $type_regime_id,
                            'type_liability_id' => $type_liability_id,
                            'business_name' => $business_name,
                            'merchant_registration' => $merchant_registration,
                            'municipality_id' => $municipality_id,
                            'address' => $address,
                            'phone' => intval($phone),
                            'email' => $email,
                            'mail_host' => $mail_host,
                            'mail_port' => $mail_port,
                            'mail_username' => $mail_username,
                            'mail_password' => $mail_password,
                            'mail_encryption' => $mail_encryption
                        ];
                        
                        $result = apidian_config_company($nit, $dv, ['apidian_url' => $apidian_url, 'config_data' => $config_data]);
                        
                    if($result['code'] == 200 && isset($result['response']['success']) && $result['response']['success']){
                        $token = $result['response']['token'] ?? $result['response']['API_TOKEN'] ?? '';
                        $id = intval($_SESSION['businessData']['id']);
                        
                        $this->model->update_apidian_token($id, $token);
                        $this->model->update_apidian_nit($id, $nit, $dv);
                        
                        // Guardar configuración de empresa en BD
                        $this->model->save_apidian_company_config($id, [
                            'type_doc' => $type_document_identification_id,
                            'type_org' => $type_organization_id,
                            'type_regime' => $type_regime_id,
                            'type_liability' => $type_liability_id,
                            'merchant' => $merchant_registration,
                            'business_name' => $business_name,
                            'municipality_id' => $municipality_id,
                            'address' => $address,
                            'phone' => $phone,
                            'email' => $email
                        ]);
                        
                        $this->model->show_business();
                        
                        $response = array(
                            'status' => 'success', 
                            'msg' => 'Empresa registrada exitosamente en APIDIAN.',
                            'token' => $token
                        );
                    }else{
                            $message = 'Error al registrar empresa en APIDIAN.';
                            if(isset($result['response']['message'])){
                                $message = $result['response']['message'];
                            }
                            if(isset($result['response']['error'])){
                                $message .= ': ' . $result['response']['error'];
                            }
                            $response = array('status' => 'error', 'msg' => $message);
                        }
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                    }
                } catch(Exception $e) {
                    $response = array('status' => 'error', 'msg' => 'Error: ' . $e->getMessage());
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        /* CONFIGURAR CERTIFICADO DIGITAL */
        public function configure_certificate(){
            if($_POST){
                try {
                    if($_SESSION['permits_module']['a']){
                        $token = $_SESSION['businessData']['apidian_token'] ?? '';
                        
                        if(empty($token)){
                            $response = array('status' => 'error', 'msg' => 'Primero debe registrar la empresa en APIDIAN.');
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        
                        $certificate_password = strClean($_POST['certificate_password']);
                        
                        if(empty($certificate_password)){
                            $response = array('status' => 'error', 'msg' => 'Ingrese la contraseña del certificado.');
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        
                        // Procesar archivo de certificado
                        if(isset($_FILES['certificate_file']) && $_FILES['certificate_file']['error'] == 0){
                            $file = $_FILES['certificate_file'];
                            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                            
                            if(!in_array($extension, ['p12', 'pfx'])){
                                $response = array('status' => 'error', 'msg' => 'Solo se permiten archivos .p12 o .pfx');
                                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                                die();
                            }
                            
                            // Leer archivo y codificar en base64
                            $file_content = file_get_contents($file['tmp_name']);
                            $base64_certificate = base64_encode($file_content);
                            
                            // Guardar archivo en el servidor
                            $upload_dir = 'Assets/uploads/certificates/';
                            if(!is_dir($upload_dir)){
                                mkdir($upload_dir, 0755, true);
                            }
                            $filename = 'certificate_' . date('YmdHis') . '.' . $extension;
                            move_uploaded_file($file['tmp_name'], $upload_dir . $filename);
                            
                            // Enviar a APIDIAN
                            $result = apidian_config_certificate($token, $base64_certificate, $certificate_password);
                            
                            if($result['code'] == 200 && isset($result['response']['success']) && $result['response']['success']){
                                $id = intval($_SESSION['businessData']['id']);
                                $days_left = $result['response']['certificate_days_left'] ?? 0;
                                
                                // Guardar en BD
                                $this->model->save_certificate($id, $filename, $certificate_password, $days_left);
                                $this->model->show_business();
                                
                                $response = array(
                                    'status' => 'success',
                                    'msg' => 'Certificado configurado exitosamente.',
                                    'days_left' => $days_left
                                );
                            }else{
                                $message = 'Error al configurar certificado en APIDIAN.';
                                if(isset($result['response']['message'])){
                                    $message = $result['response']['message'];
                                }
                                if(isset($result['response']['error'])){
                                    $message .= ': ' . $result['response']['error'];
                                }
                                $response = array('status' => 'error', 'msg' => $message);
                            }
                        }else{
                            // Si no hay archivo nuevo, usar el existente
                            $existing_file = $_SESSION['businessData']['apidian_certificate_file'] ?? '';
                            if(!empty($existing_file) && file_exists('Assets/uploads/certificates/' . $existing_file)){
                                $file_content = file_get_contents('Assets/uploads/certificates/' . $existing_file);
                                $base64_certificate = base64_encode($file_content);
                                
                                $result = apidian_config_certificate($token, $base64_certificate, $certificate_password);
                                
                                if($result['code'] == 200 && isset($result['response']['success']) && $result['response']['success']){
                                    $id = intval($_SESSION['businessData']['id']);
                                    $days_left = $result['response']['certificate_days_left'] ?? 0;
                                    
                                    $this->model->save_certificate($id, $existing_file, $certificate_password, $days_left);
                                    $this->model->show_business();
                                    
                                    $response = array(
                                        'status' => 'success',
                                        'msg' => 'Certificado configurado exitosamente.',
                                        'days_left' => $days_left
                                    );
                                }else{
                                    $message = 'Error al configurar certificado.';
                                    if(isset($result['response']['message'])){
                                        $message = $result['response']['message'];
                                    }
                                    $response = array('status' => 'error', 'msg' => $message);
                                }
                            }else{
                                $response = array('status' => 'error', 'msg' => 'Adjunte un archivo de certificado (.p12 o .pfx).');
                            }
                        }
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                    }
                } catch(Exception $e) {
                    $response = array('status' => 'error', 'msg' => 'Error: ' . $e->getMessage());
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        /* CONFIGURAR SOFTWARE EN APIDIAN */
        public function configure_software(){
            if($_POST){
                try {
                    if($_SESSION['permits_module']['a']){
                        $token = $_SESSION['businessData']['apidian_token'] ?? '';
                        
                        if(empty($token)){
                            $response = array('status' => 'error', 'msg' => 'Primero debe registrar la empresa en APIDIAN.');
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        
                        $software_id = strClean($_POST['software_id']);
                        $software_pin = strClean($_POST['software_pin']);
                        
                        if(empty($software_id) || empty($software_pin)){
                            $response = array('status' => 'error', 'msg' => 'El ID y PIN del software son obligatorios.');
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        
                        if(strlen($software_pin) != 5){
                            $response = array('status' => 'error', 'msg' => 'El PIN debe tener exactamente 5 dígitos.');
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        
                        // Enviar a APIDIAN
                        $result = apidian_config_software($token, $software_id, $software_pin);
                        
                        if($result['code'] == 200 && isset($result['response']['success']) && $result['response']['success']){
                            $id = intval($_SESSION['businessData']['id']);
                            
                            // Guardar en BD
                            $this->model->save_software_config($id, $software_id, $software_pin);
                            $this->model->show_business();
                            
                            $response = array(
                                'status' => 'success',
                                'msg' => 'Software configurado exitosamente en APIDIAN.'
                            );
                        }else{
                            $message = 'Error al configurar software en APIDIAN.';
                            if(isset($result['response']['message'])){
                                $message = $result['response']['message'];
                            }
                            if(isset($result['response']['error'])){
                                $message .= ': ' . $result['response']['error'];
                            }
                            $response = array('status' => 'error', 'msg' => $message);
                        }
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                    }
                } catch(Exception $e) {
                    $response = array('status' => 'error', 'msg' => 'Error: ' . $e->getMessage());
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        /* ENVIAR RESOLUCION A APIDIAN */
        public function send_resolution_to_apidian(){
            if($_POST){
                try {
                    if($_SESSION['permits_module']['a']){
                        $token = $_SESSION['businessData']['apidian_token'] ?? '';
                        
                        if(empty($token)){
                            $response = array('status' => 'error', 'msg' => 'Primero debe registrar la empresa en APIDIAN.');
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            die();
                        }
                        
                        $type_document_id = intval($_POST['resolution_type_doc']);
                        $prefix = strtoupper(strClean($_POST['resolution_prefix']));
                        $resolution_number = strClean($_POST['resolution_number']);
                        $resolution_date = strClean($_POST['resolution_date']);
                        $date_from = strClean($_POST['resolution_date_from']);
                        $date_to = strClean($_POST['resolution_date_to']);
                        $consecutive_from = intval($_POST['resolution_from']);
                        $consecutive_to = intval($_POST['resolution_to']);
                        $technical_key = strClean($_POST['resolution_technical_key'] ?? '');
                        
                        // Construir datos según tipo de documento
                        $resolution_data = [
                            'type_document_id' => $type_document_id,
                            'prefix' => $prefix,
                            'from' => $consecutive_from,
                            'to' => $consecutive_to
                        ];
                        
                        // Para facturas electrónicas (type_document_id=1) requiere más campos
                        if($type_document_id == 1){
                            if(empty($resolution_number) || empty($resolution_date) || empty($date_from) || empty($date_to)){
                                $response = array('status' => 'error', 'msg' => 'Para Factura Electrónica todos los campos son obligatorios.');
                                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                                die();
                            }
                            $resolution_data['resolution'] = $resolution_number;
                            $resolution_data['resolution_date'] = $resolution_date;
                            $resolution_data['technical_key'] = $technical_key;
                            $resolution_data['generated_to_date'] = 0;
                            $resolution_data['date_from'] = $date_from;
                            $resolution_data['date_to'] = $date_to;
                        }else if($type_document_id == 4 || $type_document_id == 5){
                            // NC y ND - enviar datos completos si están disponibles
                            if(!empty($resolution_number)){
                                $resolution_data['resolution'] = $resolution_number;
                            }
                            if(!empty($resolution_date)){
                                $resolution_data['resolution_date'] = $resolution_date;
                            }
                            if(!empty($technical_key)){
                                $resolution_data['technical_key'] = $technical_key;
                            }
                            if(!empty($date_from)){
                                $resolution_data['date_from'] = $date_from;
                            }
                            if(!empty($date_to)){
                                $resolution_data['date_to'] = $date_to;
                            }
                        }
                        
                        // Enviar a APIDIAN
                        $result = apidian_config_resolution($token, $resolution_data);
                        
                        if($result['code'] == 200 && isset($result['response']['success']) && $result['response']['success']){
                            // Verificar si ya existe una resolución con el mismo prefijo y número
                            require_once("Models/ElectronicResolutionModel.php");
                            $model = new ElectronicResolutionModel();
                            
                            $existing = $model->find_by_prefix_and_number($type_document_id, $prefix, $resolution_number);
                            
                            if(!empty($existing)){
                                // Actualizar existente
                                $model->update_resolution(
                                    $existing['id'], $type_document_id, $prefix, $resolution_number,
                                    $resolution_date, $date_from, $date_to,
                                    $consecutive_from, $consecutive_to, $technical_key
                                );
                            }else{
                                // Crear nueva
                                $model->create_resolution(
                                    $type_document_id, $prefix, $resolution_number,
                                    $resolution_date, $date_from, $date_to,
                                    $consecutive_from, $consecutive_to, $technical_key
                                );
                            }
                            
                            $response = array(
                                'status' => 'success',
                                'msg' => 'Resolución configurada exitosamente en APIDIAN.',
                                'data' => $result['response']
                            );
                        }else{
                            $message = 'Error al configurar resolución en APIDIAN.';
                            if(isset($result['response']['message'])){
                                $message = $result['response']['message'];
                            }
                            if(isset($result['response']['error'])){
                                $message .= ': ' . $result['response']['error'];
                            }
                            $response = array('status' => 'error', 'msg' => $message);
                        }
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                    }
                } catch(Exception $e) {
                    $response = array('status' => 'error', 'msg' => 'Error: ' . $e->getMessage());
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        /* RESOLUCIONES ELECTRONICAS */
        public function list_resolutions(){
            if($_SESSION['permits_module']['v']){
                require_once("Models/ElectronicResolutionModel.php");
                $model = new ElectronicResolutionModel();
                $data = $model->list_resolutions();
                
                $type_docs = [1 => 'Factura', 4 => 'Nota Crédito', 5 => 'Nota Débito', 11 => 'Doc. Soporte'];
                
                for($i = 0; $i < count($data); $i++){
                    $data[$i]['type_name'] = $type_docs[$data[$i]['type_document_id']] ?? 'Otro';
                    
                    // Formatear fechas solo si son válidas
                    $data[$i]['date_from_format'] = (!empty($data[$i]['date_from']) && $data[$i]['date_from'] != '0000-00-00') ? date('d/m/Y', strtotime($data[$i]['date_from'])) : '-';
                    $data[$i]['date_to_format'] = (!empty($data[$i]['date_to']) && $data[$i]['date_to'] != '0000-00-00') ? date('d/m/Y', strtotime($data[$i]['date_to'])) : '-';
                    
                    // Determinar estado según tipo de documento
                    if($data[$i]['type_document_id'] == 4 || $data[$i]['type_document_id'] == 5){
                        // NC y ND no requieren fechas de resolución DIAN
                        if($data[$i]['state'] == 1){
                            $data[$i]['state_name'] = '<span class="badge badge-success">Activa</span>';
                        }else{
                            $data[$i]['state_name'] = '<span class="badge badge-danger">Inactiva</span>';
                        }
                    }else{
                        // Facturas y DS verifican vigencia
                        if($data[$i]['state'] == 1 && !empty($data[$i]['date_to']) && $data[$i]['date_to'] >= date('Y-m-d')){
                            $data[$i]['state_name'] = '<span class="badge badge-success">Activa</span>';
                        }else if($data[$i]['state'] == 1 && !empty($data[$i]['date_to']) && $data[$i]['date_to'] < date('Y-m-d')){
                            $data[$i]['state_name'] = '<span class="badge badge-warning">Vencida</span>';
                        }else{
                            $data[$i]['state_name'] = '<span class="badge badge-danger">Inactiva</span>';
                        }
                    }
                    
                    $data[$i]['progress'] = '<strong>' . $data[$i]['current_consecutive'] . '</strong> / ' . $data[$i]['consecutive_to'];
                    
                    $actions = '<a href="javascript:;" onclick="editResolution('.$data[$i]['id'].')"><i class="fa fa-pencil-alt text-blue mr-1"></i></a>';
                    if($data[$i]['state'] == 1){
                        $actions .= '<a href="javascript:;" onclick="toggleResolution('.$data[$i]['id'].',0)"><i class="fa fa-ban text-red mr-1"></i></a>';
                    }else{
                        $actions .= '<a href="javascript:;" onclick="toggleResolution('.$data[$i]['id'].',1)"><i class="fa fa-check text-green mr-1"></i></a>';
                    }
                    $actions .= '<a href="javascript:;" onclick="deleteResolution('.$data[$i]['id'].')"><i class="fa fa-trash text-red ml-1"></i></a>';
                    $data[$i]['actions'] = $actions;
                }
                send_json($data);
            }
            die();
        }
        public function save_resolution(){
            if($_POST){
                if($_SESSION['permits_module']['a']){
                    $id = intval($_POST['resolution_id']);
                    $type_document_id = intval($_POST['resolution_type_doc']);
                    $prefix = strtoupper(strClean($_POST['resolution_prefix']));
                    $resolution_number = strClean($_POST['resolution_number']);
                    $resolution_date = strClean($_POST['resolution_date']);
                    $date_from = strClean($_POST['resolution_date_from']);
                    $date_to = strClean($_POST['resolution_date_to']);
                    $consecutive_from = intval($_POST['resolution_from']);
                    $consecutive_to = intval($_POST['resolution_to']);
                    $current_consecutive = intval($_POST['resolution_current'] ?? 0);
                    $technical_key = strClean($_POST['resolution_technical_key'] ?? '');
                    
                    // Si current_consecutive es 0, usar consecutive_from
                    if($current_consecutive == 0){
                        $current_consecutive = $consecutive_from;
                    }
                    
                    require_once("Models/ElectronicResolutionModel.php");
                    $model = new ElectronicResolutionModel();
                    
                    if($id == 0){
                        // Verificar si ya existe
                        $existing = $model->find_by_prefix_and_number($type_document_id, $prefix, $resolution_number);
                        if(!empty($existing)){
                            // Actualizar existente
                            $request = $model->update_resolution($existing['id'], $type_document_id, $prefix, $resolution_number, $resolution_date, $date_from, $date_to, $consecutive_from, $consecutive_to, $technical_key, $current_consecutive);
                        }else{
                            $request = $model->create_resolution($type_document_id, $prefix, $resolution_number, $resolution_date, $date_from, $date_to, $consecutive_from, $consecutive_to, $technical_key, $current_consecutive);
                        }
                    }else{
                        $request = $model->update_resolution($id, $type_document_id, $prefix, $resolution_number, $resolution_date, $date_from, $date_to, $consecutive_from, $consecutive_to, $technical_key, $current_consecutive);
                    }
                    
                    if($request == 'success'){
                        $response = array('status' => 'success', 'msg' => 'Resolución guardada exitosamente.');
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No se pudo guardar la resolución.');
                    }
                }else{
                    $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function get_resolution(string $id){
            if($_SESSION['permits_module']['v']){
                require_once("Models/ElectronicResolutionModel.php");
                $model = new ElectronicResolutionModel();
                $data = $model->select_record(intval($id));
                if(!empty($data)){
                    $response = array('status' => 'success', 'data' => $data);
                }else{
                    $response = array('status' => 'error', 'msg' => 'Resolución no encontrada.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function toggle_resolution(){
            if($_POST){
                if($_SESSION['permits_module']['a']){
                    $id = intval($_POST['id']);
                    $state = intval($_POST['state']);
                    
                    require_once("Models/ElectronicResolutionModel.php");
                    $model = new ElectronicResolutionModel();
                    $request = $model->update_state($id, $state);
                    
                    if($request == 'success'){
                        $response = array('status' => 'success', 'msg' => 'Estado actualizado.');
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No se pudo actualizar.');
                    }
                }else{
                    $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function delete_resolution(){
            if($_POST){
                if($_SESSION['permits_module']['e']){
                    $id = intval($_POST['id']);
                    
                    require_once("Models/ElectronicResolutionModel.php");
                    $model = new ElectronicResolutionModel();
                    $request = $model->delete_resolution($id);
                    
                    if($request == 'success'){
                        $response = array('status' => 'success', 'msg' => 'Resolución eliminada.');
                    }else{
                        $response = array('status' => 'error', 'msg' => 'No se pudo eliminar.');
                    }
                }else{
                    $response = array('status' => 'error', 'msg' => 'No tiene permisos.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }
