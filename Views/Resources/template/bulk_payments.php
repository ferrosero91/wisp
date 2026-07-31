<?php
  $client = $data['client'];
?>
<div class="panel panel-success panel-bills-pendings m-0" data-sortable="false">
  <div class="panel-heading">
    <h4 class="panel-title"><?= $client['names']." ".$client['surnames'] ?></h4>
  </div>
  <div class="panel-body border">
    <form id="transactions" autocomplete="off" name="transactions">
      <input type="hidden" id="idclient" name="idclient" value="<?= encrypt($client['id']) ?>">
      <div class="row">
        <div id="list-bills-pendings-btns-exportable" style="display: none;">
          <div class="btn-group">
            <button type="button" class="btn btn-white">
              <span class="badge label-warning f-s-10 f-w-700 mr-1"><?= $data['pending'] ?></span><span><?= $_SESSION['businessData']['symbol'].format_money($data['amount']) ?></span>
            </button>
          </div>
        </div>
        <div class="col-md-7" style="border-right: 1px solid #ddd;">
          <div class="table-responsive">
              <table id="list-bills-pendings" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width: 100%;">
                  <thead>
                      <tr>
                        <th>Nº Factura</th>
                        <th>Mes Fact.</th>
                        <th style="max-width: 70px !important; width: 70px;">Pendiente</th>
                        <th style="max-width: 60px !important; width: 60px;">Total</th>
                        <th>Emitido</th>
                        <th>Vencimiento</th>
                        <th class="all">Estado</th>
                      </tr>
                  </thead>
                  <tbody></tbody>
              </table>
          </div>
        </div>
        <div class="col-md-5">
          <div class="col-md-12 form-group mb-2">
            <style media="screen">
              .radio.radio-css.radio-inline + .radio-inline {
                margin-left: 0;
              }
            </style>
            <label>Cobrar facturas desde:</label>
            <br>
            <div class="radio radio-css radio-inline mr-4">
              <input type="radio" name="radio_option" id="radio_asc" value="ASC" checked="" data-parsley-multiple="radio_option">
              <label for="radio_asc" class="cursor-pointer f-s-14">Mas antiguo al actual.</label>
            </div>
            <div class="radio radio-css radio-inline mr-4">
              <input type="radio" name="radio_option" id="radio_desc" value="DESC" data-parsley-multiple="radio_option">
              <label for="radio_desc" class="cursor-pointer f-s-14">Actual al mas antiguo.</label>
            </div>
          </div>
          <div class="col-md-12 form-group pr-0">
            <label>Forma de pago</label>
            <select class="form-control" id="typepay" name="typepay" style="width:100%;"></select>
          </div>
          <div class="col-md-12 form-group pr-0">
            <label>Fecha</label>
            <div class="input-group">
              <?php $readonly = ($_SESSION['userData']['profileid'] == 1) ? "" : "readonly"; ?>
              <input type="text" class="form-control" id="date_time_mass" name="date_time" <?= $readonly ?>>
              <div class="input-group-append">
                <span class="input-group-text"><i class="far fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="col-md-12 form-group pr-0">
            <label>Comentario</label>
            <textarea class="form-control text-uppercase" name="comment" placeholder="INGRESE COMENTARIO" style="height: 50px"></textarea>
          </div>
          <div class="col-md-12 form-group pr-0">
            <label>TOTAL A PAGAR</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><b><?= $_SESSION['businessData']['symbol'] ?></b></span>
              </div>
              <input type="text" class="form-control f-s-19 f-w-700 text-center" id="total_pay" name="total_pay" onkeypress="return decimal(event)" onchange="validate_amount(<?= $data['amount'] ?>);" placeholder="0.00" pattern="^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$" value="">
            </div>
          </div>
          <div class="col-sm-12 m-t-10">
            <div class="form-group text-center">
              <button type="button" class="btn btn-white" onclick="$('#pending_invoices').slideUp().empty();">Cancelar</button>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Registrar pago</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
