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
    }
