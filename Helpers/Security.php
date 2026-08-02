<?php
/**
 * Security Helper - INTERNET SYSTEM
 * Funciones de seguridad centralizadas
 */

/* ============================================================
 * BCRYPT - Hashing de contraseñas
 * ============================================================ */

/**
 * Genera un hash bcrypt de una contraseña
 */
function hash_password(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verifica una contraseña contra su hash bcrypt
 */
function verify_password(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Verifica si el hash necesita rehash (para actualizaciones futuras)
 */
function needs_rehash(string $hash): bool {
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Detecta si un hash es bcrypt (para migración)
 */
function is_bcrypt(string $hash): bool {
    return substr($hash, 0, 4) === '$2y$';
}

/* ============================================================
 * CSRF TOKEN
 * ============================================================ */

/**
 * Genera un token CSRF y lo almacena en sesión
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida el token CSRF
 */
function validate_csrf_token(string $token): bool {
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Genera un campo hidden input con el token CSRF
 */
function csrf_field(): string {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Meta tag CSRF para AJAX requests
 */
function csrf_meta(): string {
    $token = generate_csrf_token();
    return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
}

/**
 * Valida CSRF en requests POST
 * Retorna true si es válido, false si no
 * Para requests AJAX, valida desde header o POST data
 * Para requests normales, valida desde POST data
 */
function verify_csrf(): bool {
    // Solo aplicar a POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true;
    }
    
    // Obtener token del header AJAX o del POST data
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (empty($token)) {
        return false;
    }
    
    return validate_csrf_token($token);
}

/**
 * Middleware CSRF - detiene ejecución si el token no es válido
 * Usar al inicio de endpoints POST que requieren protección
 */
function csrf_protect(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verify_csrf()) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode([
            'status' => 'error', 
            'msg' => 'Token de seguridad inválido. Recargue la página.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Obtiene el token CSRF actual (para usar en JavaScript)
 */
function get_csrf_token(): string {
    return generate_csrf_token();
}

/* ============================================================
 * RATE LIMITING
 * ============================================================ */

/**
 * Verifica y registra intentos de login
 * Retorna: true si permitido, false si bloqueado
 */
function check_rate_limit(string $identifier, int $max_attempts = 5, int $window_seconds = 900): bool {
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.json';
    
    $attempts = [];
    if (file_exists($file)) {
        $data = file_get_contents($file);
        $attempts = json_decode($data, true) ?: [];
    }
    
    $now = time();
    
    // Limpiar intentos fuera de la ventana de tiempo
    $attempts = array_filter($attempts, function($timestamp) use ($now, $window_seconds) {
        return ($now - $timestamp) < $window_seconds;
    });
    
    if (count($attempts) >= $max_attempts) {
        return false; // Bloqueado
    }
    
    return true; // Permitido
}

/**
 * Registra un intento fallido
 */
function record_failed_attempt(string $identifier): void {
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.json';
    
    $attempts = [];
    if (file_exists($file)) {
        $data = file_get_contents($file);
        $attempts = json_decode($data, true) ?: [];
    }
    
    $attempts[] = time();
    
    file_put_contents($file, json_encode($attempts));
}

/**
 * Limpia los intentos después de login exitoso
 */
function clear_rate_limit(string $identifier): void {
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.json';
    if (file_exists($file)) {
        unlink($file);
    }
}

/**
 * Obtiene el tiempo restante de bloqueo en segundos
 */
function get_rate_limit_remaining(string $identifier, int $window_seconds = 900): int {
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier) . '.json';
    
    if (!file_exists($file)) {
        return 0;
    }
    
    $data = file_get_contents($file);
    $attempts = json_decode($data, true) ?: [];
    
    if (empty($attempts)) {
        return 0;
    }
    
    $oldest = min($attempts);
    $remaining = $window_seconds - (time() - $oldest);
    
    return max(0, $remaining);
}

/* ============================================================
 * SANITIZACIÓN MEJORADA
 * ============================================================ */

/**
 * Limpia una cadena de entrada de forma segura
 */
function sanitize_string(string $input): string {
    // Eliminar espacios extra
    $input = preg_replace('/\s+/', ' ', $input);
    $input = trim($input);
    
    // Eliminar etiquetas HTML peligrosas
    $input = strip_tags($input, '<p><br><strong><em><a><ul><ol><li>');
    
    // Convertir caracteres especiales HTML
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

/**
 * Limpia un email
 */
function sanitize_email(string $email): string {
    $email = strtolower(trim($email));
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return $email;
}

/**
 * Limpia un número
 */
function sanitize_number($input, bool $allow_float = false): float|int {
    $input = trim($input);
    if ($allow_float) {
        return filter_var($input, FILTER_VALIDATE_FLOAT) ?: 0;
    }
    return filter_var($input, FILTER_VALIDATE_INT) ?: 0;
}

/**
 * Valida que un input no esté vacío y cumple formato
 */
function validate_input(string $input, string $type = 'string'): bool {
    if (empty(trim($input))) {
        return false;
    }
    
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        case 'url':
            return filter_var($input, FILTER_VALIDATE_URL) !== false;
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT) !== false;
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
        default:
            return strlen(trim($input)) > 0;
    }
}

/* ============================================================
 * LOGGING DE SEGURIDAD
 * ============================================================ */

/**
 * Registra eventos de seguridad
 */
function log_security_event(string $event, string $details = '', string $severity = 'INFO'): void {
    $log_dir = __DIR__ . '/../Logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/security_' . date('Y-m-d') . '.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $user_id = $_SESSION['idUser'] ?? 'guest';
    
    $log_entry = "[{$timestamp}] [{$severity}] [{$event}] IP: {$ip} | User: {$user_id} | UA: {$user_agent} | {$details}\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/* ============================================================
 * VALIDACIÓN DE SESIÓN
 * ============================================================ */

/**
 * Regenera el ID de sesión por seguridad
 */
function regenerate_session_id(): void {
    session_regenerate_id(true);
}

/**
 * Valida que la sesión sea válida y actualiza timestamp
 */
function validate_session(): bool {
    if (empty($_SESSION['login'])) {
        return false;
    }
    
    // Verificar timeout de sesión (30 minutos)
    $timeout = 1800;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    
    // Regenerar ID de sesión cada 30 minutos
    if (empty($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    return true;
}

/**
 * Cierra la sesión de forma segura
 */
function secure_logout(): void {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/* ============================================================
 * RESPUESTAS JSON SEGURAS
 * ============================================================ */

/**
 * Envía una respuesta JSON limpia (sin warnings PHP)
 * Previene el error "Invalid JSON response" de DataTables
 */
function send_json($data, int $options = JSON_UNESCAPED_UNICODE): void {
    // Suprimir manejo de errores para evitar output PHP
    $old_error_handler = set_error_handler(function($errno, $errstr, $errfile, $errline) {
        // Registrar en log pero no mostrar
        error_log("[JSON Response] {$errstr} in {$errfile} on line {$errline}");
        return true; // Prevenir que PHP muestre el error
    });
    
    // Limpiar cualquier buffer de salida previo
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Headers correctos
    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    
    // Suprimir errores durante la serialización JSON
    $json = @json_encode($data, $options);
    
    // Si json_encode falla, enviar error
    if ($json === false) {
        $json = json_encode(['status' => 'error', 'msg' => 'Error al procesar datos']);
    }
    
    // Restaurar handler de errores
    set_error_handler($old_error_handler);
    
    // Enviar JSON
    echo $json;
    exit;
}

/**
 * Envía respuesta de error JSON
 */
function send_json_error(string $message, int $code = 400): void {
    http_response_code($code);
    send_json(['status' => 'error', 'msg' => $message]);
}

/**
 * Envía respuesta de éxito JSON
 */
function send_json_success($data = null, string $message = 'OK'): void {
    $response = ['status' => 'success', 'msg' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    send_json($response);
}
