let table_pending;
let table_name_pending = "list-bills-pendings";
let bills = [];
let number_client =  document.querySelector("#massive_bill_number_client");
$(document).on('click', 'body', function(e) {
  document.querySelector(".search-input").classList.remove("active");
  document.querySelector('#box-search').innerHTML = "";
});
number_client.onpaste = function(event){
  var str = event.clipboardData.getData('text/plain');
  matches = str.match(/\d+/g);
  var v1,v2,v3,v4;
  if(!matches[0]){
    v1 = "";
  }else{
    if(matches[0] == "57"){
      v1 = "";
    }else{
      v1 = matches[0];
    }
  }
  if(!matches[1]){
    v2 = "";
  }else{
    v2 = matches[1];
  }
  if(!matches[2]){
    v3 = "";
  }else{
    v3 = matches[2];
  }
  if(!matches[3]){
    v4 = "";
  }else{
    v4 = matches[3];
  }
  var values = v1+v2+v3+v4;
  $(this).val(values);
  event.preventDefault();
};
function massive_runway(){
  if(document.querySelector('#typepay')){
    var ajaxUrl = base_url+'/runway/list_runway';
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
      if(request.readyState == 4 && request.status == 200){
        document.querySelector('#typepay').innerHTML = request.responseText;
        $('#typepay').select2();
      }
    }
  }
}
$("#search_client").keyup(function (){
  let search = $(this).val();
  if(search.length > 0){
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/payments/search_clients';
    var strData = "search="+search;
    request.open("POST",ajaxUrl,true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(strData);
    request.onreadystatechange = function(){
      if(request.readyState == 4 && request.status == 200){
        document.querySelector(".search-input").classList.add("active");
        document.querySelector('#box-search').innerHTML = request.responseText;
      }
    }
  }else{
    document.querySelector(".search-input").classList.remove("active");
    document.querySelector('#box-search').innerHTML = "";
  }
});
function pending_invoices(idclient){
  var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  var ajaxUrl = base_url+'/payments/pending_invoices/'+idclient;
  request.open("GET",ajaxUrl,true);
  request.send();
  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      var objData = JSON.parse(request.responseText);
      if(objData.status == "success"){
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector('#box-search').innerHTML = "";
        document.querySelector('#search_client').value = "";
        $('#pending_invoices').slideUp().html(objData.data.views).slideDown();
        massive_runway();
        table_pendings(objData.data.client.id);
        $('#date_time_mass').datetimepicker({locale:'es'});
        $('#date_time_mass').val(moment().format('DD/MM/YYYY H:mm'));
        document.querySelector('#transactions').addEventListener('submit',save_payment,false);
      }else{
        alert_msg("error",objData.msg);
        $('#pending_invoices').slideUp().empty();
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector('#box-search').innerHTML = "";
        document.querySelector('#search_client').value = "";
      }
    }
  }
}
function table_pendings(idclient){
  table_configuration('#'+table_name_pending,'Facturas pendientes');
  table_pending = $('#'+table_name_pending).DataTable({
    "ajax":{
      "url": " "+base_url+"/payments/list_pendings/"+idclient,
      "dataSrc": ""
    },
    'stripeClasses': ['stripe1', 'stripe2'],
    "deferRender": true,
    "rowId": 'id',
    "columns": [
      {"data":"invoice",'className':'text-center'},
      {"data":"billing"},
      {"data":"balance","render": function(data,type,full,meta){
        return state = '<span class="text-danger f-w-700">'+data+'</span>';
      },'className':'text-center'},
      {"data":"total",'className':'text-center'},
      {"data": "date_issue","render": function(data,type,full,meta){
        return moment(data).format('DD/MM/YYYY');
      },'className':'text-center'},
      {"data": "expiration_date","render": function(data,type,full,meta){
        return moment(data).format('DD/MM/YYYY');
      },'className':'text-center'},
      {"data":"state","render": function(data,type,full,meta){
        var state = '';
        if(data == 1){
          state = '<span class="label label-success">PAGADO</span>';
        }
        if(data == 2){
          state = '<span class="label label-warning">PENDIENTE</span>';
        }
        if(data == 3){
          state = '<span class="label label-danger">VENCIDO</span>';
        }
        if(data == 4){
          state = '<span class="label label-dark">ANULADO</span>';
        }
        return state;
      },'className':'text-center'}
    ],
    initComplete: function(oSettings, json){
      $('#'+table_name_pending+'_wrapper .dt-buttons').append($('#'+table_name_pending+'-btns-exportable').contents());
    }
  }).on('processing.dt', function (e, settings, processing) {
    if(processing){
      loaderin('.panel-bills-pendings');
    }else{
      loaderout('.panel-bills-pendings');
    }
  }).on( 'draw', function (){})
}
function save_payment(e){
  e.preventDefault();
  let total = document.querySelector('#total_pay').value;
  if(total <= 0 || total == ""){
    alert_msg("error","El monto a pagar debe ser mayor a 0");
    return false;
  }
  loading.style.display = "flex";
  var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  var ajaxUrl = base_url+'/payments/mass_payments';
  var transactions = document.querySelector("#transactions");
  var formData = new FormData(transactions);
  request.open("POST",ajaxUrl,true);
  request.send(formData);
  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      var objData = JSON.parse(request.responseText);
      if(objData.status == "success"){
        alert_msg("success",objData.msg);
        $('#pending_invoices').slideUp().empty();
        $('#modal-massive-voucher').modal('show');
        document.querySelector('#text-title-massive-voucher').innerHTML = "Opciones de impresión";
        document.querySelector('#massive_text_country').innerHTML = "+"+objData.country;
        document.querySelector('#massive_country_code').value = objData.country;
        document.querySelector('#massive_bill_number_client').value = objData.mobile;
        document.querySelector('#massive_client').value = objData.client;
        document.querySelector('#massive_current_paid').value = objData.current_paid;
        objData.bills.forEach((bill) => {
          bills.push(bill);
        });
      }else if(objData.status == "warning"){
        alert_msg("warning",objData.msg);
        $('#pending_invoices').slideUp().empty();
      }else{
        alert_msg("error",objData.msg);
        $('#pending_invoices').slideUp().empty();
      }
    }
    loading.style.display = "none";
    return false;
  }
}
function validate_amount(amount){
  var current = $("#total_pay").val();
  if(amount < current){
    alert_msg("warning","El monto supera la deuda.");
    document.querySelector("#total_pay").value = 0;
  }
  if(current <= 0){
    alert_msg("error","El monto no puede ser negativo");
    document.querySelector("#total_pay").value = 0;
  }
}
/* FORMAS DE IMRPESION */
$('#btn-massive-ticket').on('click',function(){
  redirect_by_post(base_url+'/payments/massive_pdfs',bills,true);
});
$('#btn-massive-print_ticket').on('click',function(){
  redirect_by_post(base_url+'/payments/massive_impressions',bills,true);
});
function redirect_by_post(route,array,in_new_tab){
  array = (typeof array == 'undefined') ? {} : array;
  in_new_tab = (typeof in_new_tab == 'undefined') ? true : in_new_tab;
  var form = document.createElement("form");
  $(form).attr("id","transactions").attr("name","transactions").attr("action",route).attr("method","post").attr("enctype", "multipart/form-data");
  if(in_new_tab){
    $(form).attr("target","_blank");
  }
  array.forEach((bill) => {
    $(form).append('<input type="text" name="ids[]" value="'+bill+'"/>');
  });
  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
  return false;
}
$('.btn-close').on('click',function(){
  bills = [];
});
/* MSJ whatsapp */
$('#btn-massive-msg').on('click',function(){
  let cell = document.querySelector('#massive_bill_number_client').value;
  if(cell !== ""){
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/payments/massive_msj';
    var formData = new FormData();
    formData.append('ids',bills);
    request.open("POST",ajaxUrl,true);
    request.send(formData);
    request.onreadystatechange = function(){
      if(request.readyState == 4 && request.status == 200){
        var objData = JSON.parse(request.responseText);
        if(objData.status == "success"){
          let country = document.querySelector('#massive_country_code').value;
          let client = document.querySelector('#massive_client').value;
          let current_paid = document.querySelector('#massive_current_paid').value;
          let message = `Hola, se registro *${objData.data.business.symbol+roundNumber(current_paid,2)}*, al recibo de `;
          objData.data.bills.forEach((months) => {
            if(months.type == 1){
              message += `,`;
            }else{
              var dateAr = months.billed_month.split('-');
              var month = dateAr[1];
              var year = dateAr[0];
              message += `*${month_letters(month).toUpperCase()}*,`;
            }
          });
           message += `cliente ${client}. Muchas gracias por su pago. Atte. *${objData.data.business.business_name}*. verifícalo en el enlace: `;
          objData.data.bills.forEach((bill) => {
            var route = base_url+"/invoice/document/"+bill.encrypt_id;
            message += `${route}%0A `;
          });
          let url = "https://api.whatsapp.com/send/?phone="+country+cell+"&text="+message;
          window.open(url);
        }else{
          alert_msg("error",objData.msg);
        }
      }
    }
  }else{
    alert_msg("info","El número es obligatorio.")
  }
});
