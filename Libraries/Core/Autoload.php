<?php
    spl_autoload_register(function($class){
        if(file_exists("Libraries/".'Core/'.$class.'.php')){
            require_once("Libraries/".'Core/'.$class.'.php');
        }
    });
    
    // Instancia global de caché
    $GLOBALS['cache'] = new Cache(300); // 5 minutos TTL por defecto
