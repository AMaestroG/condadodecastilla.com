<?php
require_once __DIR__ . '/session.php';
ensure_session_started();
require_once __DIR__ . '/env_loader.php';

if (!isset($load_bootstrap)) {
    $load_bootstrap = false;
}
if (!isset($load_aos)) {
    $load_aos = false;
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-5e2ESR8Ycmos6g3gAKr1Jvwye8sW4U1u/cAKulfVJnkakCcMqhOudbtPnvJ+nbv7" crossorigin="anonymous">
<link rel="stylesheet" href="/assets/css/epic_theme.css">
<link rel="stylesheet" href="/assets/css/header.css">
<link rel="stylesheet" href="/assets/css/sliding_menu.css">
<link rel="stylesheet" href="/assets/css/language-panel.css">
<?php if ($load_bootstrap): ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" integrity="sha384-JwsW+0ELqRMx9x6pRP70dNDO7xjoMnIKPQ4j/wcgUp3NE6PFcAckU4iigFsMghvY" crossorigin="anonymous" onerror="this.onerror=null;this.href='/assets/vendor/css/bootstrap.min.css';">
<?php endif; ?>
<link rel="stylesheet" href="/assets/css/custom.css">
<link rel="stylesheet" href="/assets/css/parallax.css">
<link rel="stylesheet" href="/assets/css/time_palettes.css">
<link rel="stylesheet" href="/assets/css/lighting.css">
<link rel="stylesheet" href="/assets/css/cave_mask.css">
<link rel="stylesheet" href="/assets/css/admin_theme.css">
<link rel="stylesheet" href="/assets/vendor/css/tailwind.min.css">
<?php if ($load_bootstrap): ?>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-SN33kWx+ihQ/HVbUaz2QmiFjCwXTlzAIXazhbugzuDUFc1l1b/HFB70dNFuPu6j6" crossorigin="anonymous" onerror="this.onerror=null;this.src='/assets/vendor/js/bootstrap.bundle.min.js';"></script>
<?php endif; ?>
<?php if ($load_aos): ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" integrity="sha384-/rJKQnzOkEo+daG0jMjU1IwwY9unxt1NBw3Ef2fmOJ3PW/TfAg2KXVoWwMZQZtw9" crossorigin="anonymous" onerror="this.onerror=null;this.href='/assets/vendor/css/aos.css';" />
<script defer src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" integrity="sha384-n1AULnKdMJlK1oQCLNDL9qZsDgXtH6jRYFCpBtWFc+a9Yve0KSoMn575rk755NJZ" crossorigin="anonymous" onerror="this.onerror=null;this.src='/assets/vendor/js/aos.js';"></script>
<?php endif; ?>
<script defer src="/assets/js/polyfills.js"></script>
<link rel="stylesheet" href="/assets/css/torch_cursor.css">
<script defer src="/assets/js/torch_cursor.js"></script>
<link rel="stylesheet" href="/assets/css/glow_filter.css">
<link rel="stylesheet" href="/assets/css/custom-pointer.css">
<link rel="stylesheet" href="/assets/css/mobile_contrast.css" media="(max-width: 768px)">
<?php
$svgFilterPath = __DIR__ . '/../fragments/header/svg_filters.html';
if (file_exists($svgFilterPath)) {
    echo file_get_contents($svgFilterPath);
}
?>
