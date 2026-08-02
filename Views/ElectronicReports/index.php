<?php
  head($data);
?>
<style>
    /* Estilos personalizados para el módulo de reportes electrónicos */
    .widget-stats {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        overflow: hidden;
    }
    .widget-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    }
    .widget-stats .stats-icon {
        opacity: 0.15;
        font-size: 100px;
        position: absolute;
        right: 15px;
        top: 10px;
    }
    .widget-stats .stats-info h4 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    .widget-stats .stats-info p {
        font-size: 13px;
        opacity: 0.9;
        margin: 5px 0 0;
        font-weight: 500;
    }
    .widget-stats .stats-link {
        border-top: 1px solid rgba(255,255,255,0.2);
        padding: 8px 15px;
        margin-top: 10px;
    }
    .widget-stats .stats-link a {
        color: rgba(255,255,255,0.9);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }
    .bg-blue { background: linear-gradient(135deg, #348fe2 0%, #2b7bd3 100%); }
    .bg-green { background: linear-gradient(135deg, #00acac 0%, #009a9a 100%); }
    .bg-orange { background: linear-gradient(135deg, #f59c1a 0%, #e08e0e 100%); }
    .bg-red { background: linear-gradient(135deg, #ff5b57 0%, #e04a46 100%); }
    .bg-teal { background: linear-gradient(135deg, #00897b 0%, #00796b 100%); }
    .bg-purple { background: linear-gradient(135deg, #727cb6 0%, #636da5 100%); }
    .bg-dark { background: linear-gradient(135deg, #2d353c 0%, #1a252f 100%); }
    
    /* Panel de la tabla */
    .panel-electronic-reports {
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #e4e9f0;
    }
    .panel-electronic-reports .panel-heading {
        background: #fff;
        border-bottom: 1px solid #e4e9f0;
        border-radius: 12px 12px 0 0;
        padding: 15px 20px;
    }
    .panel-electronic-reports .panel-title {
        font-weight: 700;
        color: #2d3a48;
        font-size: 16px;
    }
    
    /* Badges de estado - Fondos sólidos con texto blanco */
    .label {
        padding: 5px 12px !important;
        border-radius: 6px !important;
        font-weight: 700 !important;
        font-size: 11px !important;
        display: inline-block !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    /* Estados */
    .label-success { background: #00acac !important; color: #ffffff !important; }
    .label-warning { background: #f59c1a !important; color: #ffffff !important; }
    .label-danger { background: #ff5b57 !important; color: #ffffff !important; }
    .label-info { background: #348fe2 !important; color: #ffffff !important; }
    .label-default { background: #6c757d !important; color: #ffffff !important; }
    .label-dark { background: #2d353c !important; color: #ffffff !important; }
    .label-purple { background: #727cb6 !important; color: #ffffff !important; }
    
    /* Badges de tipo de documento */
    .badge-invoice { background: linear-gradient(135deg, #00acac, #009a9a) !important; color: #ffffff !important; }
    .badge-credit { background: linear-gradient(135deg, #f59c1a, #e08e0e) !important; color: #ffffff !important; }
    .badge-debit { background: linear-gradient(135deg, #ff5b57, #e04a46) !important; color: #ffffff !important; }
    
    /* Botones de acción mejorados */
    .action-buttons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        margin: 0 2px;
        transition: all 0.2s ease;
    }
    .action-buttons a:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Modal mejorado */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    .modal-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e4e9f0;
        border-radius: 12px 12px 0 0;
        padding: 18px 24px;
    }
    .modal-title {
        font-weight: 700;
        color: #2d3a48;
        font-size: 16px;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-body h6 {
        font-weight: 700;
        color: #2d3a48;
        font-size: 14px;
        margin-bottom: 12px;
    }
    .modal-body .table {
        margin-bottom: 0;
    }
    .modal-body .table td {
        padding: 10px 12px;
        font-size: 13px;
    }
    .modal-body .table .font-weight-bold {
        color: #556477;
    }
    .modal-body .bg-light {
        background: #f8f9fa !important;
        border: 1px solid #e4e9f0;
    }
    
    /* Filtros */
    .box-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e4e9f0;
        padding: 12px 16px;
        border-radius: 8px 8px 0 0;
    }
    .box-title {
        font-weight: 700;
        color: #2d3a48;
        font-size: 14px;
    }
    .box-body {
        padding: 16px;
        background: #fff;
        border: 1px solid #e4e9f0;
        border-top: none;
        border-radius: 0 0 8px 8px;
    }
    
    /* Botones de filtro */
    .options-group .btn {
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
    .options-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:;"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>

<!-- ESTADÍSTICAS -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-blue">
            <div class="stats-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="stats-info">
                <h4 id="stat-total">0</h4>
                <p>Total Documentos</p>
            </div>
            <div class="stats-link"><a href="javascript:;">Ver detalle <i class="fas fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-green">
            <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stats-info">
                <h4 id="stat-authorized">0</h4>
                <p>Autorizadas</p>
            </div>
            <div class="stats-link"><a href="javascript:;">Ver detalle <i class="fas fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-orange">
            <div class="stats-icon"><i class="fas fa-clock"></i></div>
            <div class="stats-info">
                <h4 id="stat-pending">0</h4>
                <p>Pendientes</p>
            </div>
            <div class="stats-link"><a href="javascript:;">Ver detalle <i class="fas fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-red">
            <div class="stats-icon"><i class="fas fa-times-circle"></i></div>
            <div class="stats-info">
                <h4 id="stat-rejected">0</h4>
                <p>Rechazadas</p>
            </div>
            <div class="stats-link"><a href="javascript:;">Ver detalle <i class="fas fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
</div>

<!-- RESUMEN POR TIPO -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-teal">
            <div class="stats-icon"><i class="fas fa-file-alt"></i></div>
            <div class="stats-info">
                <h4 id="stat-invoices">0</h4>
                <p>Facturas</p>
            </div>
            <div class="stats-link"><a href="javascript:;">Ver detalle <i class="fas fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-purple">
            <div class="stats-icon"><i class="fas fa-file-medical"></i></div>
            <div class="stats-info">
                <h4 id="stat-credit-notes">0</h4>
                <p>Notas Crédito</p>
            </div>
            <div class="stats-link"><a href="javascript:;">Ver detalle <i class="fas fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12">
        <div class="widget widget-stats bg-dark">
            <div class="stats-icon"><i class="fas fa-coins"></i></div>
            <div class="stats-info">
                <h4 id="stat-total-amount">$ 0</h4>
                <p>Total Facturado</p>
            </div>
            <div class="stats-link"><a href="javascript:;">Ver detalle <i class="fas fa-arrow-alt-circle-right"></i></a></div>
        </div>
    </div>
</div>

<!-- TABLA PRINCIPAL -->
<div class="panel panel-default panel-electronic-reports">
  <div class="panel-heading">
    <h4 class="panel-title">Lista de Documentos Electrónicos</h4>
    <div class="panel-heading-btn">
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand" data-original-title="" title="" data-init="true"><i class="fas fa-expand"></i></a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refreshTable()" data-original-title="" title="" data-init="true"><i class="fas fa-sync-alt"></i></a>
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
              <div class="col-lg-3 col-md-6 col-12">
                <div class="form-group">
                  <label>Fecha</label>
                  <div class="input-group input-daterange">
                    <input type="text" class="form-control" id="start" value="<?= date('d/m/Y', strtotime(date('Y-m-01'))) ?>">
                    <span class="input-group-addon">al</span>
                    <input type="text" class="form-control" id="end" value="<?= date('d/m/Y') ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-md-6 col-12">
                <div class="form-group">
                  <label>Estado</label>
                  <select class="form-control" id="listStates" style="width: 100%;">
                    <option value="0">TODOS</option>
                    <option value="1">AUTORIZADAS</option>
                    <option value="0">PENDIENTES</option>
                    <option value="2">RECHAZADAS</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-6 col-12">
                <div class="form-group">
                  <label>Tipo</label>
                  <select class="form-control" id="listTypes" style="width: 100%;">
                    <option value="0">TODOS</option>
                    <option value="invoice">FACTURAS</option>
                    <option value="credit-note">NOTAS CRÉDITO</option>
                    <option value="debit-note">NOTAS DÉBITO</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-6 col-12">
                <div class="form-group">
                  <label class="text-white width-full">.</label>
                  <button type="button" class="btn btn-success" id="btn-search" onclick="searchRecords()"> <i class="fa fa-search mr-1"></i> Buscar </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="list-btns-tools" style="display: none;">
        <div class="options-group btn-group m-r-5">
          <button type="button" class="btn btn-white" data-toggle="collapse" href="#collapseview"><i class="fas fa-filter mr-1"></i>Filtro</button>
          <button type="button" class="btn btn-white" onclick="exportExcel()"><i class="far fa-file-excel mr-1"></i>Exportar</button>
        </div>
      </div>
      <div class="col-md-12 col-sm-12 col-12">
        <div class="table-responsive">
          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width: 100%;">
            <thead>
              <tr>
                <th data-priority="1">Nº Documento</th>
                <th data-priority="2">Tipo</th>
                <th data-priority="3">Cliente</th>
                <th data-priority="5">Documento</th>
                <th data-priority="4">F. Emisión</th>
                <th data-priority="4">Total</th>
                <th data-priority="1">Estado</th>
                <th data-priority="6">CUFE</th>
                <th data-priority="7">F. DIAN</th>
                <th data-priority="1" data-orderable="false" style="max-width: 40px !important; width: 40px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL DETALLE -->
<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-detail-title">Detalle del Documento Electrónico</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modal-detail-body">
        <!-- Contenido dinámico -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php footer($data); ?>
