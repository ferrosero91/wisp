<?php
  head($data);
  modal("billsModal",$data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-bills">
  <div class="panel-heading">
    <h4 class="panel-title">Lista de facturas</h4>
    <div class="panel-heading-btn">
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand" data-original-title="" title="" data-init="true"><i class="fas fa-expand"></i></a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()" data-original-title="" title="" data-init="true"><i class="fas fa-sync-alt"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-12">
        <div id="collapseview" class="box box-solid box-inverse collapse">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-filter mr-1"></i>Filtro Avanzado</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label>Fecha</label>
                  <div class="input-group input-daterange">
                    <input type="text" class="form-control" id="start">
                    <span class="input-group-addon">al</span>
                    <input type="text" class="form-control" id="end">
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-12">
                <div class="form-group">
                  <label>Estado</label>
                  <select class="form-control" id="listStates" style="width: 100%;">
                    <option value="0">TODAS</option>
                    <option value="1">PAGADAS</option>
                    <option value="2">PENDIENTES</option>
                    <option value="3">VENCIDAS</option>
                    <option value="4">ANULADAS</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-12">
                <div class="form-group">
                  <label class="text-white width-full">.</label>
                  <button type="button" class="btn btn-success" id="btn-search"> <i class="fa fa-search"></i> </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="list-btns-exportable" style="display: none;">
      <?php if($_SESSION['userData']['profileid'] == ADMINISTRATOR){ ?>
      <?php if($_SESSION['permits_module']['r']){ ?>
        <div class="btn-group">
          <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Importar facturas" onclick="modal_import();"><i class="fas fa-upload"></i></button>
        </div>
        <div class="btn-group">
          <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Generar facturas masivas" onclick="modal_debtOpening();"><i class="far fa-calendar-plus f-s-14"></i></button>
        </div>
      <?php } ?>
        <div class="btn-group" >
          <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Exportar facturas" onclick="exports();"><i class="far fa-file-excel f-s-14"></i></button>
        </div>
      <?php } ?>
      </div>
      <div id="list-btns-tools" style="display: none;">
        <div class="options-group btn-group m-r-5">
        <?php if($_SESSION['userData']['profileid'] != TECHNICAL || $_SESSION['userData']['profileid'] != CHARGES){ ?>
        <?php if($_SESSION['permits_module']['r']){ ?>
          <button type="button" class="btn btn-white" onclick="bill_free();"><i class="fas fa-plus mr-1"></i>Factura libre</button>
          <button type="button" class="btn btn-white" onclick="bill_services();"><i class="fas fa-plus mr-1"></i>Factura servicio</button>
        <?php } ?>
        <?php } ?>
          <button type="button" class="btn btn-white" data-toggle="collapse" href="#collapseview"><i class="fas fa-filter mr-1"></i>Filtro</button>
        </div>
      </div>
      <div class="col-md-12 col-sm-12 col-12">
        <div class="table-responsive">
          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width: 100%;">
            <thead>
              <tr>
                <th>Nº Factura</th>
                <th>Mes Fact.</th>
                <th>Cliente</th>
                <th>F.Emision</th>
                <th>F.Vencimiento</th>
                <th style="max-width: 60px !important; width: 60px;">Total</th>
                <th style="max-width: 70px !important; width: 70px;">Pendiente</th>
                <th style="max-width: 60px !important; width: 60px;">Subtotal</th>
                <th style="max-width: 70px !important; width: 70px;">Descuento</th>
                <th>Tipo</th>
                <th>F.Pago</th>
                <th>Forma pago</th>
                <th>Metodo</th>
                <th>Observación</th>
                <th class="all">Estado</th>
                <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          <div class="col-xl-12 p-0 m-t-20 invoice_summary" style="margin: 0 auto;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
