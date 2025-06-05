<?php
// includes/ai_utils.php

if (!defined('AI_UTILS_LOADED')) {
    define('AI_UTILS_LOADED', true);
}

/**
 * Placeholder para una función que generaría un resumen inteligente.
 * En una implementación real, esto podría llamar a una API de IA,
 * procesar el texto localmente con un modelo, etc.
 *
 * @param string $content_key Un identificador para el contenido a resumir,
 *                            o potencialmente el texto completo si es corto.
 * @param string $full_text (Opcional) El texto completo a resumir.
 * @return string El resumen generado (o un placeholder).
 */
function get_smart_summary_placeholder(string $content_key, string $full_text = ''): string {
    // Simulación de procesamiento
    $summary = "Resumen inteligente para '" . htmlspecialchars($content_key) . "': ";

    if (!empty($full_text)) {
        // Tomar las primeras ~250 caracteres como un resumen muy básico si se proporciona el texto completo.
        // strip_tags para evitar problemas si el contenido tiene HTML.
        $summary .= substr(strip_tags($full_text), 0, 250) . "...";
    } else {
        $summary .= "Este es un resumen de demostración generado por IA. En el futuro, aquí aparecería un extracto conciso y relevante del contenido principal, procesado por un modelo de lenguaje avanzado para destacar los puntos clave de la sección sobre " . htmlspecialchars($content_key) . ".";
    }

    return "<p><strong>" . $summary . "</strong></p><p><em>(Funcionalidad de resumen real con IA pendiente de implementación completa).</em></p>";
}

/**
 * Placeholder para una función que generaría etiquetas sugeridas por IA.
 *
 * @param string $content_key Un identificador para el contenido a etiquetar.
 * @return array Un array de strings con las etiquetas sugeridas.
 */
function get_suggested_tags_placeholder(string $content_key): array {
    // Simulación basada en content_key
    if ($content_key === 'atapuerca' || $content_key === 'Contenido de Atapuerca') {
        return ['Prehistoria', 'Evolución Humana', 'Arqueología', 'Yacimientos UNESCO', 'Homo Antecessor', 'Burgos', 'Paleontología'];
    }

    // Etiquetas por defecto para otros contenidos no especificados
    return ['General', 'Contenido Interesante', 'Historia', 'Web'];
}

/**
 * Placeholder para una función que simularía una traducción inteligente.
 *
 * @param string $content_id Identificador del contenido (ej. 'atapuerca_main_text').
 * @param string $target_language Código del idioma objetivo (ej. 'en-ai', 'fr-ai').
 * @param string $original_sample_text Un extracto del texto original para incluir en la demo. O el texto completo si se desea devolverlo para 'es'.
 * @return string El texto "traducido" de demostración o el texto original si target_language es 'es'.
 */
function get_simulated_translation_placeholder(string $content_id, string $target_language, string $original_sample_text = ''): string {
    if ($target_language === 'es') {
        // Si el objetivo es español, se asume que se quiere restaurar el original.
        // El JavaScript debería tener el contenido original completo.
        // Esta función, si es llamada con 'es', simplemente devuelve el sample/original que se le pasó.
        return $original_sample_text;
    }

    $original_snippet = !empty($original_sample_text) ? htmlspecialchars(substr(strip_tags($original_sample_text), 0, 70)) . "..." : "el contenido original";

    $outputText = "<div style='padding:15px; background-color:#e3f2fd; border:1px solid #bbdefb; border-radius:4px; margin-top:10px;'>";
    $outputText .= "<p style='font-size:0.9em; color:#0d47a1;'><em>Traducción IA (Demostración) para: " . htmlspecialchars($content_id) . "</em></p>";

    switch ($target_language) {
        case 'en-ai':
            $outputText .= "<p><strong>Simulated English Translation:</strong> This demonstrates where AI-generated English text would appear. The original Spanish content started with: '<em>" . $original_snippet . "</em>'.</p>";
            $outputText .= "<p>In a production system, the full text would be processed by an advanced neural machine translation model to provide an accurate and nuanced English version.</p>";
            break;
        case 'fr-ai':
            $outputText .= "<p><strong>Traduction Française Simulée :</strong> Ceci montre où le texte français généré par l'IA apparaîtrait. Le contenu original en espagnol commençait par : '<em>" . $original_snippet . "</em>'.</p>";
            $outputText .= "<p>Dans un système de production, le texte intégral serait traité par un modèle avancé de traduction automatique neuronale pour fournir une version française précise et nuancée.</p>";
            break;
        // No hay caso 'default' o 'es' aquí porque ya se manejó al inicio de la función.
    }
    $outputText .= "<p style='font-size:0.8em; color:#1976d2; margin-top:10px;'><em>(Esta es una simulación. La funcionalidad de traducción real con IA está pendiente de implementación).</em></p>";
    $outputText .= "</div>";
    return $outputText;
}

?>
