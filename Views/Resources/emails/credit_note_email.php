<?php
$data = $_SESSION['invoice_email_data'] ?? [];
$business = $data['business'] ?? [];
$ei = $data['ei'] ?? [];
$pdf_url = $data['pdf_url'] ?? '';
$xml_url = $data['xml_url'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #17a2b8; color: #fff; padding: 20px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; }
        .content { padding: 25px; }
        .info-box { background: #f8f9fa; border-left: 4px solid #17a2b8; padding: 15px; margin: 15px 0; }
        .buttons { text-align: center; margin: 20px 0; }
        .btn { display: inline-block; padding: 12px 25px; margin: 5px; text-decoration: none; border-radius: 5px; font-weight: bold; color: #fff; }
        .btn-pdf { background: #e74c3c; }
        .btn-xml { background: #27ae60; }
        .footer { background: #ecf0f1; padding: 15px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><?= strtoupper($business['business_name'] ?? 'Empresa') ?></h2>
            <p>Nota Crédito Electrónica</p>
        </div>
        <div class="content">
            <p>Estimado cliente,</p>
            <p>Adjuntamos su nota crédito electrónica autorizada por la DIAN, la cual modifica parcial o totalmente una factura anterior.</p>
            
            <div class="info-box">
                <strong>Documento:</strong> <?= $ei['prefix'] ?? '' ?> - <?= str_pad($ei['number'] ?? 0, 7, "0", STR_PAD_LEFT) ?><br>
                <strong>CUDE:</strong> <small><?= $ei['cufe'] ?? 'N/A' ?></small><br>
                <strong>Fecha:</strong> <?= !empty($ei['created_at']) ? date("d/m/Y H:i", strtotime($ei['created_at'])) : date("d/m/Y H:i") ?>
            </div>
            
            <p>Puede descargar los archivos oficiales haciendo clic en los siguientes enlaces:</p>
            
            <div class="buttons">
                <?php if(!empty($pdf_url)){ ?>
                <a href="<?= $pdf_url ?>" class="btn btn-pdf">📄 Descargar PDF</a>
                <?php } ?>
                <?php if(!empty($xml_url)){ ?>
                <a href="<?= $xml_url ?>" class="btn btn-xml">📋 Descargar XML</a>
                <?php } ?>
            </div>
            
            <p>Para verificar la autenticidad de este documento, puede escanear el código QR incluido en la nota crédito o consultar en el portal de la DIAN.</p>
        </div>
        <div class="footer">
            <p><?= $business['business_name'] ?? '' ?> | <?= $business['address'] ?? '' ?></p>
            <p>Tel: <?= $business['mobile'] ?? '' ?> | <?= $business['email'] ?? '' ?></p>
            <p><em>Este es un correo automático, por favor no responda.</em></p>
        </div>
    </div>
</body>
</html>
