<?php
function t(string $key): string {
    static $catalog = null;
    if ($catalog === null) {
        $lang = $_GET['lang'] ?? 'es';
        $path = __DIR__ . '/../i18n/' . $lang . '.json';
        if (!file_exists($path)) {
            $path = __DIR__ . '/../i18n/es.json';
        }
        $json = file_get_contents($path);
        $catalog = json_decode($json, true) ?: [];
    }
    return $catalog[$key] ?? $key;
}
