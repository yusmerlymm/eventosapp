<?php
// Script para limpiar OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache limpiado exitosamente\n";
} else {
    echo "⚠️ OPcache no está habilitado\n";
}

if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "📊 Estado de OPcache:\n";
    echo "- Habilitado: " . ($status['opcache_enabled'] ? 'Sí' : 'No') . "\n";
    echo "- Scripts en caché: " . $status['opcache_statistics']['num_cached_scripts'] . "\n";
    echo "- Memoria usada: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n";
}

echo "\n🔄 Por favor, recarga la página de crear evento ahora.\n";
