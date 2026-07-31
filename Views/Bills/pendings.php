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
<div class="panel panel-default panel-pendings">
    <div class="panel-heading">
        <h4 class="panel-title">Lista de facturas</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand" data-original-title="" title="" data-init="true"><i class="fas fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()" data-original-title="" title="" data-init="true"><i class="fas fa-sync-alt"></i></a>
        </div>
    </div>
    <div class="panel-body border-panel">
        <div class="row">
            <div id="list-btns-exportable" style="display: none;">
              <?php if($_SESSION['userData']['profileid'] == ADMINISTRATOR){ ?>
              <div class="btn-group" id="btn-export">
                <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Exportar facturas" onclick="exports();"><i class="far fa-file-excel f-s-14"></i></button>
              </div>
              <?php }  ?>
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
