<?php
/**
 * Retrieve a translation for the given key and language.
 * Falls back to the key itself when missing and records
 * untranslated keys for later processing.
 */
function t(string $key, ?string $lang = null): string {
    static $catalogs = [];
    static $current_lang_resolved = null; // Para resolver el idioma solo una vez por petición si es necesario

    if ($lang === null) {
        if ($current_lang_resolved === null) {
            $valid_langs = ['es', 'en', 'gl'];
            $default_lang = 'es';
            $lang_candidate = $_GET['lang'] ?? $_SESSION['lang'] ?? $_COOKIE['lang'] ?? $default_lang;

            if (!in_array($lang_candidate, $valid_langs, true)) {
                $current_lang_resolved = $default_lang;
            } else {
                $current_lang_resolved = $lang_candidate;
            }

            // Actualizar sesión y cookie si el idioma se determina y es válido
            if (isset($_GET['lang']) && in_array($_GET['lang'], $valid_langs, true)) {
                 $_SESSION['lang'] = $_GET['lang'];
                 setcookie('lang', $_GET['lang'], ['expires' => time() + (86400 * 30), 'path' => '/', 'samesite' => 'Lax']);
            } elseif (!isset($_SESSION['lang']) && isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $valid_langs, true)) {
                $_SESSION['lang'] = $_COOKIE['lang'];
            } elseif(!isset($_SESSION['lang'])) { // Ni GET, ni Cookie, ni Sesión válida, usar default y poner en sesión
                 $_SESSION['lang'] = $default_lang;
            }
        }
        $lang = $current_lang_resolved;
    } else {
        // Si $lang se pasó explícitamente a la función, validarlo también
        $valid_langs_check = ['es', 'en', 'gl'];
        if (!in_array($lang, $valid_langs_check, true)) {
            $lang = 'es'; // Default si el $lang explícito no es válido
        }
    }

    // Asegurar que $lang (ahora $current_lang_resolved o el $lang validado) se use para la ruta
    $active_lang_for_path = $lang;

    if (!isset($catalogs[$active_lang_for_path])) {
        $path = __DIR__ . '/../i18n/' . $active_lang_for_path . '.json';
        if (!file_exists($path)) {
            // Fallback al idioma por defecto si el archivo del idioma actual no existe
            $path = __DIR__ . '/../i18n/' . ($default_lang ?? 'es') . '.json';
            $active_lang_for_path = ($default_lang ?? 'es'); // Actualizar el idioma activo si se usa fallback
        }

        $json_content = file_get_contents($path);
        if ($json_content === false) {
            error_log("Error al leer el archivo de idioma: " . $path);
            $catalogs[$active_lang_for_path] = [];
        } else {
            $catalogs[$active_lang_for_path] = json_decode($json_content, true) ?: [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Error al decodificar JSON del archivo de idioma: " . $path . " - Error: " . json_last_error_msg());
                $catalogs[$active_lang_for_path] = [];
            }
        }
    }

    // Usar $active_lang_for_path para la búsqueda en el catálogo
    if (array_key_exists($key, $catalogs[$active_lang_for_path] ?? [])) {
        $result = $catalogs[$active_lang_for_path][$key];
    } else {
        mark_untranslated($key, $active_lang_for_path);
        $result = $catalogs[$lang][$key];
    } else {
        mark_untranslated($key, $lang);
        $result = $key;
    }

    return str_replace('%YEAR%', date('Y'), $result);
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
