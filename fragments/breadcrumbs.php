<?php
function render_breadcrumbs(array $labelMap = []): void
{
    $defaults = [
        '' => 'Inicio',
        'index.php' => 'Inicio',
        'historia' => 'Nuestra Historia',
        'historia.php' => 'Nuestra Historia',
        'subpaginas' => 'Índice Detallado',
        'subpaginas_indice.php' => 'Índice Detallado',
        'lugares' => 'Lugares',
        'lugares.php' => 'Lugares',
    ];
    $labels = array_merge($defaults, $labelMap);

    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);
    $segments = array_values(array_filter(explode('/', trim($path, '/'))));

    $crumbs = [];
    $accum = '';

    // Root breadcrumb
    $crumbs[] = [
        'url' => '/',
        'label' => $labels[''] ?? 'Inicio'
    ];

    foreach ($segments as $seg) {
        $accum .= '/' . $seg;
        $label = $labels[$seg] ?? $labels[$accum] ?? ucwords(str_replace(['_', '-'], [' ', ' '], pathinfo($seg, PATHINFO_FILENAME)));
        $crumbs[] = [
            'url' => $accum,
            'label' => $label
        ];
    }

    echo '<div class="breadcrumb-container"><nav aria-label="breadcrumb"><ol class="breadcrumb">';
    $total = count($crumbs);
    foreach ($crumbs as $idx => $crumb) {
        if ($idx === $total - 1) {
            echo '<li class="active" aria-current="page">' . htmlspecialchars($crumb['label']) . '</li>';
        } else {
            echo '<li><a href="' . htmlspecialchars($crumb['url']) . '">' . htmlspecialchars($crumb['label']) . '</a></li>';
        }
    }
    echo '</ol></nav></div>';
}
?>
