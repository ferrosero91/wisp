<?php
/* ============================================================
 * CONFIGURACION CENTRAL - INTERNET SYSTEM
 * Lee las variables desde el archivo .env (si existe).
 * Los valores por defecto actuan como fallback para no romper
 * el sistema cuando no hay archivo .env.
 * ============================================================ */

/* Cargar variables de entorno desde el archivo .env */
$__envFile = __DIR__ . "/.env";
if (!is_file($__envFile)) {
    $__envFile = __DIR__ . "/../.env";
}
if (is_file($__envFile)) {
    $__lines = file($__envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($__lines as $__line) {
        $__line = trim($__line);
        if ($__line === "" || strpos($__line, "#") === 0) {
            continue;
        }
        if (strpos($__line, "=") !== false) {
            list($__key, $__value) = explode("=", $__line, 2);
            $__key = trim($__key);
            $__value = trim($__value);
            putenv($__key . "=" . $__value);
            $_ENV[$__key] = $__value;
            $_SERVER[$__key] = $__value;
        }
    }
}

/* Helper para leer variables de entorno con valor por defecto */
function env($key, $default = null)
{
    $value = getenv($key);
    if ($value === false || $value === "") {
        $value = isset($_ENV[$key]) ? $_ENV[$key] : null;
    }
    return ($value === null || $value === "") ? $default : $value;
}

/* RUTA DEL SISTEMA */
define('BASE_URL', env('BASE_URL', "http://localhost:8080/"));
//const BASE_URL = "https://internet.mipaginaweb.pe";
/* ZONA HORARIA*/
date_default_timezone_set('America/Lima');
/* CONSTANTE DE CONEXION */
define('DB_HOST', env('DB_HOST', "localhost"));
define('DB_NAME', env('DB_NAME', "nacion17_internet"));
define('DB_USER', env('DB_USER', "root"));
define('DB_PASSWORD', env('DB_PASSWORD', ""));
define('DB_CHARSET', env('DB_CHARSET', "utf8"));
/* BACKUP */
define('TABLES_NAME', "Tables_in_superwisp"); //Tables_in_codigosf_superwisp
/* DESARROLLADOR */
define('DEVELOPER', "Nacional Code");
define('DEVELOPER_WEBSITE', "https://internet.nacionalcode.press/");
define('DEVELOPER_EMAIL', "ventas@nacionalcode.com");
define('DEVELOPER_MOBILE', "+57 3504931577");
/* SISTEMA */
define('NAME_SYSTEM', env('NAME_SYSTEM', "INTERNET SYSTEM"));
/* CONSTANTES DE ENCRIPTACION */
define('METHOD', "AES-256-CBC");
define('SECRET_KEY', env('SECRET_KEY', 'change-me-in-production'));
define('SECRET_IV', env('SECRET_IV', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'));
/* CONSTANTES DE MODULOS */
define('DASHBOARD', 1);
define('CLIENTS', 2);
define('USERS', 3);
define('TICKETS', 4);
define('INCIDENTS', 5);
define('BILLS', 6);
define('PRODUCTS', 7);
define('CATEGORIES', 8);
define('SUPPLIERS', 9);
define('PAYMENTS', 10);
define('SERVICES', 11);
define('BUSINESS', 12);
define('INSTALLATIONS', 13);
define('CURRENCYS', 14);
define('RUNWAY', 15);
define('VOUCHERS', 16);
define('UNITS', 17);
define('EMAIL', 18);
/* DELIMITADORES */
define('SPD', ".");
define('SPM', ",");
/* USUARIOS */
define('ADMINISTRATOR', 1);
define('TECHNICAL', 2);
define('CHARGES', 3);

/* ============================================================
 * FACTURACION ELECTRONICA (APIDIAN)
 * ============================================================ */
define('APIDIAN_URL', env('APIDIAN_URL', ''));
define('APIDIAN_TOKEN', env('APIDIAN_TOKEN', ''));
define('APIDIAN_NIT', env('APIDIAN_NIT', ''));
define('APIDIAN_DV', env('APIDIAN_DV', ''));
define('APIDIAN_ENVIRONMENT', env('APIDIAN_ENVIRONMENT', 'habilitacion'));
define('APIDIAN_PREFIX', env('APIDIAN_PREFIX', 'SETP'));
define('APIDIAN_RESOLUTION', env('APIDIAN_RESOLUTION', ''));
define('APIDIAN_RESOLUTION_FROM', env('APIDIAN_RESOLUTION_FROM', 1));
define('APIDIAN_RESOLUTION_TO', env('APIDIAN_RESOLUTION_TO', 999999999));

/* Tipos de documento DIAN */
define('DIAN_INVOICE', 1);           // Factura electrónica
define('DIAN_CREDIT_NOTE', 4);       // Nota crédito
define('DIAN_DEBIT_NOTE', 5);        // Nota débito
define('DIAN_SUPPORT_DOCUMENT', 11); // Documento soporte

/* Estados de facturación electrónica */
define('EINVOICE_PENDING', 0);
define('EINVOICE_AUTHORIZED', 1);
define('EINVOICE_REJECTED', 2);

/* ============================================================
 * ENTORNO DE EJECUCION
 * ============================================================ */
define('APP_ENV', env('APP_ENV', "development"));
define('APP_DEBUG', env('APP_DEBUG', "true") === "true" ? true : false);

/* Manejo de errores: en produccion no mostrar, solo registrar */
if (APP_DEBUG) {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_NOTICE & ~E_WARNING);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . "/../error_log.php");
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_NOTICE & ~E_WARNING);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . "/../error_log.php");
}

/* Error handler personalizado para evitar output en JSON */
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $log_dir = __DIR__ . '/../Logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $log_file = $log_dir . '/php_errors_' . date('Y-m-d') . '.log';
    $message = date('Y-m-d H:i:s') . " [$errno] $errstr in $errfile on line $errline\n";
    file_put_contents($log_file, $message, FILE_APPEND | LOCK_EX);
    return true; // Prevenir output PHP
});

/* Exception handler para errores fatales */
set_exception_handler(function($exception) {
    $log_dir = __DIR__ . '/../Logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $log_file = $log_dir . '/php_errors_' . date('Y-m-d') . '.log';
    $message = date('Y-m-d H:i:s') . " [EXCEPTION] " . get_class($exception) . ": " . $exception->getMessage() . 
               " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    $message .= "Stack trace:\n" . $exception->getTraceAsString() . "\n\n";
    file_put_contents($log_file, $message, FILE_APPEND | LOCK_EX);
    
    // Si es una petición JSON o AJAX, enviar respuesta JSON
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $is_json = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    $wants_json = $is_ajax || $is_json;
    
    if ($wants_json) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'error', 'msg' => 'Error: ' . $exception->getMessage()]);
    } else {
        echo '<h1>Error del servidor</h1><p>' . htmlspecialchars($exception->getMessage()) . '</p>';
    }
    exit;
});

/* Endurecimiento de sesion */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_trans_sid', '0');
    if (PHP_VERSION_ID < 80400) {
        ini_set('session.sid_length', '64');
    }
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
}

/* Output buffering para evitar warnings en respuestas JSON */
if (!ob_get_level()) {
    ob_start();
}

/* Cabeceras de seguridad */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
if (APP_DEBUG === false) {
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'");
}
