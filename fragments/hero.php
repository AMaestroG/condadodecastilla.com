<?php
function render_hero(string $heading_html, string $subheading_html = '', string $bg_url = '', bool $show_star = true, string $id = ''): void {
    $id_attr = $id !== '' ? ' id="' . htmlspecialchars($id) . '"' : '';
    $style_attr = $bg_url !== '' ? ' style="background-image: url(' . "'" . htmlspecialchars($bg_url) . "')" . ';"' : '';
    echo "<header{$id_attr} class=\"page-header hero bg-cover bg-center md:bg-center\"{$style_attr}>";
    echo '<div class="hero-content">';
    if ($show_star) {
        echo '<img src="/assets/img/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">';
    }
    echo $heading_html;
    if ($subheading_html !== '') {
        echo $subheading_html;
    }
    echo '</div>';
    echo '</header>';
}
?>
