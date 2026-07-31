<?php
    class Controllers{
        public $views;
        public $model;
        public function __construct(){
            $this->views = new Views();
            $this->loadModel();
        }
        public function loadModel(){
            $model = get_class($this)."Model";
            $modelFile = "Models/".$model.".php";
            if(file_exists($modelFile)) {
                require_once($modelFile);
                $this->model = new $model();
            }
        }
    }
