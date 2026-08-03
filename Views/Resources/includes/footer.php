        </div>
        <!-- footer -->
        <!--<div class="footer" id="footer">
          Copyright &copy;2022 - <script>document.write(new Date().getFullYear())</script> <a class="f-w-700 f-s-13" style="color:#4e5c68;" href="<?= DEVELOPER_WEBSITE ?>" target=”_blank”><?= ucwords(strtolower(NAME_SYSTEM)) ?></a>.
		    </div>-->
        <!-- begin theme-panel -->
        <div class="theme-panel theme-panel-lg">
			    <a href="javascript:;" data-click="theme-panel-expand" class="theme-collapse-btn"><i class="fas fa-cogs f-s-15"></i></a>
    			<div class="theme-panel-content">
    				<h5>Colores</h5>
    				<ul class="theme-list clearfix">
              <li>
                <a href="javascript:;" class="bg-red" data-theme="red" data-theme-file="<?= base_style() ?>/css/default/theme/red.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Red">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-pink" data-theme="pink" data-theme-file="<?= base_style() ?>/css/default/theme/pink.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Pink">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-orange" data-theme="orange" data-theme-file="<?= base_style() ?>/css/default/theme/orange.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Orange">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-yellow" data-theme="yellow" data-theme-file="<?= base_style() ?>/css/default/theme/yellow.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Yellow">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-lime" data-theme="lime" data-theme-file="<?= base_style() ?>/css/default/theme/lime.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Lime">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-green" data-theme="green" data-theme-file="<?= base_style() ?>/css/default/theme/green.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Green">&nbsp;</a>
              </li>
              <li class="active">
                <a href="javascript:;" class="bg-teal" data-theme="default" data-theme-file="" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Default">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-aqua" data-theme="aqua" data-theme-file="<?= base_style() ?>/css/default/theme/aqua.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Aqua">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-blue" data-theme="blue" data-theme-file="<?= base_style() ?>/css/default/theme/blue.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Blue">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-purple" data-theme="purple" data-theme-file="<?= base_style() ?>/css/default/theme/purple.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Purple">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-indigo" data-theme="indigo" data-theme-file="<?= base_style() ?>/css/default/theme/indigo.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Indigo">&nbsp;</a>
              </li>
              <li>
                <a href="javascript:;" class="bg-black" data-theme="black" data-theme-file="<?= base_style() ?>/css/default/theme/black.min.css" data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Black">&nbsp;</a>
              </li>
    				</ul>
    				<div class="divider"></div>
    				<div class="row m-t-10">
    					<div class="col-8 control-label text-inverse f-w-600">Barra fija</div>
    					<div class="col-4 d-flex">
    						<div class="custom-control custom-switch ml-auto">
    							<input type="checkbox" class="custom-control-input" name="header-fixed" id="headerFixed" value="1" checked="">
    							<label class="custom-control-label" for="headerFixed">&nbsp;</label>
    						</div>
    					</div>
    				</div>
    				<div class="row m-t-10">
    					<div class="col-8 control-label text-inverse f-w-600">Barra oscura</div>
    					<div class="col-4 d-flex">
    						<div class="custom-control custom-switch ml-auto">
    							<input type="checkbox" class="custom-control-input" name="header-inverse" id="headerInverse" value="1">
    							<label class="custom-control-label" for="headerInverse">&nbsp;</label>
    						</div>
    					</div>
    				</div>
    				<div class="row m-t-10">
    					<div class="col-8 control-label text-inverse f-w-600">Menú fijo</div>
    					<div class="col-4 d-flex">
    						<div class="custom-control custom-switch ml-auto">
    							<input type="checkbox" class="custom-control-input" name="sidebar-fixed" id="sidebarFixed" value="1" checked="">
    							<label class="custom-control-label" for="sidebarFixed">&nbsp;</label>
    						</div>
    					</div>
    				</div>
    				<div class="row m-t-10">
    					<div class="col-8 control-label text-inverse f-w-600">Menú grid</div>
    					<div class="col-4 d-flex">
    						<div class="custom-control custom-switch ml-auto">
    							<input type="checkbox" class="custom-control-input" name="sidebar-grid" id="sidebarGrid" value="1">
    							<label class="custom-control-label" for="sidebarGrid">&nbsp;</label>
    						</div>
    					</div>
    				</div>
    				<div class="row m-t-10">
    					<div class="col-md-8 control-label text-inverse f-w-600">Menú degrado</div>
    					<div class="col-md-4 d-flex">
    						<div class="custom-control custom-switch ml-auto">
    							<input type="checkbox" class="custom-control-input" name="sidebar-gradient" id="sidebarGradient" value="1">
    							<label class="custom-control-label" for="sidebarGradient">&nbsp;</label>
    						</div>
    					</div>
    				</div>
    			</div>
    		</div>
        <!-- end theme-panel -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    </div>
    <!-- ================== FIN FOOTER ================== -->
    <!-- ================== INICIO RUTA  ============== -->
    <script>
        const base_url = "<?= base_url(); ?>";
        const permission_create = "<?= empty($_SESSION['permits_module']['r']) ? 0 : $_SESSION['permits_module']['r'] ?>";
        const permission_edit = "<?= empty($_SESSION['permits_module']['a']) ? 0 : $_SESSION['permits_module']['a'] ?>";
        const permission_remove = "<?= empty($_SESSION['permits_module']['e']) ? 0 : $_SESSION['permits_module']['e'] ?>";
        const permission_view = "<?= empty($_SESSION['permits_module']['v']) ? 0 : $_SESSION['permits_module']['v'] ?>";
        const currency_symbol = "<?= empty($_SESSION['businessData']['symbol']) ? "" : $_SESSION['businessData']['symbol'] ?>";
        const user_profile = "<?= empty($_SESSION['userData']['profileid']) ? 0 : $_SESSION['userData']['profileid'] ?>";
        const key_google = "<?= empty($_SESSION['businessData']['google_apikey']) ? "" : $_SESSION['businessData']['google_apikey'] ?>";
        const csrf_token = "<?= get_csrf_token(); ?>";
    </script>
    <!-- ================== INICIO RUTA  ============== -->
    <!-- ================== INICIO ARCHIVOS JS ======== -->
    <!-- Scripts críticos (sin defer) -->
    <script src="<?= base_style() ?>/js/app.min.js"></script>
    <!-- Scripts con defer para carga asíncrona -->
    <script defer src="<?= base_style() ?>/js/moment.min.js"></script>
    <script defer src="<?= base_style() ?>/js/functions.js?v=2026080217"></script>
    <script defer src="<?= base_style() ?>/js/utils.min.js"></script>
    <script defer src="<?= base_style() ?>/js/initial.min.js"></script>
    <script defer src="<?= base_style() ?>/js/theme/default.min.js"></script>
    <script defer src="<?= base_style() ?>/js/jquery-confirm.min.js"></script>
    <script defer src="<?= base_style() ?>/js/jquery.bootstrap-touchspin.min.js"></script>
    <script defer src="<?= base_style() ?>/js/datatables.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/jszip/jszip.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/pdfmake/pdfmake.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/pdfmake/vfs_fonts.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/tinymce/tinymce.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/select2/js/select2.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/parsleyjs/parsley.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/smartwizard/js/jquery.smartWizard.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/gritter/js/jquery.gritter.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/jquery.maskedinput/jquery.maskedinput.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/chartjs/js/chart.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/lightbox/js/lightbox.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script defer src="<?= base_style() ?>/bookstores/axios/axios.min.js"></script>
    <script defer src="<?= base_style() ?>/js/ui-enhancements.js?v=2026080221"></script>
    <!-- ================== FIN ARCHIVOS JS =========== -->
    <!-- ================== FIN API GOOGLE MAPS JS ================== -->
    <?php if(isset($data['page_functions_js'])){ ?>
    <!-- ================== INICIO FUNCION JS ============ -->
    <script defer src="<?= base_style() ?>/js/functions/<?= $data['page_functions_js']; ?>?v=2026080227"></script>
    <!-- ================== FIN FUNCION JS =============== -->
     <?php } ?>
    </body>
</html>
