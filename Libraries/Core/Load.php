<?php
    // Mapeo de rutas a nombres de controladores (para nombres compuestos)
    $controllerMap = array(
        'electronicinvoice' => 'ElectronicInvoice',
        'electronicreports' => 'ElectronicReports',
    );
    
    // Buscar en el mapa de controladores
    $controllerLower = strtolower($controller);
    if(isset($controllerMap[$controllerLower])){
        $controller = $controllerMap[$controllerLower];
    }else{
        $controller = ucwords($controller);
    }
    
    $controllerFile = "Controllers/".$controller.".php";
    if(file_exists($controllerFile)){
        require_once($controllerFile);
        $controllerInstance = new $controller();
        
        // Si el método es "index" y no existe, intentar con el nombre del controlador
        if($methop == "index" && !method_exists($controllerInstance, "index")){
            $methop = strtolower($controller);
        }
        
        if(method_exists($controllerInstance,$methop)){
            $controllerInstance->{$methop}($params);
        }else{
            require_once("Controllers/Error.php");
        }
    }else{
        require_once("Controllers/Error.php");
    }
