<?php
    head($data);
    $currencys = $data['options']['currencys'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row" data-sortable="false">
    <div class="col-sm-12" data-sortable="false">
        <div class="panel panel-inverse panel-with-tabs" data-sortable="false">
            <div class="panel-heading p-0">
                <div class="tab-overflow nav-ajax" style="width: 100%">
                    <ul class="nav nav-tabs nav-tabs-inverse">
                        <li class="nav-item"><a href="#general-tab" data-toggle="tab" class="nav-link active"><i class="fa fa-fw fa-lg fa-question-circle mr-1"></i><span class="d-none d-lg-inline">General</span></a></li>
                        <li class="nav-item"><a href="#invoice-tab" data-toggle="tab" class="nav-link"><i class="fas fa-fw fa-lg fa-file-alt mr-1"></i><span class="d-none d-lg-inline">Facturación</span></a></li>
                        <li class="nav-item"><a href="#electronic-tab" data-toggle="tab" class="nav-link"><i class="fas fa-fw fa-lg fa-file-invoice mr-1"></i><span class="d-none d-lg-inline">Fact. Electrónica</span></a></li>
                        <li class="nav-item"><a href="#resolutions-tab" data-toggle="tab" class="nav-link"><i class="fas fa-fw fa-lg fa-tasks mr-1"></i><span class="d-none d-lg-inline">Resoluciones</span></a></li>
                        <li class="nav-item"><a href="#api-tab" data-toggle="tab" class="nav-link"><i class="fas fa-fw fa-lg fa-cubes mr-1"></i><span class="d-none d-lg-inline">Apis</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body tab-content">
                <div class="tab-pane fade active show" id="general-tab">
                    <div id="accordion" class="accordion">
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true">
                            <i class="fa fa-building fa-fw mr-2"></i>Datos de la empresa
                          </div>
                          <div id="collapseOne" class="collapse show" data-parent="#accordion" style="">
                            <div class="card-body">
                              <form autocomplete="off" name="transactions_general" id="transactions_general" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Razon Social <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="business_name" id="business_name" onkeypress="return letters(event)" value="<?= $_SESSION['businessData']['business_name'] ?>" data-parsley-required="true">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Nombre Comercial</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="tradename" id="tradename" onkeypress="return numbersandletters(event)" value="<?= $_SESSION['businessData']['tradename'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">N° NIT <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="document" id="document" onkeypress="return numbers(event)" value="<?= $_SESSION['businessData']['ruc'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row">
                                          <label class="col-md-3 text-lg-right col-form-label">Celulares <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                                  <input type="text" class="form-control m-b-10" name="mobile" id="mobile" onkeypress="return numbers(event)" maxlength="10" data-parsley-required="true" value="<?= $_SESSION['businessData']['mobile'] ?>">
                                                  <input type="text" class="form-control" name="mobileReference" id="mobileReference" onkeypress="return numbers(event)" maxlength="10" value="<?= $_SESSION['businessData']['mobile_refrence'] ?>">
                                                  <small class="text-success text-uppercase m-b-10">Número telefonico opcional</small>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Dirección</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="address" id="address" value="<?= $_SESSION['businessData']['address'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Cambios
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false">
                            <i class="fa fa-cogs fa-fw mr-2"></i>Configuración basica
                          </div>
                          <div id="collapseTwo" class="collapse" data-parent="#accordion" style="">
                            <div class="card-body">
                              <form autocomplete="off" name="transactions_basic" id="transactions_basic" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Eslogan</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="slogan" id="slogan" onkeypress="return letters(event)" value="<?= $_SESSION['businessData']['slogan'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Departamento</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="department" id="department" value="<?= $_SESSION['businessData']['department'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Provincia</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="province" id="province" value="<?= $_SESSION['businessData']['province'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Distrito</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="district" id="district" value="<?= $_SESSION['businessData']['district'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Ubigeo</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="ubigeo" id="ubigeo" onkeypress="return numbers(event)" value="<?= $_SESSION['businessData']['ubigeo'] ?>">
                                              <small class="text-success text-uppercase m-b-10">Código de ubicación</small>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Código de pais</label>
                                          <div class="col-md-8">
                                              <select class="form-control" id="listCountry" name="listCountry">
                                                  <?= countrySelector($_SESSION['businessData']['country_code']) ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Cambios
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseThree">
                            <i class="fa fa-file-image fa-fw mr-2"></i>Logo de inicio de sesión
                          </div>
                          <div id="collapseThree" class="collapse" data-parent="#accordion">
                            <div class="card-body">
                              <form name="transactions_logo" id="transactions_logo" class="row">
                                  <?php
                                      if(!empty($_SESSION['businessData']['logo_login'])){
                                          if($_SESSION['businessData']['logo_login'] == "superwisp_white.png"){
                                              $logolog = base_style().'/images/logotypes/'.$_SESSION['businessData']['logo_login'];
                                          }else{
                                              $logolog_url = base_style().'/uploads/business/'.$_SESSION['businessData']['logo_login'];
                                              if(@getimagesize($logolog_url)){
                                                  $logolog = base_style().'/uploads/business/'.$_SESSION['businessData']['logo_login'];
                                              }else{
                                                  $logolog = base_style().'/images/logotypes/superwisp_white.png';
                                              }
                                          }
                                      }else{
                                          $logolog = base_style().'/images/logotypes/superwisp_white.png';
                                      }
                                  ?>
                                  <input type="hidden" id="logo-actual" name="logo-actual" value="<?= $_SESSION['businessData']['logo_login'] ?>">
                                  <div class="col-md-12 col-sm-12 col-12 text-center">
                                      <div class="image">
                                          <div class="cont-image">
                                              <label for="logo"></label>
                                              <div class="prev-image">
                                                  <img class="img-responsive" id="image-logo" src="<?= $logolog ?>">
                                              </div>
                                          </div>
                                          <div class="upload-image">
                                              <input type="file" name="logo" id="logo">
                                          </div>
                                      </div>
                                      <small class="text-success text-uppercase m-b-10">Max. 210 KB</small>
                                  </div>
                                  <div class="col-md-12 col-sm-12 col-12 text-center mt-2">
                                      <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-2"></i>Subir logo</button>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseFour">
                            <i class="fab fa-fly fa-fw mr-2"></i>Favicon
                          </div>
                          <div id="collapseFour" class="collapse" data-parent="#accordion">
                            <div class="card-body">
                              <form name="transactions_favicon" id="transactions_favicon" class="row">
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
                                  <input type="hidden" id="fa-actual" name="fa-actual" value="<?= $_SESSION['businessData']['favicon'] ?>">
                                  <div class="col-md-12 col-sm-12 col-12 text-center">
                                      <div class="favicon">
                                          <div class="cont-favicon">
                                              <label for="favicon"></label>
                                              <div class="prev-favicon">
                                                  <img class="img-responsive" id="image-favicon" src="<?= $favicon  ?>">
                                              </div>
                                          </div>
                                          <div class="upload-image">
                                              <input type="file" name="favicon" id="favicon">
                                          </div>
                                      </div>
                                      <small class="text-success text-uppercase m-b-10">Max. 160 KB</small>
                                  </div>
                                  <div class="col-md-12 col-sm-12 col-12 text-center mt-2">
                                      <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-2"></i>Subir favicon</button>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseFive">
                            <i class="fas fa-image fa-fw mr-2"></i>Fondo de inicio de sesión
                          </div>
                          <div id="collapseFive" class="collapse" data-parent="#accordion">
                            <div class="card-body">
                              <form name="transactions_background" id="transactions_background" class="row">
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_1" type="radio" name="background" value="bg-1.jpeg" <?php if($_SESSION['businessData']['background']== "bg-1.jpeg"){echo 'checked';}?>>
                                      <label for="select_1" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-1.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_2" type="radio" name="background" value="bg-2.jpeg" <?php if($_SESSION['businessData']['background']== "bg-2.jpeg"){echo 'checked';}?>>
                                      <label for="select_2" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-2.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_3" type="radio" name="background" value="bg-3.jpeg" <?php if($_SESSION['businessData']['background']== "bg-3.jpeg"){echo 'checked';}?>>
                                      <label for="select_3" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-3.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_4" type="radio" name="background" value="bg-4.jpeg" <?php if($_SESSION['businessData']['background']== "bg-4.jpeg"){echo 'checked';}?>>
                                      <label for="select_4" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-4.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_5" type="radio" name="background" value="bg-5.jpeg" <?php if($_SESSION['businessData']['background']== "bg-5.jpeg"){echo 'checked';}?>>
                                      <label for="select_5" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-5.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_6" type="radio" name="background" value="bg-6.jpeg" <?php if($_SESSION['businessData']['background']== "bg-6.jpeg"){echo 'checked';}?>>
                                      <label for="select_6" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-6.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_7" type="radio" name="background" value="bg-7.jpeg" <?php if($_SESSION['businessData']['background']== "bg-7.jpeg"){echo 'checked';}?>>
                                      <label for="select_7" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-7.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_8" type="radio" name="background" value="bg-8.jpeg" <?php if($_SESSION['businessData']['background']== "bg-8.jpeg"){echo 'checked';}?>>
                                      <label for="select_8" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-8.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_9" type="radio" name="background" value="bg-9.jpeg" <?php if($_SESSION['businessData']['background']== "bg-9.jpeg"){echo 'checked';}?>>
                                      <label for="select_9" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-9.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_10" type="radio" name="background" value="bg-10.jpeg" <?php if($_SESSION['businessData']['background']== "bg-10.jpeg"){echo 'checked';}?>>
                                      <label for="select_10" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-10.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_11" type="radio" name="background" value="bg-11.jpeg" <?php if($_SESSION['businessData']['background']== "bg-11.jpeg"){echo 'checked';}?>>
                                      <label for="select_11" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-11.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-6 col-md-4 col-lg-2">
                                  <span class="bmd-form-group is-filled">
                                    <div class="radio radio-css radio-inline">
                                      <input id="select_12" type="radio" name="background" value="bg-12.jpeg" <?php if($_SESSION['businessData']['background']== "bg-12.jpeg"){echo 'checked';}?>>
                                      <label for="select_12" class="cursor-pointer">
                                        <img src="<?= base_style() ?>/images/background/bg-12.jpeg" class="img-fluid img-avatar-form">
                                      </label>
                                    </div>
                                  </span>
                                </div>
                                <div class="col-12 col-md-12 col-lg-12 text-center mt-2">
                                  <button type="submit" class="btn btn-blue">
                                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                                  </button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="invoice-tab">
                    <div id="accordionTwo" class="accordion">
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseSix">
                            <i class="fa fa-cogs fa-fw mr-2"></i>Configuración de facturación
                          </div>
                          <div id="collapseSix" class="collapse show" data-parent="#accordionTwo">
                            <div class="card-body">
                              <form autocomplete="off" name="transactions_invoice" id="transactions_invoice" class="row row-space-30">
                                  <div class="col-xl-12">
                                    <div class="form-group row m-b-10">
                                        <label class="col-md-3 text-lg-right col-form-label">Moneda</label>
                                        <div class="col-md-8">
                                            <select class="form-control" id="listCurrency" name="listCurrency">
                                                <?php
                                                  foreach ($currencys as $currency) {
                                                ?>
                                                  <option value="<?= $currency['id'] ?>" <?= (($currency['id']== $_SESSION['businessData']['currencyid'])?"selected":"") ?>><?= $currency['currency_iso'].' - '.$currency['currency_name'].' - '.$currency['symbol'] ?></option>
                                                <?php } ?>
                                             </select>
                                        </div>
                                    </div>
                                    <div class="form-group row m-b-10">
                                        <label class="col-md-3 text-lg-right col-form-label">Formato de impresión</label>
                                        <div class="col-md-8">
                                            <select class="form-control" id="listPrinters" name="listPrinters">
                                                <option value="ticket" <?= (($_SESSION['businessData']['print_format'] == "ticket")?"selected":"") ?>>Ticket</option>
                                                <option value="a4" <?= (($_SESSION['businessData']['print_format'] == "a4")?"selected":"") ?>>A4</option>
                                            </select>
                                        </div>
                                    </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Texto del pie de pagina</label>
                                          <div class="col-md-8">
                                              <textarea class="form-control" id="footer_text" name="footer_text">
                                                <?= $_SESSION['businessData']['footer_text'] ?>
                                              </textarea>
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Cambios
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseSeven">
                            <i class="fa fa-file-image fa-fw mr-2"></i>Logo factura
                          </div>
                          <div id="collapseSeven" class="collapse" data-parent="#accordionTwo">
                            <div class="card-body">
                              <form name="transactions_logofac" id="transactions_logofac" class="row">
                                  <?php
                                      if(!empty($_SESSION['businessData']['logotyope'])){
                                          if($_SESSION['businessData']['logotyope'] == "superwisp.png"){
                                              $logofac = base_style().'/images/logotypes/'.$_SESSION['businessData']['logotyope'];
                                          }else{
                                              $logofac_url = base_style().'/uploads/business/'.$_SESSION['businessData']['logotyope'];
                                              if(@getimagesize($logofac_url)){
                                                  $logofac = base_style().'/uploads/business/'.$_SESSION['businessData']['logotyope'];
                                              }else{
                                                  $logofac = base_style().'/images/logotypes/superwisp.png';
                                              }
                                          }
                                      }else{
                                          $logofac = base_style().'/images/logotypes/superwisp.png';
                                      }
                                  ?>
                                  <input type="hidden" id="logfac-actual" name="logfac-actual" value="<?= $_SESSION['businessData']['logotyope'] ?>">
                                  <div class="col-md-12 col-sm-12 col-12 text-center">
                                      <div class="image">
                                          <div class="cont-image">
                                              <label for="logo-fac"></label>
                                              <div class="prev-image">
                                                  <img class="img-responsive" id="image-logofac" src="<?= $logofac ?>">
                                              </div>
                                          </div>
                                          <div class="upload-image">
                                              <input type="file" name="logo-fac" id="logo-fac">
                                          </div>
                                      </div>
                                      <small class="text-success text-uppercase m-b-10">Max. 210 KB</small>
                                  </div>
                                  <div class="col-md-12 col-sm-12 col-12 text-center mt-2">
                                      <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-2"></i>Subir logo</button>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="electronic-tab">
                    <div id="accordionElectronic" class="accordion">
                        <!-- CONEXION API -->
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapseApidian" aria-expanded="true">
                            <i class="fas fa-plug fa-fw mr-2"></i>Conexión API APIDIAN
                          </div>
                          <div id="collapseApidian" class="collapse show" data-parent="#accordionElectronic">
                            <div class="card-body">
                              <div class="alert alert-info">
                                  <i class="fas fa-info-circle mr-2"></i>Configure los datos de conexión con la API de APIDIAN. El token se obtiene al configurar la empresa en la sección inferior.
                              </div>
                              <form autocomplete="off" name="transactions_apidian_connection" id="transactions_apidian_connection" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">URL API APIDIAN <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="apidian_url" id="apidian_url" value="<?= $_SESSION['businessData']['apidian_url'] ?? '' ?>" placeholder="https://tu-dominio.com/api/ubl2.1">
                                              <small class="text-muted">URL base de la API APIDIAN (sin slash final)</small>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Token API <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <div class="input-group">
                                                  <input type="password" class="form-control" name="apidian_token" id="apidian_token" value="<?= $_SESSION['businessData']['apidian_token'] ?? '' ?>">
                                                  <div class="input-group-append">
                                                      <button class="btn btn-outline-secondary" type="button" onclick="toggleToken()"><i class="fa fa-eye"></i></button>
                                                  </div>
                                              </div>
                                              <small class="text-muted">Token de autenticación (se genera al configurar empresa)</small>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Ambiente <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <select class="form-control" name="apidian_environment" id="apidian_environment">
                                                  <option value="habilitacion" <?= (($_SESSION['businessData']['apidian_environment'] ?? '') == 'habilitacion') ? 'selected' : '' ?>>Habilitación (Pruebas)</option>
                                                  <option value="produccion" <?= (($_SESSION['businessData']['apidian_environment'] ?? '') == 'produccion') ? 'selected' : '' ?>>Producción</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Conexión
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <!-- IMPUESTOS -->
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseTaxes">
                            <i class="fas fa-percentage fa-fw mr-2"></i>Configuración de Impuestos
                          </div>
                          <div id="collapseTaxes" class="collapse" data-parent="#accordionElectronic">
                            <div class="card-body">
                              <form autocomplete="off" name="transactions_taxes" id="transactions_taxes" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Tasa de Impuesto <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <select class="form-control" name="tax_rate" id="tax_rate">
                                                  <option value="19.00" <?= (($_SESSION['businessData']['tax_rate'] ?? 19) == 19.00) ? 'selected' : '' ?>>IVA 19%</option>
                                                  <option value="5.00" <?= (($_SESSION['businessData']['tax_rate'] ?? 0) == 5.00) ? 'selected' : '' ?>>IVA 5%</option>
                                                  <option value="0.00" <?= (($_SESSION['businessData']['tax_rate'] ?? 0) == 0.00) ? 'selected' : '' ?>>Exento (0%)</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Nombre Impuesto</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="tax_name" id="tax_name" value="<?= $_SESSION['businessData']['tax_name'] ?? 'IVA' ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Impuestos
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <!-- CERTIFICADO DIGITAL -->
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseCertificate">
                            <i class="fas fa-certificate fa-fw mr-2"></i>Certificado Digital
                          </div>
                          <div id="collapseCertificate" class="collapse" data-parent="#accordionElectronic">
                            <div class="card-body">
                              <?php if(($_SESSION['businessData']['apidian_configured'] ?? 0) == 0){ ?>
                              <div class="alert alert-warning">
                                  <i class="fas fa-exclamation-triangle mr-2"></i>Primero debe registrar la empresa en APIDIAN.
                              </div>
                              <?php } else { ?>
                              <?php if(($_SESSION['businessData']['apidian_certificate_configured'] ?? 0) == 1){ ?>
                              <div class="alert alert-info">
                                  <i class="fas fa-info-circle mr-2"></i>Certificado configurado. Días restantes: <strong><?= $_SESSION['businessData']['apidian_certificate_days_left'] ?? 'N/A' ?></strong>
                              </div>
                              <?php } ?>
                              <form autocomplete="off" name="transactions_certificate" id="transactions_certificate" class="row row-space-30" enctype="multipart/form-data">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Certificado (.p12/.pfx) <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <input type="file" class="form-control" name="certificate_file" id="certificate_file" accept=".p12,.pfx">
                                              <small class="text-muted">Archivo de certificado digital PKCS12 (.p12 o .pfx)</small>
                                              <?php if(!empty($_SESSION['businessData']['apidian_certificate_file'])){ ?>
                                              <small class="text-success d-block"><i class="fa fa-check mr-1"></i>Archivo actual: <?= $_SESSION['businessData']['apidian_certificate_file'] ?></small>
                                              <?php } ?>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Contraseña <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <input type="password" class="form-control" name="certificate_password" id="certificate_password">
                                              <small class="text-muted">Contraseña del certificado proporcionada por el proveedor</small>
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-upload mr-2"></i>Configurar Certificado
                                          </button>
                                      </div>
                                  </div>
                              </form>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                        <!-- CONFIGURAR SOFTWARE -->
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseSoftware">
                            <i class="fas fa-code fa-fw mr-2"></i>Configurar Software DIAN
                          </div>
                          <div id="collapseSoftware" class="collapse" data-parent="#accordionElectronic">
                            <div class="card-body">
                              <?php if(($_SESSION['businessData']['apidian_configured'] ?? 0) == 0){ ?>
                              <div class="alert alert-warning">
                                  <i class="fas fa-exclamation-triangle mr-2"></i>Primero debe registrar la empresa en APIDIAN.
                              </div>
                              <?php } else { ?>
                              <?php if(($_SESSION['businessData']['apidian_software_configured'] ?? 0) == 1){ ?>
                              <div class="alert alert-success">
                                  <i class="fas fa-check-circle mr-2"></i>Software configurado correctamente.
                              </div>
                              <?php } ?>
                              <div class="alert alert-info">
                                  <i class="fas fa-info-circle mr-2"></i>Ingrese el ID y PIN del software propio registrado en la DIAN.
                              </div>
                              <form autocomplete="off" name="transactions_software" id="transactions_software" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">ID Software <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="software_id" id="software_id" value="<?= $_SESSION['businessData']['apidian_software_id'] ?? '' ?>" placeholder="82bf0c5e-0117-434d-9471-8a5ee58ae682">
                                              <small class="text-muted">ID del software entregado por la DIAN</small>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">PIN Software <span class="text-danger">*</span></label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="software_pin" id="software_pin" value="<?= $_SESSION['businessData']['apidian_software_pin'] ?? '' ?>" maxlength="5" onkeypress="return numbers(event)" placeholder="12345">
                                              <small class="text-muted">PIN de 5 dígitos del software propio</small>
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Configurar Software
                                          </button>
                                      </div>
                                  </div>
                              </form>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                        <!-- CONFIGURAR EMPRESA EN APIDIAN -->
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse" data-target="#collapseConfigApidian">
                            <i class="fas fa-building fa-fw mr-2"></i>Registrar Empresa en APIDIAN
                          </div>
                          <div id="collapseConfigApidian" class="collapse" data-parent="#accordionElectronic">
                            <div class="card-body">
                              <div class="alert alert-warning">
                                  <i class="fas fa-exclamation-triangle mr-2"></i><strong>Importante:</strong> Este proceso registra la empresa en la API de APIDIAN y genera el Token de autenticación. Solo debe ejecutarse <strong>una vez</strong>. Si ya configuró la empresa, no vuelva a ejecutar.
                              </div>
                              <?php if(($_SESSION['businessData']['apidian_configured'] ?? 0) == 1){ ?>
                              <div class="alert alert-success">
                                  <i class="fas fa-check-circle mr-2"></i><strong>Empresa ya configurada en APIDIAN.</strong> Los datos se muestran a continuación.
                              </div>
                              <?php } ?>
                              <form autocomplete="off" name="transactions_config_apidian" id="transactions_config_apidian" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">NIT</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="config_nit" id="config_nit" value="<?= $_SESSION['businessData']['apidian_nit'] ?? $_SESSION['businessData']['ruc'] ?>" readonly>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Dígito Verificación</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="config_dv" id="config_dv" value="<?= $_SESSION['businessData']['apidian_dv'] ?? '' ?>" maxlength="1">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Tipo Documento</label>
                                          <div class="col-md-8">
                                              <select class="form-control" name="config_type_doc" id="config_type_doc">
                                                  <?php foreach($data['dian_type_documents'] as $doc){ ?>
                                                  <option value="<?= $doc['id'] ?>" <?= ($doc['id'] == ($_SESSION['businessData']['apidian_type_doc'] ?? 3)) ? 'selected' : '' ?>><?= $doc['name'] ?> (<?= $doc['code'] ?>)</option>
                                                  <?php } ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Tipo Organización</label>
                                          <div class="col-md-8">
                                              <select class="form-control" name="config_type_org" id="config_type_org">
                                                  <?php foreach($data['dian_type_organizations'] as $org){ ?>
                                                  <option value="<?= $org['id'] ?>" <?= ($org['id'] == ($_SESSION['businessData']['apidian_type_org'] ?? 2)) ? 'selected' : '' ?>><?= $org['name'] ?></option>
                                                  <?php } ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Tipo Régimen</label>
                                          <div class="col-md-8">
                                              <select class="form-control" name="config_type_regime" id="config_type_regime">
                                                  <?php foreach($data['dian_type_regimes'] as $regime){ ?>
                                                  <option value="<?= $regime['id'] ?>" <?= ($regime['id'] == ($_SESSION['businessData']['apidian_type_regime'] ?? 2)) ? 'selected' : '' ?>><?= $regime['name'] ?></option>
                                                  <?php } ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Responsabilidad Tributaria</label>
                                          <div class="col-md-8">
                                              <select class="form-control" name="config_type_liability" id="config_type_liability">
                                                  <?php foreach($data['dian_type_liabilities'] as $liability){ ?>
                                                  <option value="<?= $liability['id'] ?>" <?= ($liability['id'] == ($_SESSION['businessData']['apidian_type_liability'] ?? 117)) ? 'selected' : '' ?>><?= $liability['code'] ?> - <?= $liability['name'] ?></option>
                                                  <?php } ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Razón Social</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="config_business_name" id="config_business_name" value="<?= $_SESSION['businessData']['apidian_business_name'] ?? $_SESSION['businessData']['business_name'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Matrícula Mercantil</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="config_merchant" id="config_merchant" value="<?= $_SESSION['businessData']['apidian_merchant'] ?? '0000000-00' ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Municipio</label>
                                          <div class="col-md-8">
                                              <select class="form-control select2" name="config_municipality" id="config_municipality">
                                                  <?php foreach($data['dian_municipalities'] as $mun){ ?>
                                                  <option value="<?= $mun['id'] ?>" <?= ($mun['id'] == ($_SESSION['businessData']['apidian_municipality_id'] ?? 0)) ? 'selected' : '' ?>><?= $mun['name'] ?> (<?= $mun['code'] ?>)</option>
                                                  <?php } ?>
                                              </select>
                                              <small class="text-muted">Seleccione el municipio según tablas DIAN</small>
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Dirección</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control text-uppercase" name="config_address" id="config_address" value="<?= $_SESSION['businessData']['apidian_address'] ?? $_SESSION['businessData']['address'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Teléfono</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="config_phone" id="config_phone" value="<?= $_SESSION['businessData']['apidian_phone'] ?? $_SESSION['businessData']['mobile'] ?>" onkeypress="return numbers(event)">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Email</label>
                                          <div class="col-md-8">
                                              <input type="email" class="form-control" name="config_email" id="config_email" value="<?= $_SESSION['businessData']['apidian_email'] ?? $_SESSION['businessData']['email'] ?>">
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-warning" id="btn-config-apidian">
                                              <i class="fas fa-plug mr-2"></i>Registrar Empresa en APIDIAN
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <!-- PESTAÑA RESOLUCIONES -->
                <div class="tab-pane fade" id="resolutions-tab">
                    <div id="accordionResolutions" class="accordion">
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapseResolutions" aria-expanded="true">
                            <i class="fas fa-tasks fa-fw mr-2"></i>Resoluciones de Facturación Electrónica
                          </div>
                          <div id="collapseResolutions" class="collapse show" data-parent="#accordionResolutions">
                            <div class="card-body">
                              <div class="row mb-3">
                                  <div class="col-md-12">
                                      <button type="button" class="btn btn-primary" onclick="showAddResolution()">
                                          <i class="fas fa-plus mr-2"></i>Nueva Resolución
                                      </button>
                                  </div>
                              </div>
                              <div id="form-resolution" style="display:none;">
                                  <form autocomplete="off" name="transactions_resolution" id="transactions_resolution" class="row row-space-30 border p-3 mb-3">
                                      <div class="col-xl-12">
                                          <h5 class="mb-3">Datos de la Resolución</h5>
                                          <input type="hidden" name="resolution_id" id="resolution_id" value="0">
                                          <div class="form-group row m-b-10">
                                              <label class="col-md-3 text-lg-right col-form-label">Tipo Documento <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <select class="form-control" name="resolution_type_doc" id="resolution_type_doc" onchange="toggleResolutionFields()">
                                                      <option value="1">Factura Electrónica</option>
                                                      <option value="4">Nota Crédito</option>
                                                      <option value="5">Nota Débito</option>
                                                      <option value="11">Documento Soporte</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10">
                                              <label class="col-md-3 text-lg-right col-form-label">Prefijo <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <input type="text" class="form-control" name="resolution_prefix" id="resolution_prefix" placeholder="SETP">
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10" id="group_resolution_number">
                                              <label class="col-md-3 text-lg-right col-form-label">N° Resolución <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <input type="text" class="form-control" name="resolution_number" id="resolution_number">
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10" id="group_resolution_date">
                                              <label class="col-md-3 text-lg-right col-form-label">Fecha Resolución <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <input type="date" class="form-control" name="resolution_date" id="resolution_date">
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10" id="group_resolution_date_from">
                                              <label class="col-md-3 text-lg-right col-form-label">Vigencia Desde <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <input type="date" class="form-control" name="resolution_date_from" id="resolution_date_from">
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10" id="group_resolution_date_to">
                                              <label class="col-md-3 text-lg-right col-form-label">Vigencia Hasta <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <input type="date" class="form-control" name="resolution_date_to" id="resolution_date_to">
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10">
                                              <label class="col-md-3 text-lg-right col-form-label">Consecutivo Desde <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <input type="number" class="form-control" name="resolution_from" id="resolution_from" value="1">
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10">
                                              <label class="col-md-3 text-lg-right col-form-label">Consecutivo Hasta <span class="text-danger">*</span></label>
                                              <div class="col-md-8">
                                                  <input type="number" class="form-control" name="resolution_to" id="resolution_to" value="999999999">
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10">
                                              <label class="col-md-3 text-lg-right col-form-label">Consecutivo Actual</label>
                                              <div class="col-md-8">
                                                  <input type="number" class="form-control" name="resolution_current" id="resolution_current" value="0">
                                                  <small class="text-muted">Siguiente número a usar (0 = empezar desde "Desde")</small>
                                              </div>
                                          </div>
                                          <div class="form-group row m-b-10" id="group_technical_key">
                                              <label class="col-md-3 text-lg-right col-form-label">Clave Técnica</label>
                                              <div class="col-md-8">
                                                  <input type="text" class="form-control" name="resolution_technical_key" id="resolution_technical_key" placeholder="Clave técnica DIAN (solo facturas)">
                                                  <small class="text-muted">Requerida solo para Factura Electrónica</small>
                                              </div>
                                          </div>
                                          <div class="form-group row justify-content-center">
                                              <button type="button" class="btn btn-secondary mr-2" onclick="cancelResolution()">Cancelar</button>
                                              <button type="submit" class="btn btn-blue">
                                                  <i class="fas fa-save mr-2"></i>Guardar Local
                                              </button>
                                              <button type="button" class="btn btn-success ml-2" onclick="sendResolutionToApidian()">
                                                  <i class="fas fa-cloud-upload-alt mr-2"></i>Enviar a APIDIAN
                                              </button>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                                <div class="table-responsive">
                                  <table class="table table-sm table-hover table-striped" id="table-resolutions">
                                      <thead>
                                          <tr>
                                              <th>Tipo</th>
                                              <th>Prefijo</th>
                                              <th>Resolución</th>
                                              <th>Desde</th>
                                              <th>Hasta</th>
                                              <th>Actual</th>
                                              <th>Vigencia</th>
                                              <th>Estado</th>
                                              <th>Acciones</th>
                                          </tr>
                                      </thead>
                                      <tbody id="list-resolutions">
                                          <!-- Se llena por AJAX -->
                                      </tbody>
                                  </table>
                              </div>
                              <div id="no-resolutions" class="text-center text-muted p-4" style="display:none;">
                                  <i class="fas fa-inbox fa-3x mb-3"></i>
                                  <p>No hay resoluciones configuradas</p>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="api-tab">
                    <div id="accordionTree" class="accordion">
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapseEigth" aria-expanded="false">
                            <img src="<?= base_style() ?>/images/default/googlemaps.png" class="image-apis mr-1">Google Maps
                          </div>
                          <div id="collapseEigth" class="collapse" data-parent="#accordionTree" style="">
                            <div class="card-body">
                              <form autocomplete="off" name="transactions_google" id="transactions_google" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Clave API google maps</label>
                                          <div class="col-md-8">
                                              <input type="text" class="form-control" value="<?= $_SESSION['businessData']['google_apikey'] ?>" name="google_apikey" id="google_apikey">
                                              <small>Para obtener su Clave API Google visite: <a href="https://console.cloud.google.com/freetrial?hl=es&amp;page=0" target="_blank">Obtén una clave para API para JavaScript</a> </small>
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Cambios
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapseNine" aria-expanded="false">
                            <img src="<?= base_style() ?>/images/default/reniec.png" class="image-apis mr-1">Reniec
                          </div>
                          <div id="collapseNine" class="collapse" data-parent="#accordionTree" style="">
                            <div class="card-body">
                              <form autocomplete="off" name="transactions_reniec" id="transactions_reniec" class="row row-space-30">
                                  <div class="col-xl-12">
                                    <div class="form-group row m-b-10">
                                        <label class="col-md-3 text-lg-right col-form-label">Clave API</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="<?= $_SESSION['businessData']['reniec_apikey'] ?>" name="reniec_apikey" id="reniec_apikey">
                                            <small>Para obtener su Token visite: <a href="https://apiperu.dev" target="_blank">Obtén tu token</a></small>
                                        </div>
                                    </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Cambios
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapseTwelve" aria-expanded="false">
                            <img src="<?= base_style() ?>/images/default/phpmailer.jpg" class="image-apis mr-1">PHP Mailer
                          </div>
                          <div id="collapseTwelve" class="collapse" data-parent="#accordionTree" style="">
                            <div class="card-body">
                              <form autocomplete="off" name="transactions_email" id="transactions_email" class="row row-space-30">
                                  <div class="col-xl-12">
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Correo</label>
                                          <div class="col-md-8">
                                            <input type="text" class="form-control" onkeypress="return mail(event)" value="<?= $_SESSION['businessData']['email'] ?>" name="email" id="email">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Contraseña</label>
                                          <div class="col-md-8">
                                            <input type="password" class="form-control" value="<?= $_SESSION['businessData']['password'] ?>" name="password" id="password">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Host/Servidor</label>
                                          <div class="col-md-8">
                                            <input type="text" class="form-control" value="<?= $_SESSION['businessData']['server_host'] ?>" name="server_host" id="server_host">
                                          </div>
                                      </div>
                                      <div class="form-group row m-b-10">
                                          <label class="col-md-3 text-lg-right col-form-label">Puerto</label>
                                          <div class="col-md-8">
                                            <select class="form-control" name="port" id="port">
                                              <option value="465" <?php if($_SESSION['businessData']['port']==465){echo 'checked';}?>>465</option>
                                              <option value="587" <?php if($_SESSION['businessData']['port']==587){echo 'checked';}?>>587</option>
                                            </select>
                                          </div>
                                      </div>
                                      <div class="form-group row">
                                          <label class="col-md-3 text-lg-right col-form-label">Logo correo</label>
                                          <div class="col-md-8">
                                            <input type="text" class="form-control" value="<?= $_SESSION['businessData']['logo_email'] ?>" name="logo_email" id="logo_email">
                                          </div>
                                      </div>
                                      <div class="form-group row justify-content-center">
                                          <button type="submit" class="btn btn-blue">
                                              <i class="fas fa-save mr-2"></i>Guardar Cambios
                                          </button>
                                      </div>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
