<?php
/**
 * CSS Bundle - Combina todos los CSS en un solo archivo
 * Se ejecuta una vez y cachea el resultado
 */

$cssDir = __DIR__ . '/../Assets/css';
$bookstoresDir = __DIR__ . '/../Assets/bookstores';
$outputFile = $cssDir . '/bundle.min.css';
$cacheTime = 3600; //1 hora

// Verificar si el bundle existe y es reciente
if (file_exists($outputFile) && (time() - filemtime($outputFile)) < $cacheTime) {
    header('Content-Type: text/css');
    header('Cache-Control: public, max-age=31536000');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    readfile($outputFile);
    exit;
}

// Lista de CSS a combinar
$cssFiles = [
    $cssDir . '/default/app.min.css',
    $cssDir . '/datatables.min.css',
    $cssDir . '/superwisp.css',
    $cssDir . '/jquery-confirm.min.css',
    $bookstoresDir . '/simple-line-icons/css/simple-line-icons.css',
    $bookstoresDir . '/ionicons/css/ionicons.min.css',
    $bookstoresDir . '/gritter/css/jquery.gritter.css',
    $bookstoresDir . '/select2/css/select2.min.css',
    $bookstoresDir . '/smartwizard/css/smart_wizard.css',
    $bookstoresDir . '/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
    $bookstoresDir . '/lightbox/css/lightbox.css',
    $cssDir . '/modern.css',
    $cssDir . '/ui-enhancements.css',
];

$combined = '';
foreach ($cssFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Corregir rutas relativas de imágenes y fuentes
        $content = preg_replace('/url\((?!data:|https?:|\/)/i', 'url(../', $content);
        $combined .= $content . "\n";
    }
}

// Guardar el bundle
file_put_contents($outputFile, $combined);

// Enviar
header('Content-Type: text/css');
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
echo $combined;
