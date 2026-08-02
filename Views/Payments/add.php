<?php
  head($data);
  modal("paymentsModal",$data);
  $symbol = isset($_SESSION['businessData']['symbol']) ? $_SESSION['businessData']['symbol'] : 'S/';
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">Registrar pago</h4>
    <div class="panel-heading-btn">
      <a href="javascript:window.history.back();" class="btn btn-xs btn-icon btn-circle btn-iconpanel"><i class="fas fa-reply"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-xl-4 search_payment">
        <label class="label_payment">BUSCAR CLIENTE</label>
      </div>
      <div class="col-xl-5 m-t-10 m-b-10">
        <div class="search-input">
          <input type="text" id="search_client" placeholder="NOMBRE O DOCUMENTO">
          <div id="box-search" class="autocom-box"></div>
          <div class="icon"><i class="fas fa-search"></i></div>
        </div>
      </div>
      <div class="col-xl-12 m-t-10" id="pending_invoices" style="display: none;" data-sortable="false"></div>
    </div>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">Clientes con deudas pendientes</h4>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-xl-12" id="pending_clients_list" data-sortable="false">
        <?php if(!empty($data['pending_clients'])){ ?>
        <table class="table table-sm table-hover table-striped">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Documento</th>
              <th>Deuda Total</th>
              <th>Vence</th>
              <th>Accion</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($data['pending_clients'] as $row){ ?>
            <tr>
              <td><?= htmlspecialchars($row['names'].' '.$row['surnames']) ?></td>
              <td><?= htmlspecialchars($row['document']) ?></td>
              <td class="font-weight-bold text-danger"><?= $symbol.format_money($row['total_debt']) ?></td>
              <td><?= date('d/m/Y', strtotime($row['next_expiration'])) ?></td>
              <td><button class="btn btn-sm btn-primary" onclick="search_pending_bills('<?= htmlspecialchars($row['document'],ENT_QUOTES) ?>')"><i class="fas fa-search"></i> Verificar</button></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <?php }else{ ?>
          <div class="text-center text-muted p-3"><i class="fas fa-check-circle fa-2x mb-2"></i><p>No hay clientes con deudas pendientes</p></div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
