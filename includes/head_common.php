<?php
require_once __DIR__ . '/session.php';
ensure_session_started();
require_once __DIR__ . '/env_loader.php';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">
<link rel="preload" href="https://fonts.gstatic.com/s/cinzel/v25/8vIU7ww63mVu7gtR-kwKxNvkNOjw-tbnTYo.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="https://fonts.gstatic.com/s/lora/v36/0QI6MX1D_JOuGQbT0gvTJPa787weuyJG.ttf" as="font" type="font/ttf" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-5e2ESR8Ycmos6g3gAKr1Jvwye8sW4U1u/cAKulfVJnkakCcMqhOudbtPnvJ+nbv7" crossorigin="anonymous">
<link rel="stylesheet" href="/assets/css/epic_theme.css">
<link rel="stylesheet" href="/assets/css/header.css">
<link rel="stylesheet" href="/assets/css/language-panel.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" integrity="sha384-JwsW+0ELqRMx9x6pRP70dNDO7xjoMnIKPQ4j/wcgUp3NE6PFcAckU4iigFsMghvY" crossorigin="anonymous">
<link rel="stylesheet" href="/assets/css/custom.css">
<link rel="stylesheet" href="/assets/css/slider_menu.css">
<link rel="stylesheet" href="/assets/css/parallax.css">
<link rel="stylesheet" href="/assets/css/time_palettes.css">
<link rel="stylesheet" href="/assets/css/lighting.css">
<link rel="stylesheet" href="/assets/css/admin_theme.css">
<link rel="preload" href="/assets/vendor/css/tailwind.min.css" as="style">
<link rel="stylesheet" href="/assets/vendor/css/tailwind.min.css">
<link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" as="script" crossorigin="anonymous">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-SN33kWx+ihQ/HVbUaz2QmiFjCwXTlzAIXazhbugzuDUFc1l1b/HFB70dNFuPu6j6" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" integrity="sha384-/rJKQnzOkEo+daG0jMjU1IwwY9unxt1NBw3Ef2fmOJ3PW/TfAg2KXVoWwMZQZtw9" crossorigin="anonymous" />
<link rel="preload" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" as="script" crossorigin="anonymous">
<script defer src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" integrity="sha384-n1AULnKdMJlK1oQCLNDL9qZsDgXtH6jRYFCpBtWFc+a9Yve0KSoMn575rk755NJZ" crossorigin="anonymous"></script>
<link rel="preload" href="/assets/js/polyfills.js" as="script">
<script defer src="/assets/js/polyfills.js"></script>
<link rel="stylesheet" href="/assets/css/torch_cursor.css">
<link rel="preload" href="/assets/js/torch_cursor.js" as="script">
<script defer src="/assets/js/torch_cursor.js"></script>
<link rel="preload" href="/assets/js/main.js" as="script">
<link rel="stylesheet" href="/assets/css/glow_filter.css">
<link rel="stylesheet" href="/assets/css/custom-pointer.css">
<link rel="stylesheet" href="/assets/css/mobile_contrast.css" media="(max-width: 768px)">
<?php
$svgFilterPath = __DIR__ . '/../fragments/header/svg_filters.html';
if (file_exists($svgFilterPath)) {
    echo file_get_contents($svgFilterPath);
}
?>
