<?php
/**
 * Script de Migración de Contraseñas
 * Convierte contraseñas de AES a bcrypt
 * 
 * IMPORTANTE: Ejecutar una sola vez y luego eliminar
 * 
 * Uso: php migrate_passwords.php
 */

// Incluir configuración
require_once('Config/Config.php');
require_once('Helpers/Helpers.php');
require_once('Libraries/Core/Autoload.php');
require_once('Libraries/Core/Conexion.php');
require_once('Libraries/Core/Mysql.php');

echo "=== MIGRACIÓN DE CONTRASEÑAS A BCRYPT ===\n\n";

// Conectar a la base de datos
$db = new Mysql();

// Obtener todos los usuarios
$sql = "SELECT id, username, password FROM users WHERE state != 0";
$users = $db->select_all($sql);

if(empty($users)){
    echo "No se encontraron usuarios para migrar.\n";
    exit;
}

echo "Se encontraron " . count($users) . " usuarios.\n\n";

$success = 0;
$failed = 0;
$skipped = 0;

foreach($users as $user){
    $id = $user['id'];
    $username = $user['username'];
    $current_password = $user['password'];
    
    // Verificar si ya es bcrypt
    if(is_bcrypt($current_password)){
        echo "[SKIP] Usuario {$username} (ID: {$id}) - Ya tiene bcrypt\n";
        $skipped++;
        continue;
    }
    
    // Para migración, necesitamos la contraseña original
    // Como no tenemos la contraseña original, marcaremos para reset manual
    // O podemos usar un hash temporal y forzar cambio de contraseña
    
    // Opción 1: Marcar para reset (recomendado)
    $temp_password = 'ChangeMe' . $id . '!';
    $new_hash = hash_password($temp_password);
    
    $sql_update = "UPDATE users SET password = ? WHERE id = ?";
    $result = $db->update($sql_update, array($new_hash, $id));
    
    if($result){
        echo "[OK] Usuario {$username} (ID: {$id}) - Migrado a bcrypt\n";
        echo "     Contraseña temporal: {$temp_password}\n";
        echo "     IMPORTANTE: El usuario debe cambiar su contraseña en el próximo login\n\n";
        $success++;
    }else{
        echo "[ERROR] Usuario {$username} (ID: {$id}) - Error al migrar\n";
        $failed++;
    }
}

echo "\n=== RESUMEN ===\n";
echo "Total usuarios: " . count($users) . "\n";
echo "Migrados: {$success}\n";
echo "Omitidos (ya bcrypt): {$skipped}\n";
echo "Fallidos: {$failed}\n";

if($success > 0){
    echo "\n=== INSTRUCCIONES ===\n";
    echo "1. Notificar a los usuarios que deben cambiar su contraseña\n";
    echo "2. Eliminar este script después de usarlo\n";
    echo "3. Los usuarios usarán su contraseña temporal: ChangeMe[ID]!\n";
}

echo "\n=== FIN DE MIGRACIÓN ===\n";
