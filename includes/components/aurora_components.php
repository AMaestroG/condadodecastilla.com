<?php
declare(strict_types=1);

namespace AuroraComponents;

use function array_merge;
use function array_values;
use function dirname;
use function file_exists;
use function file_get_contents;
use function htmlspecialchars;
use function is_array;
use function is_string;
use function json_encode;
use function preg_split;
use function strlen;
use function substr;

/**
 * Build HTML tags recursively, acting as a tiny templating helper.
 *
 * @param array<int|string, mixed>|string|null $children
 */
function component(string $tag, array $attributes = [], $children = null): string
{
    $childNodes = flatten_children($children);
    $attr = '';
    foreach ($attributes as $key => $value) {
        if ($value === null || $value === false) {
            continue;
        }
        if ($value === true) {
            $attr .= ' ' . htmlspecialchars((string) $key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            continue;
        }
        $attr .= ' ' . htmlspecialchars((string) $key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '="' . htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
    }

    $content = implode('', $childNodes);
    return "<{$tag}{$attr}>{$content}</{$tag}>";
}

/**
 * Recursively flatten mixed children into a single array of HTML strings.
 *
 * @param mixed $children
 * @return array<int, string>
 */
function flatten_children($children): array
{
    if ($children === null) {
        return [];
    }

    if (is_string($children)) {
        return [$children];
    }

    if (is_array($children)) {
        $result = [];
        foreach ($children as $child) {
            $result = array_merge($result, flatten_children($child));
        }
        return $result;
    }

    if ($children instanceof \Stringable) {
        return [(string) $children];
    }

    return [(string) $children];
}

function gradient_heading(string $text, string $tag = 'h2', string $classes = 'section-title'): string
{
    $inner = component('strong', [], [htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')]);
    return component($tag, ['class' => $classes], [$inner]);
}

function root_path(string $relativePath): string
{
    $clean = ltrim($relativePath, '/');
    return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $clean;
}

function markdown_excerpt(string $relativePath, int $maxParagraphs = 2, int $maxCharacters = 480): string
{
    $path = root_path($relativePath);
    if (!file_exists($path)) {
        return '';
    }

    $raw = (string) file_get_contents($path);
    $paragraphs = preg_split('/\n\s*\n/m', trim($raw)) ?: [];
    $selected = array_slice($paragraphs, 0, $maxParagraphs);

    $text = trim(preg_replace('/[#>*_`\-]+/', '', implode("\n\n", $selected)) ?? '');
    if (strlen($text) > $maxCharacters) {
        $text = substr($text, 0, $maxCharacters - 3) . '…';
    }

    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Collect excerpts from a tree definition, using recursion to flatten nodes.
 *
 * @param array<int|string, mixed> $tree
 * @return array<int, array{title: string, excerpt: string, link: string}>
 */
function collect_knowledge(array $tree): array
{
    $result = [];
    $walker = function ($node) use (&$walker, &$result): void {
        if (is_array($node) && isset($node['path'], $node['title'], $node['link'])) {
            $result[] = [
                'title' => (string) $node['title'],
                'excerpt' => markdown_excerpt((string) $node['path'], $node['paragraphs'] ?? 2),
                'link' => (string) $node['link'],
            ];
            return;
        }

        if (is_array($node)) {
            foreach ($node as $child) {
                $walker($child);
            }
        }
    };

    $walker($tree);
    return array_values($result);
}

/**
 * Render a list of cards describing knowledge areas.
 *
 * @param array<int, array{title: string, excerpt: string, link: string}> $items
 */
function render_story_cards(array $items): string
{
    $cards = array_map(
        static function (array $item): string {
            return component('article', ['class' => 'story-card'], [
                component('h3', [], [htmlspecialchars($item['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')]),
                component('p', [], [$item['excerpt']]),
                component('a', ['href' => $item['link']], ['Explorar legado']),
            ]);
        },
        $items
    );

    return component('div', ['class' => 'story-grid'], $cards);
}

/**
 * Render tapestry style articles from metadata.
 *
 * @param array<int, array{heading: string, body: string}> $items
 */
function render_tapestry(array $items): string
{
    $articles = array_map(
        static function (array $item): string {
            return component('article', [], [
                component('h3', [], [htmlspecialchars($item['heading'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')]),
                component('p', [], [$item['body']]),
            ]);
        },
        $items
    );

    return component('div', ['class' => 'tapestry'], $articles);
}

function json_data_attribute(string $name, array $payload): string
{
    return htmlspecialchars((string) json_encode($payload, JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function timeline(array $items): string
{
    $elements = array_map(
        static function (array $item): string {
            $label = component('strong', [], [htmlspecialchars($item['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')]);
            $text = component('span', [], [htmlspecialchars($item['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')]);
            return component('li', [], [$label . $text]);
        },
        $items
    );

    return component('ul', ['class' => 'timeline'], $elements);
}

function mission_text(): string
{
    $excerpt = markdown_excerpt('docs/README.md', 1, 400);
    if ($excerpt === '') {
        $excerpt = 'Promocionamos el turismo y preservamos el patrimonio de Cerezo de Río Tirón como cuna de la cultura hispana.';
    }

    return $excerpt;
}
