var business = document.querySelector('#idbusiness');
if(document.querySelector('#footer_text')){
    tinymce.init({
        selector: '#footer_text',
        width: "100%",
        language: "es",
        height: 300,
        statubar: true,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table directionality emoticons template paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
    });
}
/* FUNCIONES GLOBALES - Disponibles para onclick inline */
function toggleToken(){
    var input = document.getElementById('apidian_token');
    if(input.type === 'password'){
        input.type = 'text';
    }else{
        input.type = 'password';
    }
}
function showAddResolution(){
    document.getElementById('form-resolution').style.display = 'block';
    document.getElementById('resolution_id').value = '0';
    document.getElementById('transactions_resolution').reset();
    toggleResolutionFields();
}
function cancelResolution(){
    document.getElementById('form-resolution').style.display = 'none';
}
function editResolution(id){
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/settings/get_resolution/'+id;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == 'success'){
                var data = objData.data;
                document.getElementById('form-resolution').style.display = 'block';
                document.getElementById('resolution_id').value = data.id;
                document.getElementById('resolution_type_doc').value = data.type_document_id;
                document.getElementById('resolution_prefix').value = data.prefix;
                document.getElementById('resolution_number').value = data.resolution_number;
                document.getElementById('resolution_date').value = data.resolution_date || '';
                document.getElementById('resolution_date_from').value = data.date_from;
                document.getElementById('resolution_date_to').value = data.date_to;
                document.getElementById('resolution_from').value = data.consecutive_from;
                document.getElementById('resolution_to').value = data.consecutive_to;
                document.getElementById('resolution_current').value = data.current_consecutive || 0;
                toggleResolutionFields();
            }
        }
    }
}
function toggleResolution(id, state){
    var msg = state == 0 ? 'desactivar' : 'activar';
    var icon = state == 0 ? 'fas fa-pause-circle' : 'fas fa-play-circle';
    var color = state == 0 ? 'orange' : 'green';
    var btnClass = state == 0 ? 'btn-warning' : 'btn-success';
    $.confirm({
        theme: 'modern',
        draggable: false,
        closeIcon: true,
        animationBounce: 2.5,
        type: color,
        icon: icon,
        title: msg.toUpperCase() + ' RESOLUCIÓN',
        content: '<div style="text-align:center;">' +
            '<div style="font-size:48px;color:' + (state == 0 ? '#f59c1a' : '#1aa179') + ';margin-bottom:15px;"><i class="' + icon + '"></i></div>' +
            '<p style="font-size:14px;color:#555;">¿Desea ' + msg + ' esta resolución?</p>' +
            '</div>',
        buttons: {
            confirm: {
                text: '<i class="fas fa-check mr-1"></i>Confirmar',
                btnClass: btnClass,
                action: function(){
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    var ajaxUrl = base_url+'/settings/toggle_resolution';
                    var formData = new FormData();
                    formData.append('id', id);
                    formData.append('state', state);
                    request.open("POST",ajaxUrl,true);
                    request.send(formData);
                    request.onreadystatechange = function(){
                        if(request.readyState == 4 && request.status == 200){
                            var objData = JSON.parse(request.responseText);
                            if(objData.status == 'success'){
                                alert_msg("success", objData.msg);
                                loadResolutions();
                            }else{
                                alert_msg("error", objData.msg);
                            }
                        }
                    }
                }
            },
            cancel: {
                text: '<i class="fas fa-times mr-1"></i>Cancelar',
                btnClass: 'btn-default'
            }
        }
    });
}
function deleteResolution(id){
    $.confirm({
        theme: 'modern',
        draggable: false,
        closeIcon: true,
        animationBounce: 2.5,
        type: 'red',
        icon: 'fas fa-trash-alt',
        title: 'ELIMINAR RESOLUCIÓN',
        content: '<div style="text-align:center;">' +
            '<div style="font-size:48px;color:#ff5b57;margin-bottom:15px;"><i class="fas fa-trash-alt"></i></div>' +
            '<p style="font-size:14px;color:#555;margin-bottom:10px;">¿Está seguro de eliminar esta resolución?</p>' +
            '<p style="font-size:12px;color:#888;">Esta acción no se puede deshacer.</p>' +
            '</div>',
        buttons: {
            delete: {
                text: '<i class="fas fa-trash-alt mr-1"></i>Eliminar',
                btnClass: 'btn-danger',
                action: function(){
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    var ajaxUrl = base_url+'/settings/delete_resolution';
                    var formData = new FormData();
                    formData.append('id', id);
                    request.open("POST",ajaxUrl,true);
                    request.send(formData);
                    request.onreadystatechange = function(){
                        if(request.readyState == 4 && request.status == 200){
                            var objData = JSON.parse(request.responseText);
                            if(objData.status == 'success'){
                                alert_msg("success", objData.msg);
                                loadResolutions();
                            }else{
                                alert_msg("error", objData.msg);
                            }
                        }
                    }
                }
            },
            cancel: {
                text: '<i class="fas fa-times mr-1"></i>Cancelar',
                btnClass: 'btn-default'
            }
        }
    });
}

function loadResolutions(){
    var container = document.getElementById('list-resolutions');
    if(!container) return;
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/settings/list_resolutions';
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var data = JSON.parse(request.responseText);
            var html = '';
            if(data.length > 0){
                document.getElementById('no-resolutions').style.display = 'none';
                document.getElementById('table-resolutions').style.display = 'table';
                for(var i = 0; i < data.length; i++){
                    html += '<tr>';
                    html += '<td>'+data[i].type_name+'</td>';
                    html += '<td>'+data[i].prefix+'</td>';
                    html += '<td>'+data[i].resolution_number+'</td>';
                    html += '<td>'+data[i].consecutive_from+'</td>';
                    html += '<td>'+data[i].consecutive_to+'</td>';
                    html += '<td><strong>'+data[i].current_consecutive+'</strong></td>';
                    html += '<td>'+data[i].date_from_format+' - '+data[i].date_to_format+'</td>';
                    html += '<td>'+data[i].state_name+'</td>';
                    html += '<td>'+data[i].actions+'</td>';
                    html += '</tr>';
                }
                container.innerHTML = html;
            }else{
                document.getElementById('no-resolutions').style.display = 'block';
                document.getElementById('table-resolutions').style.display = 'none';
            }
        }
    }
}
function toggleResolutionFields(){
    var typeDoc = document.getElementById('resolution_type_doc').value;
    var groupResolution = document.getElementById('group_resolution_number');
    var groupDate = document.getElementById('group_resolution_date');
    var groupDateFrom = document.getElementById('group_resolution_date_from');
    var groupDateTo = document.getElementById('group_resolution_date_to');
    var groupTechKey = document.getElementById('group_technical_key');
    
    if(typeDoc == 1 || typeDoc == 11){
        // Factura Electrónica y Doc. Soporte - todos los campos obligatorios
        if(groupResolution) groupResolution.style.display = 'flex';
        if(groupDate) groupDate.style.display = 'flex';
        if(groupDateFrom) groupDateFrom.style.display = 'flex';
        if(groupDateTo) groupDateTo.style.display = 'flex';
        if(groupTechKey) groupTechKey.style.display = 'flex';
    }else{
        // NC, ND - mostrar todos los campos (opcionales pero útiles)
        if(groupResolution) groupResolution.style.display = 'flex';
        if(groupDate) groupDate.style.display = 'flex';
        if(groupDateFrom) groupDateFrom.style.display = 'flex';
        if(groupDateTo) groupDateTo.style.display = 'flex';
        if(groupTechKey) groupTechKey.style.display = 'flex';
    }
}
function sendResolutionToApidian(){
    var form = document.getElementById('transactions_resolution');
    if(!form) return;
    
    var typeDoc = document.getElementById('resolution_type_doc').value;
    var prefix = document.getElementById('resolution_prefix').value;
    var from = document.getElementById('resolution_from').value;
    var to = document.getElementById('resolution_to').value;
    
    if(!prefix){
        alert_msg("error","El prefijo es obligatorio");
        return;
    }
    if(!from || !to){
        alert_msg("error","Los consecutivos son obligatorios");
        return;
    }
    
    if(typeDoc == 1){
        // Factura Electrónica - todos los campos obligatorios
        var resolution = document.getElementById('resolution_number').value;
        var resolutionDate = document.getElementById('resolution_date').value;
        var dateFrom = document.getElementById('resolution_date_from').value;
        var dateTo = document.getElementById('resolution_date_to').value;
        
        if(!resolution || !resolutionDate || !dateFrom || !dateTo){
            alert_msg("error","Para Factura Electrónica todos los campos son obligatorios");
            return;
        }
    }
    // NC y ND no requieren campos adicionales
    
    $.confirm({
        theme: 'modern',
        draggable: false,
        closeIcon: true,
        animationBounce: 2.5,
        type: 'blue',
        icon: 'fas fa-cloud-upload-alt',
        title: 'ENVIAR RESOLUCIÓN A APIDIAN',
        content: '<div style="text-align:center;">' +
            '<div style="font-size:48px;color:#348fe2;margin-bottom:15px;"><i class="fas fa-cloud-upload-alt"></i></div>' +
            '<p style="font-size:14px;color:#555;margin-bottom:10px;">¿Está seguro de enviar esta resolución a APIDIAN?</p>' +
            '<p style="font-size:12px;color:#888;">Este proceso registrará la resolución en el sistema de facturación electrónica.</p>' +
            '</div>',
        buttons: {
            send: {
                text: '<i class="fas fa-paper-plane mr-1"></i>Enviar a APIDIAN',
                btnClass: 'btn-blue',
                action: function(){
                    showInvoiceLoading('Enviando resolución a APIDIAN...');
                    var formData = new FormData(form);
                    var ajaxUrl = base_url+'/settings/send_resolution_to_apidian';
                    
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    request.open("POST", ajaxUrl, true);
                    
                    request.onload = function() {
                        if (request.status === 200) {
                            try {
                                var objData = JSON.parse(request.responseText);
                                if (objData.status === "success") {
                                    hideLoading(true);
                                    setTimeout(function(){
                                        alert_msg("success", objData.msg);
                                        cancelResolution();
                                        loadResolutions();
                                    }, 900);
                                } else {
                                    hideLoading(false);
                                    setTimeout(function(){
                                        alert_msg("error", objData.msg);
                                    }, 1300);
                                }
                            } catch (parseError) {
                                hideLoading(false);
                                setTimeout(function(){
                                    alert_msg("error", "Error al procesar la respuesta");
                                }, 1300);
                            }
                        } else {
                            hideLoading(false);
                            setTimeout(function(){
                                alert_msg("error", "Error del servidor: " + request.status);
                            }, 1300);
                        }
                    };
                    
                    request.onerror = function() {
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error", "Error de conexión");
                        }, 1300);
                    };
                    
                    request.send(formData);
                }
            },
            cancel: {
                text: '<i class="fas fa-times mr-1"></i>Cancelar',
                btnClass: 'btn-default'
            }
        }
    });
}
/* EVENTOS - Se ejecutan cuando el DOM esta listo */
document.addEventListener('DOMContentLoaded',function(){
    if(document.querySelector("#transactions_general")){
        var transactions_general = document.querySelector("#transactions_general");
        transactions_general.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions_general').parsley().isValid()){
                showDatabaseLoading('Guardando configuración general...');
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/business/update_general';
                var formData = new FormData(transactions_general);
                request.open("POST",ajaxUrl,true);
                request.send(formData);
                request.onreadystatechange = function(){
                    if(request.readyState == 4 && request.status == 200){
                        var objData = JSON.parse(request.responseText);
                        if(objData.status == "success"){
                            hideLoading(true);
                            setTimeout(function(){
                                alert_msg("success", objData.msg);
                                location.reload();
                            }, 900);
                        }else{
                            hideLoading(false);
                            setTimeout(function(){
                                alert_msg("error",objData.msg);
                            }, 1300);
                        }
                    }
                    return false;
                }
            }
        }
    }
    if(document.querySelector("#transactions_basic")){
        var transactions_basic = document.querySelector("#transactions_basic");
        transactions_basic.onsubmit = function(e){
            e.preventDefault();
            showDatabaseLoading('Guardando información básica...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/update_basic';
            var formData = new FormData(transactions_basic);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        hideLoading(true);
                        setTimeout(function(){
                            alert_msg("success", objData.msg);
                            location.reload();
                        }, 900);
                    }else{
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error",objData.msg);
                        }, 1300);
                    }
                }
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_invoice")){
        var transactions_invoice = document.querySelector("#transactions_invoice");
        transactions_invoice.onsubmit = function(e){
            e.preventDefault();
            showInvoiceLoading('Guardando configuración de facturación...');
            tinyMCE.triggerSave();
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/update_invoice';
            var formData = new FormData(transactions_invoice);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        hideLoading(true);
                        setTimeout(function(){
                            alert_msg("success", objData.msg);
                            location.reload();
                        }, 900);
                    }else{
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error",objData.msg);
                        }, 1300);
                    }
                }
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_logofac")){
        var transactions_logofac = document.querySelector("#transactions_logofac");
        transactions_logofac.onsubmit = function(e){
            e.preventDefault();
            var logo_fac = document.querySelector('#logo-fac').value;
            if(logo_fac == ""){
              alert_msg("info","Selecionar una foto para poder realizar este cambio.");
              return false;
            }
            showUploadLoading('Subiendo logo principal...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/main_logo';
            var formData = new FormData(transactions_logofac);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        hideLoading(true);
                        setTimeout(function(){
                            alert_msg("success", objData.msg);
                            location.reload();
                        }, 900);
                    }else{
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error",objData.msg);
                        }, 1300);
                    }
                }
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_logo")){
        var transactions_logo = document.querySelector("#transactions_logo");
        transactions_logo.onsubmit = function(e){
            e.preventDefault();
            var logo_sesion = document.querySelector('#logo').value;
            if(logo_sesion == ""){
              alert_msg("info","Selecionar una foto para poder realizar este cambio.");
              return false;
            }
            showUploadLoading('Subiendo logo de acceso...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/login_logo';
            var formData = new FormData(transactions_logo);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        alert_msg("success", objData.msg);
                        setTimeout(function(){ location.reload(); }, 1500);
                    }else{
                        alert_msg("error",objData.msg);
                    }
                }
                hideLoading();
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_favicon")){
        var transactions_favicon = document.querySelector("#transactions_favicon");
        transactions_favicon.onsubmit = function(e){
            e.preventDefault();
            var favicon = document.querySelector('#favicon').value;
            if(favicon == ""){
              alert_msg("info","Selecionar una foto para poder realizar este cambio.");
              return false;
            }
            showLoading('Subiendo favicon...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/favicon';
            var formData = new FormData(transactions_favicon);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        alert_msg("success", objData.msg);
                        setTimeout(function(){ location.reload(); }, 1500);
                    }else{
                        alert_msg("error",objData.msg);
                    }
                }
                hideLoading();
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_background")){
        var transactions_background = document.querySelector("#transactions_background");
        transactions_background.onsubmit = function(e){
            e.preventDefault();
            showLoading('Guardando fondo de pantalla...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/background';
            var formData = new FormData(transactions_background);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        alert_msg("success", objData.msg);
                        setTimeout(function(){ location.reload(); }, 1500);
                    }else{
                        alert_msg("error",objData.msg);
                    }
                }
                hideLoading();
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_google")){
        var transactions_google = document.querySelector("#transactions_google");
        transactions_google.onsubmit = function(e){
            e.preventDefault();
            showLoading('Guardando configuración de Google...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/google';
            var formData = new FormData(transactions_google);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        alert_msg("success", objData.msg);
                    }else{
                        alert_msg("error",objData.msg);
                    }
                }
                hideLoading();
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_reniec")){
        var transactions_reniec = document.querySelector("#transactions_reniec");
        transactions_reniec.onsubmit = function(e){
            e.preventDefault();
            showLoading('Guardando configuración RENIEC...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/reniec';
            var formData = new FormData(transactions_reniec);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        alert_msg("success", objData.msg);
                    }else{
                        alert_msg("error",objData.msg);
                    }
                }
                hideLoading();
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_email")){
        var transactions_email = document.querySelector("#transactions_email");
        transactions_email.onsubmit = function(e){
            e.preventDefault();
            showEmailLoading('Guardando configuración de correo...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/business/email';
            var formData = new FormData(transactions_email);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        hideLoading(true);
                        setTimeout(function(){
                            alert_msg("success", objData.msg);
                        }, 900);
                    }else{
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error",objData.msg);
                        }, 1300);
                    }
                }
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_apidian_connection")){
        var transactions_apidian_connection = document.querySelector("#transactions_apidian_connection");
        transactions_apidian_connection.onsubmit = function(e){
            e.preventDefault();
            showInvoiceLoading('Conectando con APIDIAN...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/settings/update_apidian_connection';
            var formData = new FormData(transactions_apidian_connection);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        hideLoading(true);
                        setTimeout(function(){
                            alert_msg("success", objData.msg);
                        }, 900);
                    }else{
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error",objData.msg);
                        }, 1300);
                    }
                }
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_taxes")){
        var transactions_taxes = document.querySelector("#transactions_taxes");
        transactions_taxes.onsubmit = function(e){
            e.preventDefault();
            showDatabaseLoading('Guardando configuración de impuestos...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/settings/update_taxes';
            var formData = new FormData(transactions_taxes);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        hideLoading(true);
                        setTimeout(function(){
                            alert_msg("success", objData.msg);
                        }, 900);
                    }else{
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error",objData.msg);
                        }, 1300);
                    }
                }
                return false;
            }
        }
    }
    if(document.querySelector("#transactions_resolution")){
        var transactions_resolution = document.querySelector("#transactions_resolution");
        transactions_resolution.onsubmit = function(e){
            e.preventDefault();
            showInvoiceLoading('Guardando resolución de facturación...');
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/settings/save_resolution';
            var formData = new FormData(transactions_resolution);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == "success"){
                        hideLoading(true);
                        setTimeout(function(){
                            alert_msg("success", objData.msg);
                            cancelResolution();
                            loadResolutions();
                        }, 900);
                    }else{
                        hideLoading(false);
                        setTimeout(function(){
                            alert_msg("error",objData.msg);
                        }, 1300);
                    }
                }
                return false;
            }
        }
        loadResolutions();
    }
    if(document.querySelector("#transactions_config_apidian")){
        var transactions_config_apidian = document.querySelector("#transactions_config_apidian");
        transactions_config_apidian.onsubmit = function(e){
            e.preventDefault();
            $.confirm({
                theme: 'modern',
                draggable: false,
                closeIcon: true,
                animationBounce: 2.5,
                type: 'blue',
                icon: 'fas fa-building',
                title: 'REGISTRAR EMPRESA EN APIDIAN',
                content: '<div style="text-align:center;">' +
                    '<div style="font-size:48px;color:#348fe2;margin-bottom:15px;"><i class="fas fa-building"></i></div>' +
                    '<p style="font-size:14px;color:#555;margin-bottom:10px;">¿Está seguro de registrar la empresa en APIDIAN?</p>' +
                    '<div style="background:#fff3cd;padding:12px;border-radius:8px;margin-top:10px;border:1px solid #ffc107;">' +
                    '<i class="fas fa-exclamation-triangle" style="color:#856404;font-size:16px;"></i> ' +
                    '<span style="font-size:12px;color:#856404;">Este proceso solo debe ejecutarse una vez.</span></div>' +
                    '</div>',
                buttons: {
                    register: {
                        text: '<i class="fas fa-check mr-1"></i>Registrar Empresa',
                        btnClass: 'btn-blue',
                        action: function(){
                            showInvoiceLoading('Registrando empresa en APIDIAN...');
                            
                            var ajaxUrl = base_url+'/settings/configure_apidian_company';
                            var formData = new FormData(transactions_config_apidian);
                            
                            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                            request.open("POST", ajaxUrl, true);
                            
                            request.onload = function() {
                                if (request.status === 200) {
                                    try {
                                        var objData = JSON.parse(request.responseText);
                                        if (objData.status === "success") {
                                            hideLoading(true);
                                            setTimeout(function(){
                                                document.getElementById('apidian_token').value = objData.token;
                                                alert_msg("success", objData.msg + "\nToken: " + objData.token);
                                                setTimeout(function(){ location.reload(); }, 3000);
                                            }, 900);
                                        } else {
                                            hideLoading(false);
                                            setTimeout(function(){
                                                alert_msg("error", objData.msg);
                                            }, 1300);
                                        }
                                    } catch (parseError) {
                                        hideLoading(false);
                                        setTimeout(function(){
                                            alert_msg("error", "Error al procesar la respuesta del servidor");
                                        }, 1300);
                                    }
                                } else {
                                    hideLoading(false);
                                    setTimeout(function(){
                                        alert_msg("error", "Error del servidor: " + request.status);
                                    }, 1300);
                                }
                            };
                            
                            request.onerror = function() {
                                hideLoading(false);
                                setTimeout(function(){
                                    alert_msg("error", "Error de conexión");
                                }, 1300);
                            };
                            
                            request.send(formData);
                        }
                    },
                    cancel: {
                        text: '<i class="fas fa-times mr-1"></i>Cancelar',
                        btnClass: 'btn-default'
                    }
                }
            });
            return false;
        }
    }
    if(document.querySelector("#transactions_certificate")){
        var transactions_certificate = document.querySelector("#transactions_certificate");
        transactions_certificate.onsubmit = function(e){
            e.preventDefault();
            var password = document.getElementById('certificate_password').value;
            if(!password){
                alert_msg("error","Ingrese la contraseña del certificado");
                return false;
            }
            showLoading('Configurando certificado digital...');
            
            var ajaxUrl = base_url+'/settings/configure_certificate';
            var formData = new FormData(transactions_certificate);
            
            console.log('Enviando certificado a:', ajaxUrl);
            
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", ajaxUrl, true);
            
            request.onload = function() {
                hideLoading();
                if (request.status === 200) {
                    try {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status === "success") {
                            alert_msg("success", objData.msg);
                            setTimeout(function(){ location.reload(); }, 2000);
                        } else {
                            alert_msg("error", objData.msg);
                        }
                    } catch (parseError) {
                        alert_msg("error", "Error al procesar la respuesta");
                    }
                } else {
                    alert_msg("error", "Error del servidor: " + request.status);
                }
            };
            
            request.onerror = function() {
                hideLoading();
                alert_msg("error", "Error de conexión");
            };
            
            request.send(formData);
            return false;
        }
    }
    if(document.querySelector("#transactions_software")){
        var transactions_software = document.querySelector("#transactions_software");
        transactions_software.onsubmit = function(e){
            e.preventDefault();
            var softwareId = document.getElementById('software_id').value;
            var softwarePin = document.getElementById('software_pin').value;
            
            if(!softwareId){
                alert_msg("error","Ingrese el ID del software");
                return false;
            }
            if(!softwarePin || softwarePin.length != 5){
                alert_msg("error","El PIN debe tener exactamente 5 dígitos");
                return false;
            }
            
            showLoading('Configurando software DIAN...');
            var ajaxUrl = base_url+'/settings/configure_software';
            var formData = new FormData(transactions_software);
            
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", ajaxUrl, true);
            
            request.onload = function() {
                hideLoading();
                if (request.status === 200) {
                    try {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status === "success") {
                            alert_msg("success", objData.msg);
                            setTimeout(function(){ location.reload(); }, 2000);
                        } else {
                            alert_msg("error", objData.msg);
                        }
                    } catch (parseError) {
                        alert_msg("error", "Error al procesar la respuesta");
                    }
                } else {
                    alert_msg("error", "Error del servidor: " + request.status);
                }
            };
            
            request.onerror = function() {
                hideLoading();
                alert_msg("error", "Error de conexión");
            };
            
            request.send(formData);
            return false;
        }
    }
}, false);
/* EVENTOS - Se ejecutan cuando la pagina termina de cargar */
window.addEventListener('load', function(){
    if(document.querySelector('#transactions_general')){
        try{ $('#transactions_general').parsley(); }catch(e){}
    }
    try{ $('#listCurrency').select2({width:'100%'}); }catch(e){}
    try{ $('#listPrinters').select2({width:'100%'}); }catch(e){}
    try{ $('#listCountry').select2({width:'100%'}); }catch(e){}
    try{ $('#config_municipality').select2({width:'100%', placeholder: 'Buscar municipio...'}); }catch(e){}
    try{ $('#config_type_doc').select2({width:'100%'}); }catch(e){}
    try{ $('#config_type_org').select2({width:'100%'}); }catch(e){}
    try{ $('#config_type_regime').select2({width:'100%'}); }catch(e){}
    try{ $('#config_type_liability').select2({width:'100%'}); }catch(e){}
    try{ $('#resolution_type_doc').select2({width:'100%'}); }catch(e){}
    if(document.querySelector("#logo-fac")){
        var file = document.querySelector("#logo-fac");
        file.onchange = function(e) {
            var uploadFoto = document.querySelector("#logo-fac").value;
            var fileimg = document.querySelector("#logo-fac").files;
            var nav = window.URL || window.webkitURL;
            if(uploadFoto !=''){
                var type = fileimg[0].type;
                var size = fileimg[0].size;
                if(type != 'image/png'){
                    alert_msg("info","¡La imagen debe estar en formato PNG!");
                    if(document.querySelector('#image-logofac')) document.querySelector('#image-logofac').src = "";
                    file.value="";
                    return false;
                }else if(size > 215040){
                    file.value="";
                    alert_msg("info","¡La imagen no debe pesar más de 210 KB!");
                }else{
                    if(document.querySelector('#image-logofac')) document.querySelector('#image-logofac').src = "";
                    document.querySelector('#image-logofac').src = nav.createObjectURL(this.files[0]);
                }
            }
        }
    }
    if(document.querySelector("#logo")){
        var file = document.querySelector("#logo");
        file.onchange = function(e) {
            var uploadFoto = document.querySelector("#logo").value;
            var fileimg = document.querySelector("#logo").files;
            var nav = window.URL || window.webkitURL;
            if(uploadFoto !=''){
                var type = fileimg[0].type;
                var size = fileimg[0].size;
                if(type != 'image/png'){
                    alert_msg("info","¡La imagen debe estar en formato PNG!");
                    if(document.querySelector('#image-logo')) document.querySelector('#image-logo').src = "";
                    file.value="";
                    return false;
                }else if(size > 215040){
                    file.value="";
                    alert_msg("info","¡La imagen no debe pesar más de 210 KB!");
                }else{
                    if(document.querySelector('#image-logo')) document.querySelector('#image-logo').src = "";
                    document.querySelector('#image-logo').src = nav.createObjectURL(this.files[0]);
                }
            }
        }
    }
    if(document.querySelector("#favicon")){
        var file = document.querySelector("#favicon");
        file.onchange = function(e) {
            var uploadFoto = document.querySelector("#favicon").value;
            var fileimg = document.querySelector("#favicon").files;
            var nav = window.URL || window.webkitURL;
            if(uploadFoto !=''){
                var type = fileimg[0].type;
                var size = fileimg[0].size;
                if(type != 'image/png' && type != 'image/x-icon'){
                    alert_msg("info","¡La imagen debe estar en formato PNG!");
                    if(document.querySelector('#image-favicon')) document.querySelector('#image-favicon').src = "";
                    file.value="";
                    return false;
                }else if(size > 163840){
                    file.value="";
                    alert_msg("info","¡La imagen no debe pesar más de 160 KB!");
                }else{
                    if(document.querySelector('#image-favicon')) document.querySelector('#image-favicon').src = "";
                    document.querySelector('#image-favicon').src = nav.createObjectURL(this.files[0]);
                }
            }
        }
    }
}, false);
