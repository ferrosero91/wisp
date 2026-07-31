<?php
    require 'Libraries/dompdf/vendor/autoload.php';
    require 'Libraries/spreadsheet/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use Dompdf\Dompdf;
    class Payments extends Controllers{
      public function __construct(){
        parent::__construct();
			  session_start();
			  if(empty($_SESSION['login'])){
				  header('Location: '.base_url().'/login');
				  die();
			  }
			  consent_permission(PAYMENTS);
      }
      public function payments(){
        if(empty($_SESSION['permits_module']['v'])){
          header("Location:".base_url().'/dashboard');
        }
        $data['page_name'] = "Cobranzas realizadas";
        $data['page_title'] = "Cobranzas Realizadas";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Finanzas";
        $data['actual_page'] = "Cobros";
        $data['page_functions_js'] = "payments.js";
        $this->views->getView($this,"payments",$data);
      }
      public function add_payment(){
        if(empty($_SESSION['permits_module']['v'])){
          header("Location:".base_url().'/dashboard');
        }
        $data['page_name'] = "Registrar pagos";
        $data['page_functions_js'] = "add_payment.js";
        $this->views->getView($this,"add",$data);
      }
      public function statistics(){
          if(empty($_SESSION['permits_module']['v'])){
              header("Location:".base_url().'/dashboard');
          }
          $data['page_name'] = "Resumen de transacciones";
          $data['page_functions_js'] = "statistics.js";
          $this->views->getView($this,"statistics",$data);
      }
      public function export_pdf(){
        if($_SESSION['permits_module']['v']){
          if(empty($_GET['start']) && empty($_GET['end'])){
            $start = date("Y-m-01");
            $end = date("Y-m-t");
          }else{
            $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
            $start = $dateStart->format('Y-m-d');
            $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
            $end = $dateEnd->format('Y-m-d');
          }
          $type = isset($_GET['type']) ? intVal($_GET['type']) : 0;
          $user = isset($_GET['user']) ? intVal($_GET['user']) : 0;
          $state = isset($_GET['state']) ? intVal($_GET['state']) : 0;
          $data = array();
          $query = $this->model->list_records($start,$end,$type,$user,$state);
          if(empty($query)){
            header('Location: '.base_url().'/login');
          }else{
            $data = array('start' => $start,'end' => $end,'data' => $query);
            ob_end_clean();
            $html = redirect_pdf("Resources/reports/pdf/payment_report",$data);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set(array('isRemoteEnabled' => true));
            $dompdf->setOptions($options);
            $dompdf->loadHtml($html);
            $orientation = 'landscape';
            $customPaper = 'A4';
            $dompdf->setPaper($customPaper,$orientation);
            $dompdf->render();
            $dompdf->stream('Lista de cobros.pdf',array("Attachment" => false));
          }
          die();
        }else{
          header('Location: '.base_url().'/login');
        }
      }
      public function export_excel(){
        if($_SESSION['permits_module']['v']){
          if(empty($_GET['start']) && empty($_GET['end'])){
            $start = date("Y-m-01");
            $end = date("Y-m-t");
          }else{
            $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
            $start = $dateStart->format('Y-m-d');
            $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
            $end = $dateEnd->format('Y-m-d');
          }
          $type = isset($_GET['type']) ? intVal($_GET['type']) : 0;
          $user = isset($_GET['user']) ? intVal($_GET['user']) : 0;
          $state = isset($_GET['state']) ? intVal($_GET['state']) : 0;

          $spreadsheet = new SpreadSheet();
          $style_header = array(
              'font' => array(
                  'name'  => 'Calibri',
                  'bold'  => true,
                  'color' => array(
                      'rgb' => 'ffffff'
                  ),
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
          $spreadsheet->getActiveSheet()->getStyle('A1:J1')->applyFromArray($style_header);
          $center_cell = array(
              'alignment' => array(
                  'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                  'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
              ),
          );
          $spreadsheet->getActiveSheet()->getStyle('A')->applyFromArray($center_cell);
          $spreadsheet->getActiveSheet()->getStyle('C:L')->applyFromArray($center_cell);
          $spreadsheet->getActiveSheet()->getStyle('J')->applyFromArray($center_cell);
          $active_sheet = $spreadsheet->getActiveSheet();
          $active_sheet->setTitle("Cobranzas");
          $active_sheet->getColumnDimension('A')->setAutoSize(true);
          $active_sheet->setCellValue('A1', 'COD');
          $active_sheet->getColumnDimension('B')->setAutoSize(true);
          $active_sheet->setCellValue('B1', 'CLIENTE');
          $active_sheet->getColumnDimension('C')->setAutoSize(true);
          $active_sheet->setCellValue('C1', 'Nº FACTURA');
          $active_sheet->getColumnDimension('D')->setAutoSize(true);
          $active_sheet->setCellValue('D1', 'FECHA');
          $active_sheet->getColumnDimension('E')->setAutoSize(true);
          $active_sheet->setCellValue('E1', 'TOTAL FACTURA');
          $active_sheet->getColumnDimension('F')->setAutoSize(true);
          $active_sheet->setCellValue('F1', 'PAGADO');
          $active_sheet->getColumnDimension('G')->setAutoSize(true);
          $active_sheet->setCellValue('G1', 'FORMA PAGO');
          $active_sheet->getColumnDimension('H')->setAutoSize(true);
          $active_sheet->setCellValue('H1', 'USUARIO');
          $active_sheet->getColumnDimension('I')->setAutoSize(true);
          $active_sheet->setCellValue('I1', 'COMENTARIO');
          $active_sheet->getColumnDimension('J')->setAutoSize(true);
          $active_sheet->setCellValue('J1', 'ESTADO');
          $data = $this->model->list_records($start,$end,$type,$user,$state);
          if(!empty($data)){
              $i = 2;
              foreach ($data AS $key => $value){
                  if($value['state'] == 1){
                      $state = 'RECIBIDO';
                  }else if($value['state'] == 2){
                      $state = 'ENTREGAR';
                  }else if($value['state'] == 3){
                      $state = 'ANULADO';
                  }
                  $active_sheet->setCellValue('A'.$i, $value['internal_code']);
                  $active_sheet->setCellValue('B'.$i, $value['client']);
                  $active_sheet->setCellValue('C'.$i, str_pad($value['correlative'],7,"0", STR_PAD_LEFT));
                  $active_sheet->setCellValue('D'.$i, date("d/m/Y H:i", strtotime($value['payment_date'])));
                  $active_sheet->setCellValue('E'.$i, $_SESSION['businessData']['symbol'].format_money($value['bill_total']));
                  $active_sheet->setCellValue('F'.$i, $_SESSION['businessData']['symbol'].format_money($value['amount_paid']));
                  $active_sheet->setCellValue('G'.$i, $value['payment_type']);
                  $active_sheet->setCellValue('H'.$i, $value['user']);
                  $active_sheet->setCellValue('I'.$i, $value['comment']);
                  $active_sheet->setCellValue('J'.$i, $state);
                  $i++;
              }
          }
          $title = 'Lista de cobros';
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename="'. $title .'.xlsx"');
          header('Cache-Control: max-age=0');
          $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
          $writer->save('php://output');
        }
      }
      public function select_record(string $idpayment){
        if($_SESSION['permits_module']['v']){
          $idpayment = decrypt($idpayment);
          $idpayment = intval($idpayment);
          if($idpayment > 0){
            $data = $this->model->select_record($idpayment);
            if(empty($data)){
              $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
            }else{
              $data['encrypt_id'] = encrypt($data['id']);
              $data['encrypt_bill'] = encrypt($data['billid']);
              $data['encrypt_client'] = encrypt($data['clientid']);
              $data['invoice'] = str_pad($data['correlative'],7,"0", STR_PAD_LEFT);
              $answer = array('status' => 'success', 'data' => $data);
            }
          }else{
            $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
          }
          echo json_encode($answer,JSON_UNESCAPED_UNICODE);
        }
        die();
      }
      public function list_records(){
          if($_SESSION['permits_module']['v']){
            if(empty($_GET['start']) && empty($_GET['end'])){
              $start = date("Y-m-01");
              $end = date("Y-m-t");
            }else{
              $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
              $start = $dateStart->format('Y-m-d');
              $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
              $end = $dateEnd->format('Y-m-d');
            }
              $type = isset($_GET['type']) ? intVal($_GET['type']) : 0;
              $user = isset($_GET['user']) ? intVal($_GET['user']) : 0;
              $state = isset($_GET['state']) ? intVal($_GET['state']) : 0;
              $data = $this->model->list_records($start,$end,$type,$user,$state);
              $n = 1;
              for($i=0; $i < count($data); $i++){
                  $data[$i]['n'] = $n++;
                  $data[$i]['count_total'] = $data[$i]['amount_paid'];
                  /* ID ENCRUPTADOS */
                  $data[$i]['encrypt_client'] = encrypt($data[$i]['clientid']);
                  $data[$i]['encrypt_bill'] = encrypt($data[$i]['billid']);
                  /* COMPROBANTE */
                  $correlative = str_pad($data[$i]['correlative'],7,"0", STR_PAD_LEFT);
                  $data[$i]['invoice'] = $correlative;
                  /* CLIENTE TIENE CONTRATO */
                  $contract = $this->model->contract_client($data[$i]['clientid']);
                  if(empty($contract)){
                    $data[$i]['encrypt_contract'] = "";
                  }else{
                    $data[$i]['encrypt_contract'] = encrypt($contract['id']);
                  }
                  $data[$i]['amount_paid'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['amount_paid']);
                  $data[$i]['bill_total'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['bill_total']);
                  if($_SESSION['permits_module']['a']){
                      if($data[$i]['state'] == 1){
                        $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt"></i></a>';
                        $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update_payment(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
                      }else{
                        $update = '';
                        $update_2 = '';
                      }
                  }else{
                    $update = '';
                    $update_2 = '';
                  }
                  if($_SESSION['permits_module']['e']){
                      if($data[$i]['state'] == 1){
                        $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Anular" onclick="cancel(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-ban"></i></a>';
                        $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-ban mr-1"></i>Anular</a>';
                      }else{
                        $cancel = '';
                        $cancel_2 = '';
                      }
                  }else{
                    $cancel = '';
                    $cancel_2 = '';
                  }
                  if($data[$i]['state'] == 1){
                      $data[$i]['count_state'] = 'RECIBIDA';
                  }else if($data[$i]['state'] == 2){
                      $data[$i]['count_state'] = 'ANULADO';
                  }
                  $options = '<div class="hidden-sm hidden-xs action-buttons">'.$update.$cancel.'</div>';
                  $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                  <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                    '.$update_2.$cancel_2.'
                  </div>
                  </div></div>';
                  $data[$i]['options'] = $options;
              }
              echo json_encode($data,JSON_UNESCAPED_UNICODE);
          }
          die();
      }
      public function modify_payment(){
          if($_POST){
              if(empty($_POST['idpayment']) || empty($_POST['idbill']) || empty($_POST['total_payment'])){
                  $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios');
              }else{
                  $id = decrypt($_POST['idpayment']);
                  $id = intval($id);
                  $bill = intval(strClean($_POST['idbill']));
                  $user = intval(strClean($_SESSION['idUser']));
                  $date = DateTime::createFromFormat('d/m/Y H:i',$_POST['date_time']);
                  $datetime = $date->format('Y-m-d H:i:s');
                  $typepay = intval(strClean($_POST['listTypePay']));
                  $comment = strtoupper(strClean($_POST['comment']));
                  $subscriber = strClean($_POST['total_payment']);
                  if($_SESSION['permits_module']['a']){
                      $request = $this->model->modify($id,$typepay,$datetime,$comment);
                  }
                  if($request == "success"){
                      $response = array('status' => 'success', 'msg' => 'Los cambios fueron guardados correctamente.');
                  }else{
                    $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
                  }
              }
              echo json_encode($response,JSON_UNESCAPED_UNICODE);
          }
          die();
      }
      public function cancel(){
          if($_POST){
              if($_SESSION['permits_module']['e']){
                  $idpayment = decrypt($_POST['idpayment']);
                  $idpayment =  intval($idpayment);
                  $payment = $this->model->select_record($idpayment);
                  if(!empty($payment)){
                      $idbill = $payment['billid'];
                      $amount_paid = $payment['amount_paid'];
                      $bill = $this->model->select_bill($idbill);
                      $remaining_amount = $bill['remaining_amount'];
                      if($remaining_amount == 0){
                          $this->model->subtract_amounts($idbill,$amount_paid,2);
                      }else if($remaining_amount > 0){
                          $this->model->subtract_amounts($idbill,$amount_paid,0);
                      }
                      $request = $this->model->cancel($idpayment);
                      if($request == 'success'){
                        $response = array('status' => 'success', 'msg' => 'Pago anulado correctamente.');
                      }else{
                        $response = array('status' => 'error', 'msg' => 'Error no se pudo anular.');
                      }
                  }else{
                      $response = array('status' => 'error', 'msg' => 'La transacción no exite.');
                  }
                  echo json_encode($response,JSON_UNESCAPED_UNICODE);
              }
          }
          die();
      }
      public function payment_summary(int $year){
          $year = intval($year);
          if($year > 0){
              $data = $this->model->payment_summary($year);
              echo json_encode($data,JSON_UNESCAPED_UNICODE);
          }
          die();
      }
      public function view_bill(string $idbill){
          if($_SESSION['permits_module']['v']){
              $idbill = decrypt($idbill);
              $idbill = intval($idbill);
              if($idbill > 0){
                  $data = $this->model->view_bill($idbill);
                  if(empty($data)){
                    $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                  }else{
                      $data['bill']['voucher'] = ucwords(strtolower($data['bill']['voucher']));
                      $data['bill']['serie'] = str_pad($data['bill']['correlative'],7,"0", STR_PAD_LEFT);
                      $answer = array('status' => 'success', 'data' => $data);
                  }
              }else{
                $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
              }
              echo json_encode($answer,JSON_UNESCAPED_UNICODE);
          }
          die();
      }
      public function search_clients(){
        if($_POST){
          $html = "";
          $search = strClean($_POST['search']);
          $arrData = $this->model->search_clients($search);
          if(empty($arrData)){
            $html .= '<li>No se encontro "'.$search.'"</li>';
          }else{
            foreach ($arrData as $row){
              $html .= '<li onclick="pending_invoices(\''.encrypt($row['id']).'\')">'.$row['names'].' '.$row['surnames'].'<small class="ml-1 f-s-10 text-secundary">(DOC '.$row['document'].')</small></li>';
            }
          }
          echo $html;
        }
        die();
      }
      public function pending_invoices(string $idclient){
        $idclient = decrypt($idclient);
        $idclient = intval($idclient);
        if($idclient > 0){
          $data = $this->model->pending_invoices($idclient);
          if(empty($data)){
            $answer = array('status' => 'error', 'msg' => 'El cliente no tiene ninguna factura pendiente de pago.');
          }else{
            ob_end_clean();
            $data['views'] = views("bulk_payments",$data);
            $answer = array('status' => 'success', 'data' => $data);
          }
        }else{
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        }
        echo json_encode($answer,JSON_UNESCAPED_UNICODE);
        die();
      }
      public function list_pendings(string $idclient){
        $idclient = intval($idclient);
        $data = $this->model->list_pendings($idclient,"DESC");
        for($i=0; $i < count($data); $i++){
          if($data[$i]['type'] == 1){
            $month_letter = "";
          }else if($data[$i]['type'] == 2){
            $months = months();
            $month =  date('n',strtotime($data[$i]['billed_month']));
            $year =  date('Y',strtotime($data[$i]['billed_month']));
            $month_letter = strtoupper($months[intval($month)-1]).",".$year;
          }
          $correlative = str_pad($data[$i]['correlative'],7,"0", STR_PAD_LEFT);
          $data[$i]['invoice'] = $correlative;
          $data[$i]['billing'] = $month_letter;
          $data[$i]['balance'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['remaining_amount']);
          $data[$i]['total'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['total']);
        }
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        die();
      }
      public function cancel_massive(){
        if($_POST){
          if(isset($_POST['ids'])){
            $idpayments = explode(",",$_POST['ids']);
          }else{
            $idpayments = [];
          }
          if(count($idpayments) > 0){
            $total = 0;
            for($i = 0; $i < count($idpayments); $i++){
              $payment = $this->model->pending_payments($idpayments[$i]);
              $idbill = $payment['billid'];
              $amount_paid = $payment['amount_paid'];
              $bill = $this->model->select_bill($idbill);
              $remaining_amount = $bill['remaining_amount'];
              if($remaining_amount == 0){
                $this->model->subtract_amounts($idbill,$amount_paid,2);
              }else if($remaining_amount > 0){
                $this->model->subtract_amounts($idbill,$amount_paid,0);
              }
              $request = $this->model->cancel_massive($idpayments[$i]);
              $total = $total + $request;
            }
            if($total > 0){
              $response = array('status' => 'success', 'msg' => 'Pagos cancelados exitosamente.');
            }else{
              $response = array('status' => 'info', 'msg' => 'No se pudo realizar esta operacion.');
            }
          }else{
            $response = array('status' => 'error', 'msg' => 'No se enviaron datos, imposible realizar esta operacion.');
          }
          echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }
        die();
      }
      public function mass_payments(){
        if($_POST){
          if(empty($_POST['idclient']) || empty($_POST['total_pay'])){
            $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios');
          }else{
            $user = intval(strClean($_SESSION['idUser']));
            $client = decrypt($_POST['idclient']);
            $client = intval($client);
            $typepay = intval($_POST['typepay']);
            if($_SESSION['userData']['profileid'] == 1){
              $date = DateTime::createFromFormat('d/m/Y H:i',$_POST['date_time']);
              $datetime = $date->format('Y-m-d H:i:s');
            }else{
              $datetime = date('Y-m-d H:i:s');
            }
            $radio_option = strClean($_POST['radio_option']);
            $comment = strtoupper(strClean($_POST['comment']));
            $total_pay = $_POST['total_pay'];
            $current_paid = $_POST['total_pay'];
            $count = 0;
            $idbills = array();
            if($_SESSION['permits_module']['r']){
              $pendings = $this->model->list_pendings($client,$radio_option);
              for($i=0; $i < count($pendings); $i++){
                $bill = $this->model->select_pending($pendings[$i]['id']);
                foreach($bill as $bill){
                  if($total_pay < $bill['remaining_amount']){
                    $total = $this->model->returnCodePayment();
                    if($total == 0){
                      $code = "T00001";
                    }else{
                      $max = $this->model->generateCodePayment();
                      $code = "T".substr((substr($max,1)+100001),1);
                    }
                    $subtract_amounts = $bill['remaining_amount'] - $total_pay;
                    if($total_pay >= 1){
                      $this->model->modify_amounts($bill['id'],$total_pay,$subtract_amounts,0);
                      $amount_paid = $this->model->amount_paid($bill['id']);
                      $request = $this->model->mass_payments($bill['id'],$user,$client,$code,$typepay,$datetime,$comment,$total_pay,$amount_paid,$subtract_amounts,1);
                      array_push($idbills,$bill['id']);
                    }
                    $total_pay = 0;
                  }
                  if($total_pay == $bill['remaining_amount']){
                    $total = $this->model->returnCodePayment();
                    if($total == 0){
                      $code = "T00001";
                    }else{
                      $max = $this->model->generateCodePayment();
                      $code = "T".substr((substr($max,1)+100001),1);
                    }
                    $this->model->modify_amounts($bill['id'],$bill['remaining_amount'],0,1);
                    $amount_paid = $this->model->amount_paid($bill['id']);
                    $request = $this->model->mass_payments($bill['id'],$user,$client,$code,$typepay,$datetime,$comment,$bill['remaining_amount'],$amount_paid,0,1);
                    array_push($idbills,$bill['id']);
                    $total_pay = 0;
                  }
                  if($total_pay > $bill['remaining_amount']){
                    $total = $this->model->returnCodePayment();
                    if($total == 0){
                      $code = "T00001";
                    }else{
                      $max = $this->model->generateCodePayment();
                      $code = "T".substr((substr($max,1)+100001),1);
                    }
                    $this->model->modify_amounts($bill['id'],$bill['remaining_amount'],0,1);
                    $amount_paid = $this->model->amount_paid($bill['id']);
                    $request = $this->model->mass_payments($bill['id'],$user,$client,$code,$typepay,$datetime,$comment,$bill['remaining_amount'],$amount_paid,0,1);
                    array_push($idbills,$bill['id']);
                    $total_pay = $total_pay - $bill['remaining_amount'];
                  }
                }
                $count = $count + $request;
              }
            }
            if($count >= 1){
              $consult_client = $this->model->select_client($client);
              $response = array('status' => 'success',
              'msg' => 'Se agrego el pago exitosamente.',
              'bills' => $idbills,
              'current_paid' => $current_paid,
              'business_name' => $_SESSION['businessData']['business_name'],
              'symbol' => $_SESSION['businessData']['symbol'],
              'client' => $consult_client['names']." ".$consult_client['surnames'],
              'mobile' => $consult_client['mobile'],
              'country' => $_SESSION['businessData']['country_code']
              );
            }else if($count == 0){
              $response = array('status' => 'warning', 'msg' => 'No se pudo realizar el pago.');
            }else{
              $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operación, intentelo nuevamente.');
            }
          }
          echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }
        die();
      }
      public function massive_pdfs(){
        if($_POST){
          if(isset($_POST['ids'])){
            $idbills = $_POST['ids'];
          }else{
            $idbills = [];
          }
          if(count($idbills) > 0){
            $data = $this->model->massive_bills($idbills);
            if(empty($data)){
              echo "Información no ha sido encontrada.";
            }else{
              ob_end_clean();
              $html = redirect_pdf("Resources/reports/pdf/massive_bills",$data);
              $customPaper = array(0,0,204,700);
              $dompdf = new Dompdf();
              $options = $dompdf->getOptions();
              $options->set(array('isRemoteEnabled' => true));
              $dompdf->setOptions($options);
              $dompdf->loadHtml($html);
              $orientation = 'portrait';
              $dompdf->setPaper($customPaper,$orientation);
              $dompdf->render();
              $voucher = "comprobante";
              $dompdf->stream($voucher.'.pdf',array("Attachment" => false));
            }
          }else{
            echo "No se enviaron datos, imposible realizar esta operacion.";
          }
        }
        die();
      }
      public function massive_impressions(){
        if($_POST){
          if(isset($_POST['ids'])){
            $idbills = $_POST['ids'];
          }else{
            $idbills = [];
          }
          if(count($idbills) > 0){
            $data = $this->model->massive_bills($idbills);
            if(empty($data)){
              echo "Información no ha sido encontrada.";
            }else{
              redirect_file("Resources/reports/prints/massive_bills",$data);
            }
          }else{
            echo "No se enviaron datos, imposible realizar esta operacion.";
          }
        }
        die();
      }
      public function massive_msj(){
        if($_POST){
          if(isset($_POST['ids'])){
            $idbills = explode(",",$_POST['ids']);
          }else{
            $idbills = [];
          }
          if(count($idbills) > 0){
            $data = $this->model->massive_bills($idbills);
            if(empty($data)){
              $response = array('status' => 'error', 'msg' => 'Información no ha sido encontrada.');
            }else{
              for($i=0; $i < count($data['bills']); $i++){
                $data['bills'][$i]['encrypt_id'] = encrypt($data['bills'][$i]['id']);
              }
              $response = array('status' => 'success', 'data' => $data);
            }
          }else{
            $response = array('status' => 'error', 'msg' => 'No se enviaron datos, imposible realizar esta operacion.');
          }
          echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }
        die();
      }
    }
