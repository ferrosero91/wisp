<?php
require 'Libraries/resize/vendor/autoload.php';
use Verot\Upload\Upload;
    class Users extends Controllers{
        public function __construct(){
            parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(USERS);
        }
        public function users(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Usuarios";
            $data['page_title'] = "Gestión de Usuarios";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Ajustes";
            $data['actual_page'] = "Usuarios";
            $data['page_functions_js'] = "users.js";
            $this->views->getView($this,"users",$data);
        }
        public function list_records(){
            if(empty($_SESSION['permits_module']['v'])){
                send_json([]);
            }
                $n = 1;
	            $data = $this->model->list_records();
	            for($i=0; $i < count($data); $i++){
                    $data[$i]['n'] = $n++;
                    $data[$i]['encrypt'] = encrypt($data[$i]['id']);
                    $mobile = "";
                    if(!empty($data[$i]['mobile'])){
                      $mobile = '<a href="tel:+'.$_SESSION['businessData']['country_code'].$data[$i]['mobile'].'"><i class="fa fa-mobile mr-1"></i>'.$data[$i]['mobile'].'</a>';
                    }
                    $data[$i]['cellphone'] = $mobile;
                    $data[$i]['fullname'] = $data[$i]['names']." ".$data[$i]['surnames'];
                    /* BOTON WHATSAPP */
                    $whatsapp = (empty($data[$i]['mobile'])) ? '' : '<a href="javascript:;" class="whatsapp" data-toggle="tooltip" data-original-title="WhatsApp" onclick="open_message(\''.$_SESSION['businessData']['country_code'].'\',\''.$data[$i]['mobile'].'\')"><i class="fab fa-whatsapp"></i></a>';
                    $whatsapp_2 = (empty($data[$i]['mobile'])) ? '' : '<a href="javascript:;" class="dropdown-item" onclick="open_message(\''.$_SESSION['businessData']['country_code'].'\',\''.$data[$i]['mobile'].'\')"><i class="fab fa-whatsapp mr-1"></i>WhatsApp</a>';
                    $data[$i]['date'] = date("d/m/Y", strtotime($data[$i]['registration_date']));
                    if($_SESSION['permits_module']['a']){
            						if(($_SESSION['idUser'] == 1 and $_SESSION['userData']['profileid'] == 1) || ($_SESSION['userData']['profileid'] == 1 and $data[$i]['profileid'] != 1)){
                            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt"></i></a>';
                            $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            						}else{
                            $update = '';
                            $update_2 = '';
                        }
            				}else{
                        $update = '';
                        $update_2 = '';
                    }
                    if($_SESSION['permits_module']['e']){
                        if(($_SESSION['idUser'] == 1 and $_SESSION['userData']['profileid'] == 1) || ($_SESSION['userData']['profileid'] == 1 and $data[$i]['profileid'] != 1) and ($_SESSION['userData']['id'] != $data[$i]['id'])){
                          $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt"></i></a>';
                          $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
                        }else{
                          $delete = '';
                          $delete_2 = '';
          					    }
                    }else{
                        $delete = '';
                        $delete_2 = '';
          					}
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$update.$delete.$whatsapp.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$update_2.$delete_2.$whatsapp_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                send_json($data);
            die();
        }
        public function select_record(string $iduser){
            if($_SESSION['permits_module']['v']){
                $iduser = decrypt($iduser);
                $iduser = intval($iduser);
                if($iduser > 0){
                    $data = $this->model->select_record($iduser);
                    if(empty($data)){
                        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                    }else{
                        $data['encrypt_id'] = encrypt($data['id']);
                        $data['encrypt_profile'] = encrypt($data['profileid']);
                        $data['country'] = $_SESSION['businessData']['country_code'];
                        $answer = array('status' => 'success', 'data' => $data);
                    }
                }else{
                  $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                }
                echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function list_documents(){
            $html = "";
            $data = $this->model->list_documents();
            if(count($data) > 0){
                for($i=0; $i < count($data); $i++) {
                    $html .= '<option value="'.$data[$i]['id'].'">'.$data[$i]['document'].'</option>';
                }
            }
            echo $html;
            die();
        }
        public function list_users(){
            $html = "";
            $data = $this->model->list_users();
            if(count($data) > 0){
                $html .= '<option value="0">TODOS</option>';
                for($i=0; $i < count($data); $i++) {
                    $html .= '<option value="'.$data[$i]['id'].'">'.$data[$i]['names'].' '.$data[$i]['surnames'].'</option>';
                }
            }
            echo $html;
            die();
        }
        public function search_document(string $params){
            $arrParams = explode(",",$params);
            $type = $arrParams[0];
            $document = $arrParams[1];
            if($type == 2){
                $validate = strlen($document);
                if($validate < 8){
                    $arrResponse = array('status' => 'info', 'msg' => 'La cédula no debe tener menos de 8 digitos.');
                }else if($validate > 10){
                    $arrResponse = array('status' => 'info', 'msg' => 'La cédula no debe tener mas de 10 digitos.');
                }else{
                    $answer = consult_document("dni",$document,$_SESSION['businessData']['reniec_apikey']);
                    if(empty($answer['success'])){
                        $arrResponse = array('status' => 'error', 'msg' => 'No se encontraton resultados.');
                    }else{
                        $arrConsult = array(
                            "names" => $answer['data']['nombres'],
                            "surnames" => $answer['data']['apellido_paterno']." ".$answer['data']['apellido_materno'],
                            "dni" => $answer['data']['numero']
                        );
                        $arrResponse = array('status' => 'success', 'data' => $arrConsult);
                    }
                }
            }else if(in_array($type, array(4,5,6,7,8))){
                $arrResponse = array('status' => 'info', 'msg' => 'La consulta automática no esta disponible para este tipo de documento, complete los datos manualmente.');
            }else{
                $arrResponse = array('status' => 'error', 'msg' => 'La busqueda solo es para la cédula.');
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function action(){
          if($_POST){
            // Validar CSRF Token
            if(empty($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])){
                $response = array("status" => 'error', "msg" => 'Token de seguridad inválido.');
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
                die();
            }

            if(empty($_POST['names']) || empty($_POST['surnames']) || empty($_POST['document']) || empty($_POST['username'])){
        				$response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
        		}else{
              $id = decrypt($_POST['iduser']);
              $id = intval($id);
              $names = strtoupper(sanitize_string($_POST['names']));
              $surnames = strtoupper(sanitize_string($_POST['surnames']));
              $types = intval($_POST['listTypes']);
              $document = sanitize_string($_POST['document']);
              $mobile = sanitize_string($_POST['mobile']);
              $email = sanitize_email($_POST['email']);
              $profile = decrypt($_POST['listProfiles']);
              $profile = intval($profile);
              $username = strtolower(sanitize_string($_POST['username']));
              $state = intval($_POST['listStatus']);
              $image = "user_default.png";
              $datetime = date("Y-m-d H:i:s");
              
              if($id == 0){
    				    $option = 1;
                    // Usar bcrypt para nueva contraseña
                    $password = empty($_POST['password']) ? hash_password(generate_password()) : hash_password($_POST['password']);
      					if($_SESSION['permits_module']['r']){
      						$request = $this->model->create($names,$surnames,$types,$document,$mobile,$email,$profile,$username,$password,$image,$datetime,$state);
      					}
      				}else{
                $option = 2;
                // Usar bcrypt si se proporciona nueva contraseña
                $password = empty($_POST['password']) ? "" : hash_password($_POST['password']);
                if($_SESSION['permits_module']['a']){
                  $request = $this->model->modify($id,$names,$surnames,$types,$document,$mobile,$email,$profile,$username,$password,$state);
                }
      				}
              if($request == "success"){
                  log_security_event('USER_CREATED', "User ID: {$id} by User ID: {$_SESSION['idUser']}", 'INFO');
      					if($option == 1){
      						$response = array('status' => 'success', 'msg' => 'Se ha registrado exitosamente.');
      					}else{
      						$response = array('status' => 'success', 'msg' => 'Se ha actualizado el registro exitosamente.');
      					}
      				}else if($request == 'exists'){
      					$response = array('status' => 'error', 'msg' => '¡Atención! El registro ya existe, ingrese otro.');
      				}else{
      					$response = array("status" => 'error', "msg" => 'No se pudo realizar esta operación, intentelo nuevamente.');
      				}
            }
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
          }
          die();
        }
        public function modify_data(){
            if($_POST){
                if(empty($_POST['names']) || empty($_POST['surnames']) || empty($_POST['email'])){
                    $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
                }else{
                    $id = intval($_SESSION['idUser']);
                    $names = strtoupper(strClean($_POST['names']));
                    $surnames = strtoupper(strClean($_POST['surnames']));
                    $mobile = strClean($_POST['mobile']);
                    $email = strtolower(strClean($_POST['email']));

                    $request = $this->model->modify_data($id,$names,$surnames,$mobile,$email);

                    if($request == "success"){
                        user_session($_SESSION['idUser']);
                        $response = array('status' => 'success', 'msg' => 'Datos actualizado correctamente.');
                    }else{
                        $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function change_profile(){
          if($_POST){
            if(empty($_POST['current_photo'])){
              $response = array('status' => 'error', 'msg' => 'Error de datos.');
            }else{
              /* VARIABLES PARA EL REGISTRO A LA BD*/
              $id = intval($_SESSION['idUser']);
              /* IMAGEN DESDE EL FORMULARIO */
              $photo = $_FILES['photo'];
              $name = $photo['name'];
              /* EXTENCION DE IMAGEN */
              $ext = explode(".", $name);
              /* RUTA Y NOMBRE DE LA NUEVA IMAGEN */
              $image = 'profile_'.md5(round(microtime(true))).'.'.end($ext);
              $image_file = 'profile_'.md5(round(microtime(true)));
              $save_path = 'Assets/uploads/users/';
              $url_image = base_style().'/uploads/users/profile_'.md5(round(microtime(true))).'.'.end($ext);
              /* REGISTRAR Y GUARDAR IMAGEN */
              $request = $this->model->change_profile($id,$image);
              if($request == "success"){
                user_session($id);
                if(isset($photo)){
                  $up = new Upload($photo);
                  if($up->uploaded){
                    $up->file_new_name_body = $image_file;
                    $up->image_resize = true;
                    $up->image_x = 448;
                    $up->image_ratio_y = true;
                    $up->Process($save_path);
                    if($up->processed){
                      $up->clean();
                    }
                  }
                }
                if($name != '' && $_POST['current_photo'] != 'user_default.png'){
                  delete_image('users',$_POST['current_photo']);
                }
                $response = array('status' => 'success', 'msg' => 'Se ha cambiado la foto de perfil.');
              }else{
                $response = array('status' => 'error', 'msg' => 'No se pudo completar esta operación.');
              }
            }
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
          }
          die();
        }
        public function modify_password(){
            if($_POST){
                // Validar CSRF Token
                if(empty($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])){
                    $response = array("status" => 'error', "msg" => 'Token de seguridad inválido.');
                    echo json_encode($response,JSON_UNESCAPED_UNICODE);
                    die();
                }

                if(empty($_POST['password']) || empty($_POST['repeat_password'])){
                    $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
                }else{
                    $id = intval($_SESSION['idUser']);
                    $password = $_POST['password'];
                    $repeat_password = $_POST['repeat_password'];

                    // Validar fortaleza de contraseña
                    if(strlen($password) < 8){
                        $response = array("status" => 'error', "msg" => 'La contraseña debe tener al menos 8 caracteres.');
                    }else if($password !== $repeat_password){
                        $response = array("status" => 'error', "msg" => 'Las contraseñas no coinciden.');
                    }else{
                        // Usar bcrypt para nueva contraseña
                        $password_hash = hash_password($password);
                        $request = $this->model->modify_password($id, $password_hash);

                        if($request == "success"){
                            user_session($_SESSION['idUser']);
                            log_security_event('PASSWORD_CHANGED', "User ID: {$id}", 'INFO');
                            $response = array('status' => 'success', 'msg' => 'Contraseña actualizada correctamente.');
                        }else{
                            $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
                        }
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function remove(){
            if($_POST){
                if($_SESSION['permits_module']['e']){
                    $iduser = decrypt($_POST['iduser']);
                    $iduser =  intval($iduser);
                    $request = $this->model->remove($iduser);
                    if($request == 'success'){
                      $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
                    }else{
                      $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
                    }
                    echo json_encode($response,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
    }
