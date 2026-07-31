<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="author" content="<?= DEVELOPER ?>">
        <meta name="theme-color" content="#00acac">
        <?php
            if(!empty($data['business']['favicon'])){
                if($data['business']['favicon'] == "favicon.png"){
                    $favicon = base_style().'/images/logotypes/'.$data['business']['favicon'];;
                }else{
                    $favicon_url = base_style().'/uploads/business/'.$data['business']['favicon'];
                    if(@getimagesize($favicon_url)){
                        $favicon = base_style().'/uploads/business/'.$data['business']['favicon'];
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
        <link rel="stylesheet" href="<?= base_style() ?>/css/default/app.min.css" >
        <!-- ================== FIN ARCHIVOS CSS ============== -->
        <!-- ================== INICIO TITULO ================= -->
        <title><?= $data['page_name'] ?></title>
        <!-- ================== FIN TITULO =================== -->
    </head>
    <body class="pace-top">
    	<div id="page-container" class="fade">
    		<div class="error">
    			<div class="error-code">404</div>
    			<div class="error-content">
    				<div class="error-message">No pudimos encontrarlo...</div>
    				<div class="error-desc mb-3 mb-sm-4 mb-md-5">
    					La página que buscas no existe.
    				</div>
    				<div>
    					<a href="javascript:window.history.back();" class="btn btn-success p-l-20 p-r-20"><i class="fas fa-home mr-1"></i>Regresar al panel</a>
    				</div>
    			</div>
    		</div>
    	</div>
        <!-- ================== INICIO ARCHIVOS JS ======== -->
        <script src="<?= base_style() ?>/js/app.min.js"></script>
        <!-- ================== END ARCHIVOS JS =========== -->
    </body>
</html>
