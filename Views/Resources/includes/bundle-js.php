<?php
/**
 * JS Bundle - Combina todos los JS en un solo archivo
 * Se ejecuta una vez y cachea el resultado
 */

$jsDir = __DIR__ . '/../Assets/js';
$bookstoresDir = __DIR__ . '/../Assets/bookstores';
$outputFile = $jsDir . '/bundle.min.js';
$cacheTime = 3600; //1 hora

// Verificar si el bundle existe y es reciente
if (file_exists($outputFile) && (time() - filemtime($outputFile)) < $cacheTime) {
    header('Content-Type: application/javascript');
    header('Cache-Control: public, max-age=31536000');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    readfile($outputFile);
    exit;
}

// Lista de JS a combinar (orden importante)
$jsFiles = [
    $jsDir . '/moment.min.js',
    $jsDir . '/functions.js',
    $jsDir . '/utils.min.js',
    $jsDir . '/initial.min.js',
    $jsDir . '/theme/default.min.js',
    $jsDir . '/jquery-confirm.min.js',
    $jsDir . '/jquery.bootstrap-touchspin.min.js',
    $jsDir . '/datatables.min.js',
    $bookstoresDir . '/jszip/jszip.min.js',
    $bookstoresDir . '/pdfmake/pdfmake.min.js',
    $bookstoresDir . '/pdfmake/vfs_fonts.js',
    $bookstoresDir . '/select2/js/select2.min.js',
    $bookstoresDir . '/parsleyjs/parsley.js',
    $bookstoresDir . '/smartwizard/js/jquery.smartWizard.js',
    $bookstoresDir . '/gritter/js/jquery.gritter.min.js',
    $bookstoresDir . '/jquery.maskedinput/jquery.maskedinput.js',
    $bookstoresDir . '/chartjs/js/chart.min.js',
    $bookstoresDir . '/lightbox/js/lightbox.min.js',
    $bookstoresDir . '/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',
    $bookstoresDir . '/axios/axios.min.js',
    $jsDir . '/ui-enhancements.js',
];

$combined = '';
foreach ($jsFiles as $file) {
    if (file_exists($file)) {
        $combined .= file_get_contents($file) . ";\n";
    }
}

// Guardar el bundle
file_put_contents($outputFile, $combined);

// Enviar
header('Content-Type: application/javascript');
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
echo $combined;
