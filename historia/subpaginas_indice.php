<?php
$json_path = __DIR__ . '/../../data/historia/indice_detallado.json';
$page_data = null;
$error_message = '';

if (file_exists($json_path)) {
    $json_content = file_get_contents($json_path);
    $decoded_data = json_decode($json_content, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $page_data = $decoded_data;
    } else {
        $error_message = 'Error al decodificar el archivo JSON de datos: ' . json_last_error_msg();
    }
} else {
    $error_message = 'Error: No se pudo encontrar el archivo JSON de datos: ' . $json_path;
}

// Valores por defecto por si el JSON falla o no tiene todos los campos
$titulo_pagina = $page_data['titulo_pagina'] ?? 'Índice de Temas Históricos';
$descripcion_general = $page_data['descripcion_general'] ?? 'No se pudo cargar la descripción.';
$temas_detallados = $page_data['temas_detallados'] ?? [];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina); ?> - Cerezo de Río Tirón</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">
    <style>
        .subpage-index-list {
            list-style-type: none;
            padding: 0;
        }
        .subpage-index-item {
            background-color: rgba(var(--color-primario-purpura-rgb), 0.05);
            border: 1px solid rgba(var(--color-primario-purpura-rgb), 0.2);
            padding: 1.5em;
            margin-bottom: 1.5em;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .subpage-index-item h3 {
            font-family: 'Cinzel', serif;
            color: var(--color-primario-purpura);
            margin-top: 0;
            margin-bottom: 0.5em;
        }
        .subpage-index-item p {
            font-family: 'Lora', serif;
            line-height: 1.6;
            color: var(--color-texto-principal);
            margin-bottom: 1em;
        }
        .read-more {
            display: inline-block;
            padding: 0.6em 1.2em;
            background-color: var(--color-acento-rojo);
            color: var(--color-blanco-fondo);
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .read-more:hover {
            background-color: var(--color-secundario-dorado);
            color: var(--color-negro-contraste);
        }
        .page-header.hero {
            min-height: 30vh; /* Reduced height for index page */
        }
        .error-message {
            background-color: #ffdddd;
            border: 1px solid #ff0000;
            color: #d8000c;
            padding: 1em;
            margin: 1em 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="/index.html" class="logo-link">
                <img src="/imagenes/escudo.jpg" alt="Escudo del Condado de Castilla: castillo dorado sobre fondo púrpura con una estrella de 8 puntas dorada encima." class="logo-image">
            </a>
            <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>
            <ul class="nav-links">
                <li><a href="/index.html">Inicio</a></li>
                <li><a href="/historia/historia.html">Nuestra Historia</a></li>
                <li><a href="/historia/nuestra_historia_nuevo4.html">Reflexiones sobre Auca</a></li>
                <li><a href="/historia/subpaginas_indice.php" class="active-link">Índice Detallado</a></li>
                <li><a href="/lugares/lugares.html">Lugares Emblemáticos</a></li>
                <li><a href="/visitas/visitas.html">Planifica Tu Visita</a></li>
                <li><a href="/cultura/cultura.html">Cultura y Legado</a></li>
                <li><a href="/contacto/contacto.html">Contacto</a></li>
                <li><a href="https://www.facebook.com/groups/1052427398664069" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-square"></i> Comunidad</a></li>
            </ul>
        </div>
    </nav>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.7), rgba(var(--color-negro-contraste-rgb), 0.85)), url('/imagenes/mapa_antiguo_detalle.jpg');">
        <div class="hero-content">
            <h1><?php echo htmlspecialchars($titulo_pagina); ?></h1>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <h2>Explorando la Historia de Cerezo de Río Tirón en Detalle</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; max-width: 80ch; margin-left: auto; margin-right: auto; margin-bottom: 2em;"><?php echo htmlspecialchars($descripcion_general); ?></p>
                    <p style="text-align: center; margin-bottom: 2.5em;"><a href="/historia/nuestra_historia_nuevo4.html" class="read-more" style="background-color: var(--color-primario-purpura);">&laquo; Volver a la página principal de Nuestra Historia</a></p>

                    <?php if (!empty($temas_detallados)): ?>
                        <ul class="subpage-index-list">
                            <?php foreach ($temas_detallados as $tema): ?>
                                <li class="subpage-index-item">
                                    <h3><?php echo htmlspecialchars($tema['titulo_tema'] ?? 'Título no disponible'); ?></h3>
                                    <p><?php echo htmlspecialchars($tema['descripcion_corta'] ?? 'Descripción no disponible.'); ?></p>
                                    <?php if (isset($tema['ruta_html_original'])): ?>
                                        <a href="/<?php echo htmlspecialchars(ltrim($tema['ruta_html_original'], '/')); ?>" class="read-more">Leer más...</a>
                                    <?php else: ?>
                                        <span class="read-more" style="background-color: grey;">Enlace no disponible</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay temas detallados disponibles en este momento.</p>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>© <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
            <p>Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/groups/1052427398664069" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Síguenos en Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="/en_construccion.html" aria-label="Instagram" title="Síguenos en Instagram"><i class="fab fa-instagram"></i></a>
                <a href="/en_construccion.html" aria-label="Twitter" title="Síguenos en Twitter"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script>
        // Script para el menú de navegación móvil
        const navToggle = document.querySelector('.nav-toggle');
        const navLinks = document.querySelector('.nav-links');

        navToggle.addEventListener('click', () => {
            const isExpanded = navToggle.getAttribute('aria-expanded') === 'true' || false;
            navToggle.setAttribute('aria-expanded', !isExpanded);
            navLinks.classList.toggle('active');
        });

        // Opcional: Cerrar menú al hacer clic en un enlace
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (navLinks.classList.contains('active')) {
                    navToggle.setAttribute('aria-expanded', 'false');
                    navLinks.classList.remove('active');
                }
            });
        });
    </script>

</body>
</html>
