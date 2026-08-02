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
        <link rel="icon" type="image/x-icon" href="<?= $favicon ?>">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
        <link rel="stylesheet" href="<?= base_style() ?>/css/default/app.min.css">
        <link rel="stylesheet" href="<?= base_style() ?>/css/jquery-confirm.min.css">
        <link rel="stylesheet" href="<?= base_style() ?>/bookstores/gritter/css/jquery.gritter.css"/>
        <link rel="stylesheet" href="<?= base_style() ?>/css/login.css">
        <link rel="stylesheet" href="<?= base_style() ?>/css/login-modern.css">
        <title><?= $data['page_name'] ?></title>
    </head>
    <body class="pace-top">
        <div id="loading"><span class="loading-spinner"></span></div>
    	<div class="login-cover">
        <?php
          if(!empty($data['business']['background'])){
            $background_url = base_style().'/images/background/'.$data['business']['background'];
            if(@getimagesize($background_url)){
                $background = base_style().'/images/background/'.$data['business']['background'];
            }else{
                $background = base_style().'/images/background/bg-1.jpeg';
            }
          }else{
            $background = base_style().'/images/background/bg-1.jpeg';
          }
        ?>
    		<div id="particles-js" class="login-cover-image" style="background-image: url(<?= $background ?>)" data-id="login-cover-image"></div>
    		<div class="login-cover-bg"></div>
    	</div>
	    <div id="page-container" class="fade">
		    <div class="login login-v2" data-pageload-addclass="animated fadeIn">
    			<div class="login-header">
    				<div class="brand">
                        <?php
                            if(!empty($data['business']['logo_login'])){
                                if($data['business']['logo_login'] == "superwisp_white.png"){
                                    $logo = base_style().'/images/logotypes/'.$data['business']['logo_login'];
                                }else{
                                    $logo_url = base_style().'/uploads/business/'.$data['business']['logo_login'];
                                    if(@getimagesize($logo_url)){
                                        $logo = base_style().'/uploads/business/'.$data['business']['logo_login'];
                                    }else{
                                        $logo = base_style().'/images/logotypes/superwisp_white.png';
                                    }
                                }
                            }else{
                                $logo = base_style().'/images/logotypes/superwisp_white.png';
                            }
                        ?>
                        <img src="<?= $logo ?>" class="img-responsive" alt="Logo">
    				</div>
    			</div>
    			<div class="login-content">
				<form name="transactions" id="transactions" autocomplete="off" class="margin-bottom-0">
					<input type="hidden" name="csrf_token" value="<?= $data['csrf_token'] ?>">
					<div class="form-group">
						<i class="fa fa-user field-icon"></i>
						<input type="text" class="form-control" placeholder="Usuario" id="username" name="username" value="<?php if(isset($_COOKIE["username"])){ echo $_COOKIE["username"];} ?>">
    					</div>
    					<div class="form-group">
    						<i class="fa fa-lock field-icon"></i>
    						<input type="password" class="form-control" placeholder="Contrase&ntilde;a" id="password" name="password" autocomplete="current-password">
                <i class="fa fa-eye-slash showHidePw"></i>
    					</div>
              <div class="checkbox checkbox-css">
                <input type="checkbox" id="remember" name="remember" <?php if(isset($_COOKIE["username"])){ ?> checked <?php } ?>>
                <label for="remember">Mantener sesi&oacute;n</label>
              </div>
    					<div class="login-buttons">
    						<button type="submit" class="btn btn-block">Ingresar</button>
    					</div>
    					<div class="m-t-5">
    						&iquest;Olvidaste tu contrase&ntilde;a? <a href="javascript:;" onclick="modal();">Click aqu&iacute;</a>
    					</div>
    				</form>
    			</div>
		    </div>
	    </div>
        <?php
          modal("loginModal",$data);
        ?>
        <script> const base_url = "<?= base_url(); ?>"; </script>
        <script src="<?= base_style() ?>/js/app.min.js"></script>
        <script src="<?= base_style() ?>/js/particles.min.js"></script>
        <script src="<?= base_style() ?>/js/functions.js"></script>
        <script src="<?= base_style() ?>/js/jquery-confirm.min.js"></script>
        <script src="<?= base_style() ?>/bookstores/parsleyjs/parsley.js"></script>
        <script src="<?= base_style() ?>/bookstores/gritter/js/jquery.gritter.min.js"></script>
        <script src="<?= base_style() ?>/js/functions/<?= $data['page_functions_js']; ?>"></script>
    </body>
</html>
