<div id="header" class="header navbar-default">
    <div class="navbar-header">
        <a href="<?= base_url() ?>/dashboard" class="navbar-brand">
            <?php
                if(!empty($_SESSION['businessData']['logotyope'])){
                    if($_SESSION['businessData']['logotyope'] == "superwisp.png"){
                        $logo = base_style().'/images/logotypes/'.$_SESSION['businessData']['logotyope'];
                    }else{
                        $logofac_url = base_style().'/uploads/business/'.$_SESSION['businessData']['logotyope'];
                        if(@getimagesize($logofac_url)){
                            $logo = base_style().'/uploads/business/'.$_SESSION['businessData']['logotyope'];
                        }else{
                            $logo = base_style().'/images/logotypes/superwisp.png';
                        }
                    }
                }else{
                    $logo = base_style().'/images/logotypes/superwisp.png';
                }
            ?>
            <img src="<?= $logo ?>" id="mainlogo" class="img-responsive" style="max-width:250px; height:auto">
        </a>
        <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
    <ul class="navbar-nav navbar-right">
      <?php if(!empty($_SESSION['permits'][PAYMENTS]['r'])){ ?>
        <li>
          <a href="<?= base_url() ?>/payments/add_payment" class="f-s-12" style="padding: 15px 8px;" data-toggle="tooltip" data-original-title="Registrar pago">
            <i class="fa fa-dollar-sign f-s-18 mr-1"></i>
          </a>
        </li>
      <?php } ?>
        <li class="dropdown navbar-user">
            <a href="javascript:;" class="dropdown-toggle" style="padding: 15px 10px;" data-toggle="dropdown">
                <?php
                    if(!empty($_SESSION['userData']['image'])){
                        if($_SESSION['userData']['image'] == "user_default.png"){
                            $image = base_style().'/images/default/user_default.png';
                        }else{
                            $url = base_style().'/uploads/users/'.$_SESSION['userData']['image'];
                            if(@getimagesize($url)){
                                $image = $url;
                            }else{
                                $image = base_style().'/images/default/user_default.png';
                            }
                        }
                    }else{
                        $image = base_style().'/images/default/user_default.png';
                    }
                ?>
                <img src="<?= $image ?>" alt="<?= $_SESSION['userData']['names'] ?>" style="border-radius:50%; border:2px solid #e4e9f0; width:32px; height:32px; object-fit:cover;">
                <span class="d-none d-md-inline f-w-600" style="color:#556477;"><?= $_SESSION['userData']['names'] ?></span> <b class="caret"></b>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?= base_url(); ?>/profile" class="dropdown-item"><i class="far fa-user mr-2"></i>Mi cuenta</a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url(); ?>/logout" class="dropdown-item text-danger"><i class="fa fa-sign-out-alt mr-2"></i>Cerrar Sesión</a>
            </div>
        </li>
    </ul>
    <!-- end header-nav -->
</div>
