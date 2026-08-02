/**
 * Funciones JavaScript para el Módulo de Reportes de Facturación Electrónica
 */

let table;

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Configurar DataTable
    initDataTable();
    
    // Cargar estadísticas
    loadStats();
    
    // Inicializar selectores
    initSelects();
    
    // Inicializar datepickers
    initDatepickers();
});

/**
 * Inicializa el DataTable principal
 */
function initDataTable() {
    table_configuration('#list', 'Reportes Facturación Electrónica');
    
    let start = document.getElementById('start').value;
    let end = document.getElementById('end').value;
    
    table = $('#list').DataTable({
        "ajax": {
            "url": base_url + "/electronicreports/list_records?start=" + start + "&end=" + end,
            "dataSrc": ""
        },
        "deferRender": true,
        "idDataTables": "1",
        "columns": [
            {"data": "document_number", "className": "text-center"},
            {"data": "type_label", "className": "text-center"},
            {"data": "client_name"},
            {"data": "client_document", "className": "text-center"},
            {"data": "date_issue_formatted", "className": "text-center"},
            {"data": "total_formatted", "className": "text-right"},
            {"data": "state_label", "className": "text-center"},
            {"data": "cufe_short"},
            {"data": "created_at_formatted", "className": "text-center"},
            {"data": "options", "className": "text-center", "aWidth": "40px"}
        ],
        "initComplete": function(oSettings, json) {
            $('#list_wrapper div.container-options').append($('#list-btns-tools').contents());
        }
    }).on('processing.dt', function(e, settings, processing) {
        if (processing) {
            loaderin('.panel-electronic-reports');
        } else {
            loaderout('.panel-electronic-reports');
        }
    });
}

/**
 * Inicializa los selectores
 */
function initSelects() {
    $('#listStates').select2({
        minimumResultsForSearch: -1
    });
    
    $('#listTypes').select2({
        minimumResultsForSearch: -1
    });
}

/**
 * Inicializa los datepickers
 */
function initDatepickers() {
    $('#start').datetimepicker({
        locale: 'es',
        format: 'DD/MM/YYYY'
    });
    
    $('#end').datetimepicker({
        locale: 'es',
        format: 'DD/MM/YYYY'
    });
}

/**
 * Carga las estadísticas del dashboard
 */
function loadStats() {
    let start = document.getElementById('start').value;
    let end = document.getElementById('end').value;
    
    // Convertir fechas si es necesario
    if (start.includes('/')) {
        let parts = start.split('/');
        start = parts[2] + '-' + parts[1] + '-' + parts[0];
    }
    if (end.includes('/')) {
        let parts = end.split('/');
        end = parts[2] + '-' + parts[1] + '-' + parts[0];
    }
    
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/electronicreports/get_stats?start=' + start + '&end=' + end;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status == 'success') {
                let stats = objData.data;
                
                document.getElementById('stat-total').textContent = stats.total_documents || 0;
                document.getElementById('stat-authorized').textContent = stats.authorized || 0;
                document.getElementById('stat-pending').textContent = stats.pending || 0;
                document.getElementById('stat-rejected').textContent = stats.rejected || 0;
                document.getElementById('stat-invoices').textContent = stats.invoices || 0;
                document.getElementById('stat-credit-notes').textContent = stats.credit_notes || 0;
                document.getElementById('stat-total-amount').textContent = stats.total_amount_formatted || '$ 0';
            }
        }
    };
}

/**
 * Busca registros con los filtros aplicados
 */
function searchRecords() {
    let start = document.getElementById('start').value;
    let end = document.getElementById('end').value;
    let state = document.getElementById('listStates').value;
    let type = document.getElementById('listTypes').value;
    
    // Recargar DataTable con nuevos parámetros
    let url = base_url + '/electronicreports/list_records?start=' + start + '&end=' + end;
    
    if (state !== '0') {
        url += '&state=' + state;
    }
    
    if (type !== '0') {
        url += '&type=' + type;
    }
    
    table.ajax.url(url).load();
    
    // Recargar estadísticas
    loadStats();
}

/**
 * Refresca la tabla
 */
function refreshTable() {
    table.ajax.reload(null, false);
    loadStats();
}

/**
 * Ver detalle de un documento electrónico
 */
function viewDetail(id) {
    showDatabaseLoading('Cargando detalle del documento...');
    
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/electronicreports/get_detail/' + id;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            hideLoading(true);
            
            setTimeout(function() {
                let objData = JSON.parse(request.responseText);
                
                if (objData.status == 'success') {
                    showDetailModal(objData.data);
                } else {
                    alert_msg("error", objData.msg);
                }
            }, 500);
        }
    };
}

/**
 * Muestra el modal con el detalle del documento
 */
function showDetailModal(data) {
    let html = '';
    
    // Información del documento
    html += '<div class="row mb-3">';
    html += '<div class="col-md-6">';
    html += '<h6 class="text-muted mb-2"><i class="fas fa-file-invoice mr-1"></i>Información del Documento</h6>';
    html += '<table class="table table-sm table-bordered">';
    html += '<tr><td class="font-weight-bold" style="width: 40%;">Tipo</td><td><span class="label label-' + data.state_class + '">' + data.type_label + '</span></td></tr>';
    html += '<tr><td class="font-weight-bold">Nº Documento</td><td>' + data.document_number + '</td></tr>';
    html += '<tr><td class="font-weight-bold">Estado</td><td><span class="label label-' + data.state_class + '">' + data.state_label + '</span></td></tr>';
    html += '<tr><td class="font-weight-bold">Fecha Emisión</td><td>' + data.date_issue_formatted + '</td></tr>';
    html += '<tr><td class="font-weight-bold">Fecha DIAN</td><td>' + data.created_at_formatted + '</td></tr>';
    html += '</table>';
    html += '</div>';
    
    html += '<div class="col-md-6">';
    html += '<h6 class="text-muted mb-2"><i class="fas fa-user mr-1"></i>Información del Cliente</h6>';
    html += '<table class="table table-sm table-bordered">';
    html += '<tr><td class="font-weight-bold" style="width: 40%;">Cliente</td><td>' + data.client_name + '</td></tr>';
    html += '<tr><td class="font-weight-bold">Documento</td><td>' + data.document_type + ': ' + data.client_document + '</td></tr>';
    html += '<tr><td class="font-weight-bold">Email</td><td>' + (data.client_email || 'N/A') + '</td></tr>';
    html += '<tr><td class="font-weight-bold">Teléfono</td><td>' + (data.client_mobile || 'N/A') + '</td></tr>';
    html += '<tr><td class="font-weight-bold">Dirección</td><td>' + (data.client_address || 'N/A') + '</td></tr>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    
    // CUFE
    if (data.cufe) {
        html += '<div class="mb-3">';
        html += '<h6 class="text-muted mb-2"><i class="fas fa-fingerprint mr-1"></i>CUFE</h6>';
        html += '<div class="bg-light p-2 rounded" style="word-break: break-all; font-family: monospace; font-size: 12px;">';
        html += data.cufe;
        html += '</div>';
        html += '</div>';
    }
    
    // Botones de descarga
    html += '<div class="mb-3">';
    html += '<h6 class="text-muted mb-2"><i class="fas fa-download mr-1"></i>Descargas</h6>';
    html += '<div class="btn-group">';
    
    if (data.pdf_url) {
        html += '<a href="' + data.pdf_url + '" target="_blank" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf mr-1"></i>Descargar PDF</a>';
    }
    
    if (data.xml_url) {
        html += '<a href="' + data.xml_url + '" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-file-code mr-1"></i>Descargar XML</a>';
    }
    
    if (data.encrypted_billid) {
        html += '<a href="javascript:;" class="btn btn-success btn-sm" onclick="viewElectronic(\'' + data.encrypted_billid + '\')"><i class="fas fa-eye mr-1"></i>Ver Factura</a>';
    }
    
    html += '</div>';
    html += '</div>';
    
    // Detalle de items
    if (data.items && data.items.length > 0) {
        html += '<div class="mb-3">';
        html += '<h6 class="text-muted mb-2"><i class="fas fa-list mr-1"></i>Detalle de Items</h6>';
        html += '<table class="table table-sm table-bordered">';
        html += '<thead class="thead-light"><tr>';
        html += '<th>Descripción</th>';
        html += '<th class="text-center">Cantidad</th>';
        html += '<th class="text-right">Precio</th>';
        html += '<th class="text-right">Total</th>';
        html += '</tr></thead>';
        html += '<tbody>';
        
        for (let i = 0; i < data.items.length; i++) {
            let item = data.items[i];
            html += '<tr>';
            html += '<td>' + item.description + '</td>';
            html += '<td class="text-center">' + item.quantity + '</td>';
            html += '<td class="text-right">' + currency_symbol + ' ' + formatNumber(item.price) + '</td>';
            html += '<td class="text-right">' + currency_symbol + ' ' + formatNumber(item.total) + '</td>';
            html += '</tr>';
        }
        
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
    }
    
    // Totales
    html += '<div class="row">';
    html += '<div class="col-md-6 offset-md-6">';
    html += '<table class="table table-sm table-bordered">';
    html += '<tr><td class="font-weight-bold">Subtotal</td><td class="text-right">' + data.subtotal_formatted + '</td></tr>';
    
    if (parseFloat(data.bill_discount) > 0) {
        html += '<tr><td class="font-weight-bold">Descuento</td><td class="text-right">- ' + data.discount_formatted + '</td></tr>';
    }
    
    html += '<tr class="table-primary"><td class="font-weight-bold">TOTAL</td><td class="text-right font-weight-bold">' + data.total_formatted + '</td></tr>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    
    // Pagos realizados
    if (data.payments && data.payments.length > 0) {
        html += '<div class="mb-3">';
        html += '<h6 class="text-muted mb-2"><i class="fas fa-credit-card mr-1"></i>Pagos Realizados</h6>';
        html += '<table class="table table-sm table-bordered">';
        html += '<thead class="thead-light"><tr>';
        html += '<th>Fecha</th>';
        html += '<th>Tipo Pago</th>';
        html += '<th class="text-right">Monto</th>';
        html += '<th>Observación</th>';
        html += '</tr></thead>';
        html += '<tbody>';
        
        for (let i = 0; i < data.payments.length; i++) {
            let payment = data.payments[i];
            html += '<tr>';
            html += '<td>' + formatDate(payment.payment_date) + '</td>';
            html += '<td>' + payment.payment_type + '</td>';
            html += '<td class="text-right">' + currency_symbol + ' ' + formatNumber(payment.amount_paid) + '</td>';
            html += '<td>' + (payment.comment || '-') + '</td>';
            html += '</tr>';
        }
        
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
    }
    
    document.getElementById('modal-detail-body').innerHTML = html;
    document.getElementById('modal-detail-title').textContent = data.type_label + ' - ' + data.document_number;
    
    $('#modal-detail').modal('show');
}

/**
 * Ver factura electrónica
 */
function viewElectronic(encryptedId) {
    window.open(base_url + '/electronicinvoice/view_electronic/' + encryptedId, '_blank');
}

/**
 * Ver nota crédito electrónica
 */
function viewCreditNote(encryptedId) {
    window.open(base_url + '/electronicinvoice/view_credit_note/' + encryptedId, '_blank');
}

/**
 * Exportar a Excel
 */
function exportExcel() {
    let start = document.getElementById('start').value;
    let end = document.getElementById('end').value;
    let state = document.getElementById('listStates').value;
    let type = document.getElementById('listTypes').value;
    
    let url = base_url + '/electronicreports/export?start=' + start + '&end=' + end;
    
    if (state !== '0') {
        url += '&state=' + state;
    }
    
    if (type !== '0') {
        url += '&type=' + type;
    }
    
    window.open(url, '_blank');
}

/**
 * Formatear número
 */
function formatNumber(number) {
    return parseFloat(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

/**
 * Formatear fecha
 */
function formatDate(dateStr) {
    if (!dateStr) return '-';
    let date = new Date(dateStr);
    let day = String(date.getDate()).padStart(2, '0');
    let month = String(date.getMonth() + 1).padStart(2, '0');
    let year = date.getFullYear();
    return day + '/' + month + '/' + year;
}
