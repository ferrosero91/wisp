<?php
    class Login extends Controllers{
        public function __construct(){
            session_start();
            if(!empty($_SESSION['login'])) {
              header('Location: '.base_url().'/dashboard');
            }
            parent::__construct();
        }
        public function login(){
            $data['page_name'] = "Login";
            $data['page_functions_js'] = "login.js";
            $data['business'] = business_session();
            $data['csrf_token'] = generate_csrf_token();
            $this->views->getView($this,"login",$data);
        }
        public function validation(){
          if($_POST){
            // Validar CSRF Token
            if(empty($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])){
                $arrResponse = array('status' => "error", 'msg' => 'Token de seguridad inválido. Recargue la página.');
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                die();
            }

            if(empty($_POST['username']) || empty($_POST['password'])){
                $arrResponse = array('status' => "warning", 'msg' => 'El usuario y contraseña son campos obligatorios.');
            }else{
              $username = strtolower(sanitize_string($_POST['username']));
              $password = $_POST['password'];

              // Rate Limiting - Verificar intentos
              $rate_key = 'login_' . $username . '_' . $_SERVER['REMOTE_ADDR'];
              if(!check_rate_limit($rate_key, 5, 900)){
                  $remaining = get_rate_limit_remaining($rate_key);
                  $minutes = ceil($remaining / 60);
                  log_security_event('RATE_LIMIT', "Username: {$username}", 'WARNING');
                  $arrResponse = array('status' => "error", 'msg' => "Demasiados intentos. Espere {$minutes} minutos.");
                  echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                  die();
              }

              // Buscar usuario por username
              $request = $this->model->get_user_by_username($username);
              if(empty($request)){
                record_failed_attempt($rate_key);
                log_security_event('LOGIN_FAILED', "Username: {$username} - User not found", 'WARNING');
                $arrResponse = array('status' => "warning", 'msg' => 'Usuario o contraseña es incorrecta.');
              }else{
                $arrData = $request;

                // Verificar contraseña con bcrypt o AES (compatibilidad)
                $password_valid = false;
                if(is_bcrypt($arrData['password'])){
                    // Nuevo formato bcrypt
                    $password_valid = verify_password($password, $arrData['password']);
                }else{
                    // Formato antiguo AES - verificar y migrar
                    $old_hash = encrypt($password);
                    if($old_hash === $arrData['password']){
                        $password_valid = true;
                        // Migrar a bcrypt
                        $new_hash = hash_password($password);
                        $this->model->migrate_password($arrData['id'], $new_hash);
                        log_security_event('PASSWORD_MIGRATED', "User ID: {$arrData['id']}", 'INFO');
                    }
                }

                if(!$password_valid){
                    record_failed_attempt($rate_key);
                    log_security_event('LOGIN_FAILED', "Username: {$username} - Wrong password", 'WARNING');
                    $arrResponse = array('status' => "warning", 'msg' => 'Usuario o contraseña es incorrecta.');
                }else{
                    // Login exitoso - limpiar rate limit
                    clear_rate_limit($rate_key);

                    if($arrData['state'] == 1){
                      // Regenerar ID de sesión
                      regenerate_session_id();

                      if(!empty($_POST["remember"])){
                        $cookie_expiration_time = time() + (365 * 24 * 60 * 60);
                        setcookie("username",$username,$cookie_expiration_time);
                      }else{
                        clearCookie();
                      }
                      $_SESSION['idUser'] = $arrData['id'];
                      $_SESSION['login'] = true;
                      $_SESSION['last_activity'] = time();
                      $_SESSION['created'] = time();
                      $_SESSION['login_ip'] = $_SERVER['REMOTE_ADDR'];
                      $_SESSION['login_ua'] = $_SERVER['HTTP_USER_AGENT'];
                      $arrData = $this->model->login_session($_SESSION['idUser']);
                      user_session($_SESSION['idUser']);
                      business_session();
                      
                      log_security_event('LOGIN_SUCCESS', "User ID: {$_SESSION['idUser']}", 'INFO');
                      $arrResponse = array('status' => "success", 'msg' => 'ok');
                    }else{
                      log_security_event('LOGIN_DISABLED', "Username: {$username}", 'WARNING');
                      $arrResponse = array('status' => "error", 'msg' => 'El usuario se encuentra desactivado, comuniquese con su administrador.');
                    }
                }
              }
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
          }
          die();
        }
        public function reset(){
            if($_POST){
                if(empty($_POST['email'])){
                    $answer = array('status' => 'error', 'msg' => 'Ingrese un correo electrónico valido.');
                }else{
                    // Rate limiting para reset
                    $rate_key = 'reset_' . $_SERVER['REMOTE_ADDR'];
                    if(!check_rate_limit($rate_key, 3, 3600)){
                        $answer = array('status' => 'error', 'msg' => 'Demasiadas solicitudes. Espere una hora.');
                        echo json_encode($answer,JSON_UNESCAPED_UNICODE);
                        die();
                    }

                    $token = token();
                    $email = sanitize_email($_POST['email']);
                    
                    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $answer = array('status' => 'error', 'msg' => 'Ingrese un correo electrónico válido.');
                        echo json_encode($answer,JSON_UNESCAPED_UNICODE);
                        die();
                    }

                    $request = $this->model->validation_email($email);
                    if(empty($request)){
                        record_failed_attempt($rate_key);
                        log_security_event('RESET_FAILED', "Email: {$email} - Not found", 'WARNING');
                        $answer = array('status' => 'not_exist', 'msg' => 'No existe ningún operador con este correo.');
                    }else{
                        clear_rate_limit($rate_key);
                        $iduser = $request['id'];
                        $fullnames = $request['names'].' '.$request['surnames'];
                        $url_recovery = base_url().'/login/restore/'.encrypt($email).'/'.$token;
                        $businness = business_session();

                        $data = array(
                            'logo' => $businness['logo_email'],
                            'name_sender' => $businness['business_name'],
                            'sender' => $businness['email'],
                            'password' => $businness['password'],
                            'mobile' => $businness['mobile'],
                            'address' => $businness['address'],
                            'host' => $businness['server_host'],
                            'port' => $businness['port'],
                            'addressee' => $email,
                            'name_addressee' => $fullnames,
                            'affair' => 'Restablecer su contraseña',
                            'url_recovery' => $url_recovery,
                        );

                        $result = sendMail($data,"reset");
                        if($result === true){
                            $modify_token = $this->model->update_token($iduser,$token);
                            if($modify_token == 'success'){
                                log_security_event('RESET_REQUESTED', "User ID: {$iduser}", 'INFO');
                                $answer = array('status' => 'success', 'msg' => "Se le envio un correo, revise su bandeja de entrada.");
                            }else{
                                $answer = array('status' => false, 'msg' => 'No es posible realizar el proceso, intenta más tarde.');
                            }
                        }else{
                           $answer = array('status' => 'error', 'msg' => "No es posible realizar el proceso, intenta más tarde.");
                        }
                   }
               }
               echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function restore(string $params){
            if(empty($params)){
                header('Location: '.base_url());
            }else{
                $arrParams = explode(',',$params);
                $email = decrypt($arrParams[0]);
                $email = strClean($email);
                $token = strClean($arrParams[1]);
                $query = $this->model->user_information($email,$token);
                if(empty($query)){
                    header("Location: ".base_url());
                }else{
                    $data['email'] = $email;
                    $data['token'] = $token;
                    $data['id'] = encrypt($query['id']);
                    $data['page_name'] = "Restaurar contraseña";
                    $data['page_functions_js'] = "restore.js";
                    $data['business'] = business_session();
                    $this->views->getView($this,"restore_password",$data);
                }
            }
            die();
        }
        public function update_password(){
            if(empty($_POST['id']) || empty($_POST['email']) || empty($_POST['token']) || empty($_POST['password']) || empty($_POST['passwordConfirm'])){
                $answer = array('status' => false, 'msg' => 'Los campos son obligatorios.' );
            }else{
                $id = decrypt($_POST['id']);
                $id = intval($id);
                $password = $_POST['password'];
                $passwordConfirm = $_POST['passwordConfirm'];
                $email = sanitize_string($_POST['email']);
                $token = sanitize_string($_POST['token']);

                // Validar fortaleza de contraseña
                if(strlen($password) < 8){
                    $answer = array('status' => 'error', 'msg' => 'La contraseña debe tener al menos 8 caracteres.');
                }else if($password != $passwordConfirm){
                    $answer = array('status' => 'error', 'msg' => 'Las contraseñas no coinciden.');
                }else{
                    $request = $this->model->user_information($email,$token);
                    if(empty($request)){
                        $answer = array('status' => 'error', 'msg' => 'No se encontró información del usuario.');
                    }else{
                        // Usar bcrypt para nueva contraseña
                        $password_hash = hash_password($password);
                        $modify = $this->model->update_password($id, $password_hash);
                        if($modify == 'success'){
                            $businness = business_session();
                            $data = array(
                                'logo' => $businness['logo_email'],
                                'name_sender' => $businness['business_name'],
                                'sender' => $businness['email'],
                                'password' => $businness['password'],
                                'mobile' => $businness['mobile'],
                                'address' => $businness['address'],
                                'host' => $businness['server_host'],
                                'port' => $businness['port'],
                                'addressee' => $email,
                                'name_addressee' => $request['names']." ".$request['surnames'],
                                'affair' => 'Tu contraseña ha sido restablecida',
                            );

                            $result = sendMail($data,"change_password");
                            if($result === true){
                                log_security_event('PASSWORD_RESET', "User ID: {$id}", 'INFO');
                                $answer = array('status' => 'success', 'msg' => 'Tu contraseña ha sido restablecida.');
                            }else{
                               $answer = array('status' => 'error', 'msg' => "No es posible realizar el proceso, intenta más tarde.");
                            }
                        }else{
                            $answer = array('status' => 'error', 'msg' => 'No es posible realizar el proceso, intente más tarde.');
                        }
                    }
                }
            }
            echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            die();
        }
    }
