<?php
  head($data);
  modal("clientsModal",$data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-clients">
  <div class="panel-heading">
    <h4 class="panel-title">Lista de clientes</h4>
    <div class="panel-heading-btn">
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div id="list-btns-exportable" style="display: none;">
      <?php if($_SESSION['userData']['profileid'] == ADMINISTRATOR){ ?>
      <?php if($_SESSION['permits_module']['r']){ ?>
        <div class="btn-group">
          <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Importar clientes" onclick="modal_import();"><i class="fas fa-upload"></i></button>
        </div>
      <?php } ?>
        <div class="btn-group">
          <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Exportar clientes" onclick="exports();"><i class="far fa-file-excel f-s-14"></i></button>
        </div>
      <?php } ?>
      </div>
      <div id="list-btns-tools" style="display: none;">
        <div class="options-group btn-group m-r-5">
        <?php if($_SESSION['userData']['profileid'] == ADMINISTRATOR){ ?>
        <?php if($_SESSION['permits_module']['r']){ ?>
          <button type="button" class="btn btn-white" onclick="add()"><i class="fas fa-plus mr-1"></i>Nuevo</button>
        <?php } ?>
        <?php } ?>
          <select class="form-control" id="filter_states" name="tblestado" onchange="filter_states();" style="width: 130px">
            <option value="0" selected>TODOS</option>
            <option value="1">INSTALACIÓN</option>
            <option value="2">ACTIVOS</option>
            <option value="3">SUSPENDIDOS</option>
            <option value="4">CANCELADOS</option>
            <option value="5">GRATIS</option>
          </select>
        </div>
      </div>
      <div class="col-md-12 col-sm-12 col-12">
        <div class="table-responsive">
          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
            <thead>
              <tr>
                <th style="max-width: 20px !important; width: 20px;">Id</th>
                <th>Cliente</th>
                <th>Documento</th>
                <th>Celular</th>
                <th>Coordenadas</th>
                <th>Deuda actual</th>
                <th>Dia pago</th>
                <th>Ultimo pago</th>
                <th>Proximo pago</th>
                <th>Plan</th>
                <th>F.Suspendido</th>
                <th>F.Cancelado</th>
                <th>Dirección</th>
                <th>Referencia</th>
                <th>Estado</th>
                <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
