<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="author" content="<?= DEVELOPER ?>">
        <meta name="theme-color" content="#00acac">
        <?php
            if(!empty($_SESSION['businessData']['favicon'])){
                if($_SESSION['businessData']['favicon'] == "favicon.png"){
                    $favicon = base_style().'/images/logotypes/'.$_SESSION['businessData']['favicon'];;
                }else{
                    $favicon_url = base_style().'/uploads/business/'.$_SESSION['businessData']['favicon'];
                    if(@getimagesize($favicon_url)){
                        $favicon = base_style().'/uploads/business/'.$_SESSION['businessData']['favicon'];
                    }else{
                        $favicon = base_style().'/images/logotypes/favicon.png';
                    }
                }
            }else{
                $favicon = base_style().'/images/logotypes/favicon.png';
            }
        ?>
        <!-- ================== INICIO ICONO ================== -->
        <link rel="icon" type="image/x-icon" href="<?= $favicon ?>">
        <!-- ================== FIN ICONO ===================== -->
    	  <!-- ================== INICIO ARCHIVOS CSS =========== -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"/>
        <link rel="stylesheet" href="<?= base_style() ?>/css/default/app.min.css">
        <link rel="stylesheet" href="<?= base_style() ?>/css/datatables.min.css"/>
        <link rel="stylesheet" href="<?= base_style() ?>/css/superwisp.css">
        <link rel="stylesheet" href="<?= base_style() ?>/css/jquery-confirm.min.css">
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/simple-line-icons/css/simple-line-icons.css">
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/ionicons/css/ionicons.min.css">
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/gritter/css/jquery.gritter.css"/>
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/select2/css/select2.min.css">
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/smartwizard/css/smart_wizard.css">
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/lightbox/css/lightbox.css">
        <link rel="stylesheet" href="<?= base_style() ?>/css/modern.css">
        <!-- ================== FIN ARCHIVOS CSS ============== -->
        <!-- ================== INICIO TITULO ================= -->
        <title><?= $data['page_name'] ?></title>
        <!-- ================== FIN TITULO =================== -->
    </head>
    <body class="pace-done pace-done">
        <div id="loading"><span class="loading-spinner"></span></div>
        <div id="page-container" class="page-container fade page-sidebar-fixed page-header-fixed">
            <!-- ================== INICIO CABEZERA =============== -->
            <?php
                $route_current = isset($_GET['route']) ? $_GET['route'] : "";
                $current = array_pad(explode("/", $route_current), 2, "");
                require_once("navbar.php");
                require_once("sidemenu.php");
            ?>
            <!-- ================== FIN CABEZERA ================== -->
            <!-- ================== INICIO CONT. PAGINA =========== -->
            <div id="content" class="content">
