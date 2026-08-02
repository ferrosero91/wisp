<?php
class SettingsModel extends Mysql{
    public function __construct(){
        parent::__construct();
    }
    
    /* DATOS DE REFERENCIA DIAN */
    public function get_dian_type_documents(){
        $sql = "SELECT * FROM dian_type_documents ORDER BY id";
        return $this->select_all($sql);
    }
    public function get_dian_type_organizations(){
        $sql = "SELECT * FROM dian_type_organizations ORDER BY id";
        return $this->select_all($sql);
    }
    public function get_dian_type_regimes(){
        $sql = "SELECT * FROM dian_type_regimes ORDER BY id";
        return $this->select_all($sql);
    }
    public function get_dian_type_liabilities(){
        $sql = "SELECT * FROM dian_type_liabilities ORDER BY id";
        return $this->select_all($sql);
    }
    public function get_dian_municipalities(){
        $sql = "SELECT * FROM dian_municipalities ORDER BY name";
        return $this->select_all($sql);
    }
    public function get_dian_payment_forms(){
        $sql = "SELECT * FROM dian_payment_forms ORDER BY id";
        return $this->select_all($sql);
    }
    public function get_dian_payment_methods(){
        $sql = "SELECT * FROM dian_payment_methods ORDER BY id";
        return $this->select_all($sql);
    }
    
    /* CONFIGURACION APIDIAN */
    public function update_apidian_connection(int $id, string $apidian_url, string $apidian_token, string $apidian_environment){
        $sql = "UPDATE business SET apidian_url=?, apidian_token=?, apidian_environment=? WHERE id = ?";
        $data = array($apidian_url, $apidian_token, $apidian_environment, $id);
        $request = $this->update($sql, $data);
        return $request ? 'success' : 'error';
    }
    public function update_apidian_nit(int $id, string $nit, string $dv){
        $sql = "UPDATE business SET apidian_nit=?, apidian_dv=? WHERE id = ?";
        $data = array($nit, $dv, $id);
        $request = $this->update($sql, $data);
        return $request ? 'success' : 'error';
    }
    public function update_apidian_token(int $id, string $token){
        $sql = "UPDATE business SET apidian_token = ? WHERE id = ?";
        $data = array($token, $id);
        $request = $this->update($sql, $data);
        return $request ? 'success' : 'error';
    }
    public function update_taxes(int $id, float $tax_rate, string $tax_name){
        $sql = "UPDATE business SET tax_rate=?, tax_name=? WHERE id = ?";
        $data = array($tax_rate, $tax_name, $id);
        $request = $this->update($sql, $data);
        return $request ? 'success' : 'error';
    }
    public function show_business(){
        $sql = "SELECT b.*, c.symbol, c.money, c.money_plural FROM business b JOIN currency c ON b.currencyid = c.id";
        $request = $this->select($sql);
        $_SESSION['businessData'] = $request;
        return $request;
    }
    public function save_apidian_company_config(int $id, array $data){
        $sql = "UPDATE business SET apidian_type_doc=?, apidian_type_org=?, apidian_type_regime=?, apidian_type_liability=?, apidian_merchant=?, apidian_business_name=?, apidian_municipality_id=?, apidian_address=?, apidian_phone=?, apidian_email=?, apidian_configured=1 WHERE id = ?";
        $params = array(
            $data['type_doc'], $data['type_org'], $data['type_regime'], $data['type_liability'],
            $data['merchant'], $data['business_name'], $data['municipality_id'],
            $data['address'], $data['phone'], $data['email'], $id
        );
        $request = $this->update($sql, $params);
        return $request ? 'success' : 'error';
    }
    public function save_certificate(int $id, string $certificate_file, string $certificate_password, int $days_left = 0){
        $sql = "UPDATE business SET apidian_certificate_file=?, apidian_certificate_password=?, apidian_certificate_configured=1, apidian_certificate_days_left=? WHERE id = ?";
        $params = array($certificate_file, $certificate_password, $days_left, $id);
        $request = $this->update($sql, $params);
        return $request ? 'success' : 'error';
    }
    public function save_software_config(int $id, string $software_id, string $software_pin){
        $sql = "UPDATE business SET apidian_software_id=?, apidian_software_pin=?, apidian_software_configured=1 WHERE id = ?";
        $params = array($software_id, $software_pin, $id);
        $request = $this->update($sql, $params);
        return $request ? 'success' : 'error';
    }
}
