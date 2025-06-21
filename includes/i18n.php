<?php
/**
 * Retrieve a translation for the given key and language.
 * Falls back to the key itself when missing and records
 * untranslated keys for later processing.
 */
function t(string $key, ?string $lang = null): string {
    static $catalogs = [];

    $lang = $lang ?? ($_GET['lang'] ?? 'es');
    if (!isset($catalogs[$lang])) {
        $path = __DIR__ . '/../i18n/' . $lang . '.json';
        if (!file_exists($path)) {
            $path = __DIR__ . '/../i18n/es.json';
        }
        $json = file_get_contents($path);
        $catalogs[$lang] = json_decode($json, true) ?: [];
    }

    if (array_key_exists($key, $catalogs[$lang])) {
        return $catalogs[$lang][$key];
    }

    mark_untranslated($key, $lang);
    return $key;
}

/** @var array<string,array<string,bool>> */
$untranslated_keys = [];

function mark_untranslated(string $key, string $lang): void {
    global $untranslated_keys;
    if (!isset($untranslated_keys[$lang])) {
        $untranslated_keys[$lang] = [];
    }
    $untranslated_keys[$lang][$key] = true;
}

function _save_untranslated_keys(): void {
    global $untranslated_keys;
    if (empty($untranslated_keys)) {
        return;
    }

    $path = __DIR__ . '/../i18n/untranslated_keys.json';
    $existing = [];
    if (file_exists($path)) {
        $existing = json_decode(file_get_contents($path), true) ?: [];
    }

    foreach ($untranslated_keys as $lang => $keys) {
        if (!isset($existing[$lang])) {
            $existing[$lang] = [];
        }
        foreach ($keys as $k => $_) {
            $existing[$lang][$k] = true;
        }
    }

    file_put_contents($path, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

register_shutdown_function('_save_untranslated_keys');

function translate_or_lookup(string $key, string $es_text, string $target_lang): string {
    $lang = preg_replace('/-ai$/', '', $target_lang);
    $translation = t($key, $lang);
    if ($translation !== $key) {
        return $translation;
    }

    if (!function_exists('get_ai_translation')) {
        require_once __DIR__ . '/ai_utils.php';
    }
    return get_ai_translation($es_text, $lang);
}
