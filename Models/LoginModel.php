<?php
  class LoginModel extends Mysql{
    private $intId,$strUsername,$strPassword,$strEmail,$strToken;
    public function __construct(){
      parent::__construct();
    }
    public function validation(string $username, string $password){
			$this->strUsername = $username;
			$this->strPassword = $password;
			$sql = "SELECT id,state FROM users WHERE username = ? AND password = ? AND state != 0";
			$data = array($this->strUsername,$this->strPassword);
			$request = $this->select($sql,$data);
			return $request;
		}
		public function login_session(int $id){
			$this->intId = $id;
			$sql = "SELECT u.id,u.names,u.surnames,u.documentid,u.document,u.mobile,u.email,u.profileid,p.profile,u.username,u.password,u.image,u.state
			FROM users u
			JOIN profiles p ON u.profileid = p.id
			WHERE u.id = $this->intId";
			$request = $this->select($sql);
			$_SESSION['userData'] = $request;
			return $request;
    }
    public function validation_email(string $email){
    	$this->strEmail = $email;
      $sql = "SELECT id,names,surnames FROM users WHERE email = ? AND state = 1";
      $data = array($this->strEmail);
    	$request = $this->select($sql,$data);
    	return $request;
    }
    public function update_token(int $id, string $token){
      $this->intId = $id;
      $this->strToken = $token;
      $answer = "";
      $sql = "UPDATE users SET token = ? WHERE id = $this->intId";
      $data = array($this->strToken);
      $update = $this->update($sql,$data);
      if($update){
        $answer = 'success';
      }else{
        $answer = 'error';
      }
      return $answer;
    }
    public function user_information(string $email, string $token){
      $this->strEmail = $email;
      $this->strToken = $token;
    	$sql = "SELECT id,names,surnames FROM users WHERE email = ? AND token = ? AND state = 1 ";
    	$data = array($this->strEmail,$this->strToken);
    	$request = $this->select($sql,$data);
    	return $request;
    }
    public function update_password(int $id, string $password){
      $this->intId = $id;
      $this->strPassword = $password;
      $answer = "";
      $sql = "UPDATE users SET password = ?, token = ? WHERE id = $this->intId";
      $arrData = array($this->strPassword,"");
      $update = $this->update($sql,$arrData);
      if($update){
        $answer = 'success';
      }else{
        $answer = 'error';
      }
      return $answer;
    }

    /**
     * Obtiene usuario por username para login
     */
    public function get_user_by_username(string $username){
        $this->strUsername = $username;
        $sql = "SELECT id, username, password, state FROM users WHERE username = ? AND state != 0";
        $data = array($this->strUsername);
        $request = $this->select($sql, $data);
        return $request;
    }

    /**
     * Migra contraseña de AES a bcrypt
     */
    public function migrate_password(int $id, string $new_hash){
        $this->intId = $id;
        $this->strPassword = $new_hash;
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $data = array($this->strPassword, $this->intId);
        $update = $this->update($query, $data);
        return $update ? 'success' : 'error';
    }
  }
