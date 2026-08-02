<?php
class ElectronicResolutionModel extends Mysql{
    private $intId;
    
    public function __construct(){
        parent::__construct();
    }
    
    public function list_resolutions(){
        $sql = "SELECT * FROM electronic_resolutions ORDER BY type_document_id, prefix";
        $request = $this->select_all($sql);
        return $request;
    }
    
    public function select_record(int $id){
        $this->intId = $id;
        $sql = "SELECT * FROM electronic_resolutions WHERE id = $this->intId";
        $request = $this->select($sql);
        return $request;
    }
    
    public function get_by_type(int $type_document_id){
        $sql = "SELECT * FROM electronic_resolutions WHERE type_document_id = ? AND state = 1 ORDER BY id DESC";
        $data = array($type_document_id);
        $request = $this->select_all($sql, $data);
        return $request;
    }
    
    public function get_active_by_type(int $type_document_id){
        // Para NC y ND no se verifican fechas de vigencia
        if($type_document_id == 4 || $type_document_id == 5){
            $sql = "SELECT * FROM electronic_resolutions WHERE type_document_id = ? AND state = 1 ORDER BY id DESC LIMIT 1";
        }else{
            $sql = "SELECT * FROM electronic_resolutions WHERE type_document_id = ? AND state = 1 AND (date_to = '0000-00-00' OR date_to IS NULL OR date_to >= CURDATE()) ORDER BY id DESC LIMIT 1";
        }
        $data = array($type_document_id);
        $request = $this->select($sql, $data);
        return $request;
    }
    
    public function select_record_by_type(int $type_document_id){
        if($type_document_id == 4 || $type_document_id == 5){
            $sql = "SELECT * FROM electronic_resolutions WHERE type_document_id = ? AND state = 1 ORDER BY id DESC LIMIT 1";
        }else{
            $sql = "SELECT * FROM electronic_resolutions WHERE type_document_id = ? AND state = 1 AND (date_to = '0000-00-00' OR date_to IS NULL OR date_to >= CURDATE()) ORDER BY id DESC LIMIT 1";
        }
        $data = array($type_document_id);
        $request = $this->select($sql, $data);
        return $request;
    }
    
    public function find_by_prefix_and_number(int $type_document_id, string $prefix, string $resolution_number){
        $sql = "SELECT * FROM electronic_resolutions WHERE type_document_id = ? AND prefix = ? AND resolution_number = ? LIMIT 1";
        $data = array($type_document_id, strtoupper($prefix), $resolution_number);
        $request = $this->select($sql, $data);
        return $request;
    }
    
    public function create_resolution(int $type_document_id, string $prefix, string $resolution_number, string $resolution_date, string $date_from, string $date_to, int $consecutive_from, int $consecutive_to, string $technical_key = '', int $current_consecutive = 0){
        $answer = "";
        if($current_consecutive == 0){
            $current_consecutive = $consecutive_from;
        }
        $sql = "INSERT INTO electronic_resolutions(type_document_id, prefix, resolution_number, resolution_date, date_from, date_to, consecutive_from, consecutive_to, current_consecutive, technical_key, state) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        $data = array($type_document_id, strtoupper($prefix), $resolution_number, $resolution_date, $date_from, $date_to, $consecutive_from, $consecutive_to, $current_consecutive, $technical_key, 1);
        $request = $this->insert($sql, $data);
        if($request > 0){
            $answer = 'success';
        }else{
            $answer = 'error';
        }
        return $answer;
    }
    
    public function update_resolution(int $id, int $type_document_id, string $prefix, string $resolution_number, string $resolution_date, string $date_from, string $date_to, int $consecutive_from, int $consecutive_to, string $technical_key = '', int $current_consecutive = 0){
        $this->intId = $id;
        $answer = "";
        if($current_consecutive > 0){
            $sql = "UPDATE electronic_resolutions SET type_document_id=?, prefix=?, resolution_number=?, resolution_date=?, date_from=?, date_to=?, consecutive_from=?, consecutive_to=?, technical_key=?, current_consecutive=? WHERE id = $this->intId";
            $data = array($type_document_id, strtoupper($prefix), $resolution_number, $resolution_date, $date_from, $date_to, $consecutive_from, $consecutive_to, $technical_key, $current_consecutive);
        }else{
            $sql = "UPDATE electronic_resolutions SET type_document_id=?, prefix=?, resolution_number=?, resolution_date=?, date_from=?, date_to=?, consecutive_from=?, consecutive_to=?, technical_key=? WHERE id = $this->intId";
            $data = array($type_document_id, strtoupper($prefix), $resolution_number, $resolution_date, $date_from, $date_to, $consecutive_from, $consecutive_to, $technical_key);
        }
        $request = $this->update($sql, $data);
        if($request){
            $answer = 'success';
        }else{
            $answer = 'error';
        }
        return $answer;
    }
    
    public function update_state(int $id, int $state){
        $this->intId = $id;
        $answer = "";
        $sql = "UPDATE electronic_resolutions SET state = ? WHERE id = $this->intId";
        $data = array($state);
        $request = $this->update($sql, $data);
        if($request){
            $answer = 'success';
        }else{
            $answer = 'error';
        }
        return $answer;
    }
    
    public function increment_consecutive(int $id){
        $this->intId = $id;
        $answer = "";
        $sql = "UPDATE electronic_resolutions SET current_consecutive = current_consecutive + 1 WHERE id = $this->intId";
        $request = $this->update($sql, array());
        if($request){
            $answer = 'success';
        }else{
            $answer = 'error';
        }
        return $answer;
    }
    
    public function delete_resolution(int $id){
        $this->intId = $id;
        $answer = "";
        $sql = "DELETE FROM electronic_resolutions WHERE id = ?";
        $data = array($this->intId);
        $request = $this->delete($sql, $data);
        if($request){
            $answer = 'success';
        }else{
            $answer = 'error';
        }
        return $answer;
    }
}
