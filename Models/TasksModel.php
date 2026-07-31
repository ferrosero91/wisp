<?php
	class TasksModel extends Mysql{
		public function __construct(){
			parent::__construct();
		}
		public function customers_invoice(string $month,string $year){
			$sql = "SELECT c.id,c.clientid,c.payday,cl.names,cl.surnames
			FROM contracts c
			JOIN clients cl ON c.clientid = cl.id
			WHERE c.state NOT IN(4,5) AND c.clientid NOT IN(SELECT b.clientid FROM bills b WHERE MONTH(b.billed_month) = ? AND YEAR(b.billed_month) = ? AND b.state != 4 AND b.type = 2)";
			$data = array($month,$year);
			$asnwer = $this->select_all($sql,$data);
			return $asnwer;
		}
		public function service_amount(int $contract){
			$sql = "SELECT COALESCE(SUM(price),0) as total FROM detail_contracts WHERE contractid = $contract";
			$answer = $this->select($sql);
			$total =   $answer['total'];
			return $total;
		}
		public function returnCode(){
      $sql = "SELECT COUNT(internal_code) AS code FROM bills";
			$answer = $this->select($sql);
			$code = $answer['code'];
      return $code;
    }
		public function generateCode(){
			$sql = "SELECT MAX(internal_code) AS code FROM bills";
			$answer = $this->select($sql);
			$code = $answer['code'];
			return $code;
		}
		public function returnCorrelative(int $idvoucher,int $idserie){
			$sql = "SELECT MAX(correlative) as correlative FROM bills WHERE serieid = $idserie AND voucherid = $idvoucher";
			$answer = $this->select($sql);
			$correlative = $answer['correlative'];
      return $correlative;
		}
		public function returnUsed(int $idvoucher,int $idserie){
			$sql = "SELECT until - available + 1 AS used FROM voucher_series WHERE id = $idserie AND voucherid = $idvoucher";
			$answer = $this->select($sql);
			$used = $answer['used'];
			return $used;
		}
		public function returnBill(){
			$sql = "SELECT MAX(id) AS id FROM bills";
			$answer = $this->select($sql);
			$bill = $answer['id'];
			return $bill;
		}
		public function invoice_receipts(int $business,int $user,int $client,int $voucher,int $serie,string $code,string $correlative,string $issue,string $expiration,string $current,string $subtotal,string $discount,string $total,int $type,int $method,string $observation,string $state){
			$answer = 0;
			$query = "INSERT INTO bills(businessid,userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method,observation,state) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$data = array($business,$user,$client,$voucher,$serie,$code,$correlative,$issue,$expiration,$current,$subtotal,$discount,$total,$total,$type,$method,$observation,$state);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = $insert;
			}else{
				$answer = 0;
			}
			return $answer;
		}
		public function modify_available(int $idvoucher,int $idserie){
			$answer = "";
			$query = "UPDATE voucher_series SET available = available - ? WHERE id = $idserie AND voucherid = $idvoucher";
			$data = array(1);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function create_datail(int $id,int $type,int $serpro,string $description,string $quantity,string $price,string $total){
			$answer = "";
			$query = "INSERT INTO detail_bills(billid,type,serproid,description,quantity,price,total) VALUES(?,?,?,?,?,?,?)";
			$data = array($id,$type,$serpro,$description,$quantity,$price,$total);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function select_detail_contract(int $contract){
			$sql = "SELECT dc.id,dc.contractid,dc.serviceid,dc.price,s.service
			FROM detail_contracts dc
			JOIN services s ON dc.serviceid = s.id
			WHERE dc.contractid = $contract";
			$answer = $this->select_all($sql);
			return $answer;
		}
    public function pending_bills(){
			$sql = "SELECT * FROM bills WHERE state = 2 ORDER BY id ASC";
			$answer = $this->select_all($sql);
			return $answer;
		}
    public function expired_bills(int $bill){
      $answer = 0;
      $query = "UPDATE bills SET state = ? WHERE id = $bill";
      $data = array(3);
      $update = $this->update($query,$data);
      if($update){
        $answer = $update;
      }else{
        $answer = 0;
      }
      return $answer;
    }
    public function pending_tickets(){
      $sql = "SELECT * FROM tickets WHERE state = 2 ORDER BY id ASC";
      $answer = $this->select_all($sql);
      return $answer;
    }
    public function expired_tickets(int $ticket){
      $answer = 0;
      $query = "UPDATE tickets SET state = ? WHERE id = $ticket";
      $data = array(5);
      $update = $this->update($query,$data);
      if($update){
        $answer = $update;
      }else{
        $answer = 0;
      }
      return $answer;
    }
  }
