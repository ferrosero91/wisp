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
        <link rel="stylesheet" href="<?= base_style() ?>/css/login.css?v=2026080211">
        <link rel="stylesheet" href="<?= base_style() ?>/css/login-modern.css?v=2026080211">
        <title><?= $data['page_name'] ?></title>
    </head>
    <body class="pace-top">
        <div id="loading" style="position:fixed;top:0;left:0;bottom:0;right:0;background:rgba(6,18,32,.65);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;opacity:0;transition:opacity 0.3s ease;">
            <div class="loading-box" style="background:rgba(255,255,255,.97);-webkit-backdrop-filter:blur(24px);backdrop-filter:blur(24px);border-radius:20px;box-shadow:0 30px 90px rgba(0,0,0,.3),0 0 0 1px rgba(255,255,255,.15) inset;padding:44px 52px 36px;text-align:center;min-width:320px;max-width:92vw;display:flex;flex-direction:column;align-items:center;transform:scale(0.85) translateY(30px);transition:transform 0.5s cubic-bezier(0.34,1.56,0.64,1);position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#0e9f9f,#14c4b8,#0e9f9f);background-size:200% 100%;animation:loadingShimmer 2s ease infinite;"></div>
                <div class="loading-icon-wrapper" style="position:relative;width:88px;height:88px;margin-bottom:22px;">
                    <div class="loading-spinner-ring" style="position:absolute;top:0;left:0;width:100%;height:100%;border-radius:50%;border:3px solid rgba(14,159,159,.12);border-top-color:#0e9f9f;animation:spinnerRotate 1.2s cubic-bezier(0.5,0,0.5,1) infinite;">
                        <div class="loading-ring-inner" style="position:absolute;top:7px;left:7px;right:7px;bottom:7px;border-radius:50%;border:2px solid rgba(14,159,159,.08);border-bottom-color:#14c4b8;animation:spinnerRotate 0.8s linear infinite reverse;"></div>
                    </div>
                    <div class="loading-icon-content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:44px;height:44px;background:linear-gradient(135deg,#0e9f9f,#14c4b8);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(14,159,159,.35);animation:iconPulse 2s ease-in-out infinite;">
                        <i class="fas fa-shield-alt" style="color:#ffffff;font-size:20px;"></i>
                    </div>
                </div>
                <div class="loading-title" style="font-size:19px;font-weight:700;color:#1a2332;margin-bottom:6px;letter-spacing:-0.02em;">Iniciando Sesión</div>
                <div class="loading-message" style="font-size:13.5px;font-weight:500;color:#6b7f97;line-height:1.5;max-width:280px;margin-bottom:18px;transition:all 0.3s ease;">Verificando credenciales...</div>
                <div class="loading-progress" style="width:220px;height:4px;background:rgba(14,159,159,.1);border-radius:4px;overflow:hidden;margin-bottom:18px;">
                    <div class="loading-progress-bar" style="height:100%;background:linear-gradient(90deg,#0e9f9f,#14c4b8);border-radius:4px;width:0%;animation:progressAnimate 2.5s ease-in-out infinite;"></div>
                </div>
                <div class="loading-dots" style="display:flex;gap:8px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#0e9f9f;opacity:.25;animation:loadDots 1.4s infinite ease-in-out;"></span>
                    <span style="width:8px;height:8px;border-radius:50%;background:#0e9f9f;opacity:.25;animation:loadDots 1.4s infinite ease-in-out;animation-delay:.2s;"></span>
                    <span style="width:8px;height:8px;border-radius:50%;background:#0e9f9f;opacity:.25;animation:loadDots 1.4s infinite ease-in-out;animation-delay:.4s;"></span>
                </div>
            </div>
        </div>
        <style>
            @keyframes loadingShimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
            @keyframes spinnerRotate{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
            @keyframes iconPulse{0%,100%{transform:translate(-50%,-50%) scale(1)}50%{transform:translate(-50%,-50%) scale(1.06)}}
            @keyframes progressAnimate{0%{width:0%;margin-left:0}50%{width:65%;margin-left:17%}100%{width:0%;margin-left:100%}}
            @keyframes loadDots{0%,80%,100%{opacity:.25;transform:scale(1)}40%{opacity:1;transform:scale(1.15)}}
            @keyframes successBounce{0%{transform:translate(-50%,-50%) scale(0)}50%{transform:translate(-50%,-50%) scale(1.2)}100%{transform:translate(-50%,-50%) scale(1)}}
            #loading.active{opacity:1}
            #loading.active .loading-box{transform:scale(1) translateY(0)}
            .loading-success .loading-icon-content{background:linear-gradient(135deg,#1aa179,#14b58c)!important;animation:successBounce 0.6s ease}
            .loading-success .loading-spinner-ring{border-top-color:#1aa179!important;animation:none!important;opacity:0}
            .loading-success .loading-ring-inner{animation:none!important;opacity:0}
            .loading-error .loading-icon-content{background:linear-gradient(135deg,#f2514c,#ff7269)!important}
            .loading-error .loading-spinner-ring{border-top-color:#f2514c!important}
        </style>
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
        <script src="<?= base_style() ?>/js/functions.js?v=2026080213"></script>
        <script src="<?= base_style() ?>/js/jquery-confirm.min.js"></script>
        <script src="<?= base_style() ?>/bookstores/parsleyjs/parsley.js"></script>
        <script src="<?= base_style() ?>/bookstores/gritter/js/jquery.gritter.min.js"></script>
        <script src="<?= base_style() ?>/js/functions/<?= $data['page_functions_js']; ?>?v=2026080213"></script>
    </body>
</html>
