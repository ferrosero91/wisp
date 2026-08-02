<?php
    head($data);
    $bill = $data['bill']['bill'];
    $details = $data['bill']['detail'];
    $business = $data['bill']['business'];
    $ei = $data['electronic'];
    $payments = $data['bill']['payments'] ?? [];
    $resolution = $data['resolution'] ?? [];
    $tax_rate = floatval($business['tax_rate'] ?? 0);
    $subtotal = 0;
    $total_tax = 0;
    foreach($details as $d){
        $line = $d['quantity'] * $d['price'];
        $subtotal += $line;
        $total_tax += round($line * ($tax_rate / 100), 2);
    }
    $discount = floatval($bill['discount'] ?? 0);
    $total = $subtotal + $total_tax - $discount;
?>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #e9ecef; }
    
    .ticket-container {
        width: 300px;
        margin: 15px auto;
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        color: #000;
        line-height: 1.35;
    }
    .ticket-paper {
        background: #fff;
        padding: 12px 10px;
        border: 1px solid #ddd;
    }
    .t-center { text-align: center; }
    .t-bold { font-weight: bold; }
    .t-line { border-top: 1px dashed #000; margin: 6px 0; }
    .t-dline { border-top: 2px solid #000; margin: 6px 0; }
    .t-sm { font-size: 10px; }
    .t-xs { font-size: 9px; }
    .t-badge {
        display: inline-block;
        background: #000;
        color: #fff;
        padding: 1px 6px;
        font-size: 9px;
    }
    .t-table { width: 100%; border-collapse: collapse; }
    .t-table th {
        font-size: 10px;
        text-align: left;
        border-bottom: 1px solid #000;
        padding: 2px 0;
    }
    .t-table td {
        font-size: 10px;
        padding: 2px 0;
    }
    .t-table .r { text-align: right; }
    .t-total-row {
        display: flex;
        justify-content: space-between;
        padding: 1px 0;
    }
    .t-total-row.big {
        font-size: 13px;
        font-weight: bold;
        border-top: 2px solid #000;
        padding-top: 4px;
        margin-top: 4px;
    }
    .t-cufe {
        font-size: 8px;
        word-break: break-all;
        margin: 6px 0;
        padding: 4px;
        background: #f5f5f5;
        border: 1px dashed #999;
    }
    .t-qr { text-align: center; margin: 8px 0; }
    .t-qr img { width: 80px; height: 80px; }
    .t-legal {
        font-size: 8px;
        color: #333;
        text-align: justify;
        margin: 8px 0;
        line-height: 1.2;
    }
    .t-footer {
        text-align: center;
        font-size: 9px;
        color: #555;
        margin-top: 8px;
    }
    .t-cut {
        text-align: center;
        margin: 10px 0 5px;
        font-size: 10px;
        color: #999;
    }
    @media print {
        .no-print { display: none !important; }
        body { background: #fff; }
        .ticket-paper { box-shadow: none; border: none; }
        @page {
            margin: 0;
            size: 80mm auto;
        }
    }
</style>

<div class="no-print" style="width:300px; margin:10px auto; text-align:center;">
    <button onclick="window.print();" class="btn btn-sm btn-primary"><i class="fa fa-print mr-1"></i>Imprimir</button>
    <?php if(!empty($data['pdf_url'])){ ?>
    <a href="<?= $data['pdf_url'] ?>" target="_blank" class="btn btn-sm btn-danger ml-1"><i class="fa fa-file-pdf"></i> PDF</a>
    <?php } ?>
    <?php if(!empty($data['xml_url'])){ ?>
    <a href="<?= $data['xml_url'] ?>" target="_blank" class="btn btn-sm btn-success ml-1"><i class="fa fa-file-code"></i> XML</a>
    <?php } ?>
    <button onclick="sendInvoiceEmail()" class="btn btn-sm btn-info ml-1"><i class="fa fa-envelope"></i> Enviar</button>
    <a href="<?= base_url() ?>/bills" class="btn btn-sm btn-secondary ml-1"><i class="fa fa-arrow-left"></i></a>
</div>

<script>
function sendInvoiceEmail(){
    if(!confirm('¿Enviar factura electrónica por correo al cliente?')) return;
    
    var formData = new FormData();
    formData.append('idbill', '<?= encrypt($bill['id']) ?>');
    formData.append('email', '<?= $bill['email'] ?>');
    
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("POST", base_url+'/electronicinvoice/send_email', true);
    request.onload = function() {
        if(request.status === 200){
            try {
                var obj = JSON.parse(request.responseText);
                if(obj.status === 'success'){
                    alert('Correo enviado exitosamente');
                } else {
                    alert(obj.msg || 'Error al enviar correo');
                }
            } catch(e) {
                alert('Error al procesar respuesta');
            }
        } else {
            alert('Error del servidor');
        }
    };
    request.send(formData);
}
</script>

<div class="ticket-container">
<div class="ticket-paper">

    <!-- EMPRESA -->
    <div class="t-center t-bold" style="font-size:13px;"><?= strtoupper($business['business_name']) ?></div>
    <div class="t-center t-sm">
        <?= $business['documentid'] == 6 ? 'NIT' : 'CC' ?>: <?= $business['ruc'] ?><?= !empty($business['apidian_dv']) ? '-'.$business['apidian_dv'] : '' ?>
    </div>
    <div class="t-center t-sm"><?= $business['address'] ?></div>
    <div class="t-center t-sm">Tel: <?= $business['mobile'] ?></div>
    <div class="t-center t-sm"><?= $business['email'] ?></div>

    <div class="t-dline"></div>

    <!-- TIPO DOCUMENTO -->
    <div class="t-center t-bold" style="font-size:12px;">FACTURA ELECTRÓNICA DE VENTA</div>
    <div class="t-center t-bold" style="font-size:12px;"><?= $ei['prefix'] ?> - <?= str_pad($ei['number'], 7, "0", STR_PAD_LEFT) ?></div>
    <div class="t-center t-sm">Emisión: <?= date("d/m/Y H:i", strtotime($bill['date_issue'])) ?></div>
    <?php if(!empty($ei['created_at'])){ ?>
    <div class="t-center t-sm">DIAN: <?= date("d/m/Y H:i:s", strtotime($ei['created_at'])) ?></div>
    <?php } ?>
    <div class="t-center"><span class="t-badge">✓ AUTORIZADA</span></div>

    <div class="t-line"></div>

    <!-- RESOLUCIÓN -->
    <div class="t-center t-xs">
        <strong>Resolución DIAN:</strong> <?= $resolution['resolution_number'] ?? 'N/A' ?><br>
        <strong>Prefijo:</strong> <?= $ei['prefix'] ?> | <strong>Rango:</strong> <?= $resolution['consecutive_from'] ?? 'N/A' ?> - <?= $resolution['consecutive_to'] ?? 'N/A' ?><br>
        <strong>Vigencia:</strong> <?= !empty($resolution['date_from']) ? date("d/m/Y", strtotime($resolution['date_from'])) : 'N/A' ?> al <?= !empty($resolution['date_to']) ? date("d/m/Y", strtotime($resolution['date_to'])) : 'N/A' ?>
    </div>

    <div class="t-line"></div>

    <!-- CLIENTE -->
    <div class="t-xs"><strong>CLIENTE:</strong></div>
    <div class="t-sm t-bold"><?= strtoupper($bill['names'] . ' ' . $bill['surnames']) ?></div>
    <div class="t-xs"><?= $bill['type_doc'] ?>: <?= $bill['document'] ?></div>
    <div class="t-xs">Dir: <?= $bill['address'] ?></div>
    <div class="t-xs">Tel: <?= $bill['mobile'] ?></div>

    <div class="t-line"></div>

    <!-- DETALLE -->
    <table class="t-table">
        <thead>
            <tr>
                <th>DESCRIPCIÓN</th>
                <th style="text-align:center;">CANT</th>
                <th class="r">V.UNIT</th>
                <th class="r">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($details as $d): 
                $lt = $d['quantity'] * $d['price'];
            ?>
            <tr>
                <td><?= strtoupper($d['description']) ?></td>
                <td style="text-align:center;"><?= intval($d['quantity']) ?></td>
                <td class="r"><?= number_format($d['price'], 2, '.', ',') ?></td>
                <td class="r"><?= number_format($lt, 2, '.', ',') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="t-line"></div>

    <!-- IMPUESTOS -->
    <?php if($tax_rate > 0){ ?>
    <div class="t-xs">
        <strong>Impuestos:</strong> IVA <?= number_format($tax_rate, 0) ?>% = $ <?= number_format($total_tax, 2, '.', ',') ?>
    </div>
    <div class="t-line"></div>
    <?php } ?>

    <!-- TOTALES -->
    <div class="t-total-row">
        <span>SUBTOTAL:</span>
        <span>$ <?= number_format($subtotal, 2, '.', ',') ?></span>
    </div>
    <?php if($tax_rate > 0){ ?>
    <div class="t-total-row">
        <span>IVA (<?= number_format($tax_rate, 0) ?>%):</span>
        <span>$ <?= number_format($total_tax, 2, '.', ',') ?></span>
    </div>
    <?php } ?>
    <?php if($discount > 0){ ?>
    <div class="t-total-row">
        <span>DESCUENTO:</span>
        <span>-$ <?= number_format($discount, 2, '.', ',') ?></span>
    </div>
    <?php } ?>
    <div class="t-total-row big">
        <span>TOTAL:</span>
        <span>$ <?= number_format($total, 2, '.', ',') ?></span>
    </div>

    <div class="t-line"></div>

    <!-- LETRA -->
    <div class="t-center t-bold t-xs">
        SON: <?= strtoupper(numbers_letters(number_format($total, 2, '.', ''), $business['money'], $business['money_plural'])) ?>
    </div>

    <div class="t-line"></div>

    <!-- PAGO -->
    <div class="t-xs">
        <strong>PAGO:</strong> Contado | Efectivo<br>
        <strong>VENCE:</strong> <?= date("d/m/Y", strtotime($bill['expiration_date'])) ?>
    </div>

    <div class="t-line"></div>

    <!-- CUFE -->
    <div class="t-cufe">
        <strong>CUFE:</strong><br>
        <?= $ei['cufe'] ?? 'N/A' ?>
    </div>

    <!-- QR -->
    <?php if(!empty($ei['qr_string'])){ ?>
    <div class="t-qr">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= urlencode($ei['qr_string']) ?>" alt="QR">
    </div>
    <?php } ?>

    <!-- LEGAL -->
    <div class="t-legal">
        La presente Factura Electrónica de Venta es un título valor según Código de Comercio art. 621, 772 y 774. El comprador declara haber recibido real y materialmente los servicios descritos.
    </div>

    <div class="t-dline"></div>

    <!-- PIE -->
    <div class="t-footer">
        <em>Representación gráfica según normativa DIAN</em>
    </div>

    <div class="t-cut">✂ - - - - - - - - - - - - - - -</div>

</div>
</div>

<?php footer($data); ?>
