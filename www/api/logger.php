<?php
// www/api/logger.php
// Logging simple a fichero con date, method, endpoint, user_id

function logApi(string $level, string $endpoint, string $message, ?int $userId = null): void
{
    $dir = __DIR__ . '/../logs';
    
    // Crear directorio si no existe
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $file = $dir . '/api.log';
    $time = date('Y-m-d H:i:s');
    $userPart = $userId ? " user_id={$userId}" : "";
    $line = "[$time] {$level} {$endpoint}{$userPart} - {$message}\n";
    
    // Escribir con lock para evitar concurrencia
    $fp = fopen($file, 'a');
    if ($fp) {
        flock($fp, LOCK_EX);
        fwrite($fp, $line);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
