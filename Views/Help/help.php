<?php head($data); ?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default">
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-12">
        <center><img src="<?= base_style() ?>/images/logotypes/superwisp.png" width="200px"></center>
        <p class="m-t-10">
          Esto es un Sistema de administración de clientes wisp e isp con funciones de contabilidad. Pudiendo administrar la facturación, suspensión y reactivación del servicio, calidad en la señal y recepción, servicio técnico y atención al cliente, en un solo sistema, facilitará la ejecución y el rendimiento de tu empresa y optimizar el servicio que se presta al cliente.
        </p>
      </div>
      <div class="ccol-md-12 col-sm-12 col-12" data-sortable="false">
        <div class="panel panel-default" data-sortable="false">
          <div class="panel-heading">
            <h6 class="panel-title f-w-700">DERECHOS DE AUTOR</h6>
          </div>
          <div class="panel-body border-panel" align="justify">
            <ul>
              <li><b>Proyecto: </b>Sistema de Proveedor de Internet</li>
              <li><b>Versión: </b>1.0.0</li>
              <li><b>Desarrollado por: </b><?= DEVELOPER ?></li>
              <li><b>Web: </b><a href="<?= DEVELOPER_WEBSITE ?>"><?= DEVELOPER_WEBSITE ?></a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="ccol-md-12 col-sm-12 col-12" data-sortable="false">
        <div class="panel panel-default" data-sortable="false">
          <div class="panel-heading">
            <h6 class="panel-title f-w-700">CONTACTO</h6>
          </div>
          <div class="panel-body border-panel" align="justify">
            <p>Para soporte en caso de que haya algun error en el funcionamiento del sistema o sugerencia de cambios, implementación de nuevos módulos escribanos:</p>
            <ul>
              <li><b>Correo: </b><?= DEVELOPER_EMAIL ?></li>
              <li><b>WhatsApp: </b>+51 <?= DEVELOPER_MOBILE ?></li>
            </ul>
            <p>Todo correo de este deberá ser con el asunto "Sugerencia-SistemaProveedordeInternet"</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
