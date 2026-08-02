<?php
/**
 * Sistema de caché simple basado en archivos
 * Para optimizar consultas frecuentes a la base de datos
 */
class Cache {
    private $cacheDir;
    private $defaultTTL;
    
    public function __construct(int $defaultTTL = 300) {
        $this->cacheDir = __DIR__ . '/../../cache/';
        $this->defaultTTL = $defaultTTL;
        
        // Crear directorio de caché si no existe
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Obtiene un valor del caché
     */
    public function get(string $key) {
        $file = $this->getCacheFile($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = file_get_contents($file);
        if ($data === false) {
            return null;
        }
        
        $cached = unserialize($data);
        
        // Verificar si ha expirado
        if (time() > $cached['expires']) {
            $this->delete($key);
            return null;
        }
        
        return $cached['value'];
    }
    
    /**
     * Almacena un valor en el caché
     */
    public function set(string $key, $value, int $ttl = null): bool {
        $ttl = $ttl ?? $this->defaultTTL;
        $file = $this->getCacheFile($key);
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        return file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }
    
    /**
     * Elimina un valor del caché
     */
    public function delete(string $key): bool {
        $file = $this->getCacheFile($key);
        
        if (file_exists($file)) {
            return unlink($file);
        }
        
        return true;
    }
    
    /**
     * Limpia todo el caché
     */
    public function clear(): bool {
        $files = glob($this->cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
    
    /**
     * Verifica si existe un valor en el caché
     */
    public function has(string $key): bool {
        return $this->get($key) !== null;
    }
    
    /**
     * Obtiene o establece un valor (cache-aside pattern)
     */
    public function remember(string $key, int $ttl, callable $callback) {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Genera la ruta del archivo de caché
     */
    private function getCacheFile(string $key): string {
        return $this->cacheDir . md5($key) . '.cache';
    }
    
    /**
     * Obtiene estadísticas del caché
     */
    public function getStats(): array {
        $files = glob($this->cacheDir . '*.cache');
        $totalSize = 0;
        $validCount = 0;
        $expiredCount = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            $data = unserialize(file_get_contents($file));
            if (time() <= $data['expires']) {
                $validCount++;
            } else {
                $expiredCount++;
            }
        }
        
        return [
            'total_files' => count($files),
            'valid_files' => $validCount,
            'expired_files' => $expiredCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatSize($totalSize)
        ];
    }
    
    /**
     * Formatea el tamaño del archivo
     */
    private function formatSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
