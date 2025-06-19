<?php
require_once __DIR__ . '/session.php';
ensure_session_started();
require_once __DIR__ . '/env_loader.php';
$geminiKey = getenv('GEMINI_API_KEY') ?: '';
$useMin = filter_var($_ENV['USE_MINIFIED_ASSETS'] ?? false, FILTER_VALIDATE_BOOLEAN);
$min = $useMin ? '.min' : '';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="/assets/css/epic_theme{$min}.css">
<link rel="stylesheet" href="/assets/css/header{$min}.css">
<link rel="stylesheet" href="/assets/css/sliding_menu{$min}.css">
<link rel="stylesheet" href="/assets/css/language-panel{$min}.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" integrity="sha384-JwsW+0ELqRMx9x6pRP70dNDO7xjoMnIKPQ4j/wcgUp3NE6PFcAckU4iigFsMghvY" crossorigin="anonymous">
<link rel="stylesheet" href="/assets/css/custom{$min}.css">
<link rel="stylesheet" href="/assets/css/lighting{$min}.css">
<link rel="stylesheet" href="/assets/vendor/css/tailwind.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-SN33kWx+ihQ/HVbUaz2QmiFjCwXTlzAIXazhbugzuDUFc1l1b/HFB70dNFuPu6j6" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" integrity="sha384-JNHB8zHcoUfOaL+5wtIg9Y9ycYzaO+F6DRKCVh0b07XHtcwPa5RWPLXrI75EetBh" crossorigin="anonymous" />
<script defer src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" integrity="sha384-pqaW8ZT6X0hgqP/d9vywgq6Z9erjRzCQXDpUe1koRaSPoaqe7iT730cdpShUMHbV" crossorigin="anonymous"></script>
<script defer src="/assets/js/polyfills{$min}.js"></script>
