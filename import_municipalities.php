<?php
/**
 * Importar municipios desde CSV de APIDIAN
 */

require_once("Config/Config.php");

echo "=== Importando municipios desde APIDIAN ===\n\n";

try {
    $conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conexión exitosa\n\n";
} catch(PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}

// Limpiar tabla actual
$conexion->exec("TRUNCATE TABLE dian_municipalities");
echo "✓ Tabla dian_municipalities limpiada\n";

// Leer CSV
$csvFile = __DIR__ . "/apidian/public/csv/municipalities.csv";
if (!file_exists($csvFile)) {
    echo "✗ No se encontró el archivo CSV\n";
    exit(1);
}

$lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$count = 0;
$errors = 0;

$sql = "INSERT INTO dian_municipalities (id, department_id, name, code, codefacturador) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);

foreach ($lines as $line) {
    $parts = explode("\t", $line);
    if (count($parts) >= 5) {
        $id = intval($parts[0]);
        $department_id = intval($parts[1]);
        $name = trim($parts[2]);
        $code = trim($parts[3]);
        $codefacturador = trim($parts[4]);
        
        try {
            $stmt->execute([$id, $department_id, $name, $code, $codefacturador]);
            $count++;
        } catch(PDOException $e) {
            $errors++;
        }
    }
}

echo "✓ Municipios importados: $count\n";
if ($errors > 0) {
    echo "⚠ Errores: $errors\n";
}

// Verificar Arauca
$stmt = $conexion->query("SELECT * FROM dian_municipalities WHERE name LIKE '%Arauca%'");
$arauca = $stmt->fetch(PDO::FETCH_ASSOC);
if ($arauca) {
    echo "\n✓ Arauca encontrado: ID={$arauca['id']}, Code={$arauca['code']}\n";
}

echo "\n=== Importación completada ===\n";
