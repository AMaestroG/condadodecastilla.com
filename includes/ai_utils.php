<?php
// includes/ai_utils.php

if (!defined('AI_UTILS_LOADED')) {
    define('AI_UTILS_LOADED', true);
}

// Require the Gemini API client
require_once __DIR__ . '/gemini_api_client.php';

/**
 * Generates a smart summary for the given text using the Gemini API.
 *
 * @param string $text_to_summarize The text to be summarized.
 * @param string $context Optional context to aid the summarization.
 * @return string The AI-generated summary or a fallback message on error.
 */
function get_smart_summary(string $text_to_summarize, string $context = ''): string {
    $prompt = "Por favor, resume el siguiente texto de manera concisa y clara.";
    if (!empty($context)) {
        $prompt .= " Contexto adicional: " . $context;
    }
    $prompt .= "\n\nTexto a resumir:\n\"" . $text_to_summarize . "\"";

    $summary = generate_text_gemini($prompt);

    return $summary ?? "No se pudo generar el resumen en este momento.";
}

/**
 * Suggests relevant tags for the given text using the Gemini API.
 *
 * @param string $text_for_tags The text to generate tags for.
 * @param int $max_tags Maximum number of tags to suggest.
 * @return array An array of suggested tags, or an empty array on error.
 */
function get_suggested_tags(string $text_for_tags, int $max_tags = 5): array {
    $prompt = "Sugiere un máximo de " . $max_tags . " etiquetas o palabras clave relevantes para el siguiente texto. ";
    $prompt .= "Devuelve las etiquetas como una lista simple separada por comas (por ejemplo: etiqueta1, etiqueta2, etiqueta3). ";
    $prompt .= "No incluyas numeración ni guiones en las etiquetas devueltas. Solo las etiquetas separadas por comas.";
    $prompt .= "\n\nTexto para etiquetar:\n\"" . $text_for_tags . "\"";

    $tags_string = generate_text_gemini($prompt);

    if ($tags_string) {
        // Remove any leading/trailing whitespace and split into an array
        $tags = array_map('trim', explode(',', $tags_string));
        // Filter out empty tags that might result from multiple commas, etc.
        return array_filter($tags);
    }

    return [];
}

/**
 * Translates text using the Gemini API.
 *
 * @param string $text_to_translate The text to be translated.
 * @param string $target_language_code The target language code (e.g., 'en' for English, 'fr' for French).
 * @param string $source_language_name The name of the source language (e.g., 'español', 'English').
 * @return string The translated text or a fallback error message.
 */
function get_ai_translation(string $text_to_translate, string $target_language_code, string $source_language_name = 'español'): string {
    // Basic validation for target language code
    if (empty($target_language_code) || strlen($target_language_code) > 10) {
        return "Código de idioma de destino no válido.";
    }

    // It's often better to let Gemini infer the target language by its name if codes are not standard for the model
    // However, providing a code can be more precise if the model supports it well.
    // For this prompt, we'll use a general approach.
    $prompt = "Traduce el siguiente texto de '" . $source_language_name . "' al idioma '" . $target_language_code . "'.";
    // Example of specifying target language by name if preferred:
    // $target_language_name = '';
    // switch ($target_language_code) {
    //     case 'en': $target_language_name = 'inglés'; break;
    //     case 'fr': $target_language_name = 'francés'; break;
    //     default: return "Idioma de destino no soportado para la traducción.";
    // }
    // $prompt = "Traduce el siguiente texto de '" . $source_language_name . "' a '" . $target_language_name . "'.";

    $prompt .= "\n\nTexto a traducir:\n\"" . $text_to_translate . "\"";

    $translation = generate_text_gemini($prompt);

    return $translation ?? "No se pudo traducir el texto en este momento.";
}

/**
 * Generates a structured article outline for a given topic using the Gemini API.
 *
 * @param string $topic The topic for the article.
 * @param string $site_themes_summary A summary of the website's main themes, to provide context.
 * @param string $site_style_summary A summary of the website's writing style.
 * @return string|null The AI-generated article outline (e.g., as markdown list) or null on error.
 */
function get_gemini_article_outline(string $topic, string $site_themes_summary = '', string $site_style_summary = ''): ?string {
    $prompt = "Necesito un esquema detallado para un artículo de una página web sobre el tema: '" . htmlspecialchars($topic) . "'.\n";
    if (!empty($site_themes_summary)) {
        $prompt .= "El sitio web se centra principalmente en los siguientes temas: " . htmlspecialchars($site_themes_summary) . ".\n";
    }
    if (!empty($site_style_summary)) {
        $prompt .= "El estilo de escritura general del sitio es: " . htmlspecialchars($site_style_summary) . ".\n";
    }
    $prompt .= "El esquema debe ser estructurado, preferiblemente usando encabezados o listas con viñetas (markdown).\n";
    $prompt .= "Debe cubrir los subtemas principales, puntos clave a tratar en cada sección y, si es posible, sugerir un flujo lógico para el artículo.\n";
    $prompt .= "Por favor, proporciona solo el esquema del artículo conciso y bien estructurado, listo para ser usado como base para escribir.";

    // It's good practice to clean up the topic and summaries if they come from user input or less controlled sources,
    // though htmlspecialchars() already provides some safety for prompt construction.
    // For very long summaries, consider truncating them to keep the prompt focused and within token limits.

    $outline = generate_text_gemini($prompt);

    // Basic cleaning of the outline: remove potential "Here's the outline:" type prefixes if AI adds them.
    if ($outline) {
        $outline = preg_replace('/^Aquí tienes el esquema:\s*/i', '', $outline);
        $outline = preg_replace('/^Claro, aquí está el esquema del artículo:\s*/i', '', $outline);
        $outline = preg_replace('/^## Esquema del Artículo\s*/i', '', $outline); // Remove if AI adds a title
        $outline = trim($outline);
    }

    return $outline ?? null;
}

?>
