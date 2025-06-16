<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';      // For is_admin_logged_in()
require_once __DIR__ . '/../dashboard/db_connect.php'; // Provides $pdo
/** @var PDO $pdo */
require_once __DIR__ . '/../includes/text_manager.php';// For editableText()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cultura y Legado - Condado de Castilla</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <?php include __DIR__ . '/../includes/head_common.php'; ?>
    <?php require_once __DIR__ . '/../includes/load_page_css.php'; ?>
</head>
<body>

    <?php
    require_once __DIR__ . '/../includes/components/header/header.php';
    ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.75), rgba(var(--color-negro-contraste-rgb), 0.88)), url('/assets/img/hero_cultura_background.jpg');">
        <div class="hero-content">
            <img src="/assets/img/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <?php editableText('cultura_hero_titulo', $pdo, 'Cultura Viva y Legado Perenne', 'h1'); ?>
            <?php editableText('cultura_hero_subtitulo', $pdo, 'Las tradiciones, el idioma y el espíritu de una tierra forjada en la historia.', 'p'); ?>
        </div>
    </header>

    <main>
        <section class="section"> <div class="container page-content-block"> <?php editableText('cultura_intro_p1', $pdo, 'El Condado de Castilla no solo es un crisol de historia, sino también una fuente inagotable de cultura
                    y tradiciones que han perdurado a través de los siglos. Desde el nacimiento de un idioma universal
                    hasta las costumbres que definen a sus gentes, te invitamos a explorar el alma de esta tierra.', 'p', 'intro-paragraph text-center', 'style="font-size: clamp(1.1em, 2.4vw, 1.3em); margin-bottom: 2.5em;"'); ?>

                <article class="culture-article-block">
                    <?php editableText('cultura_idioma_titulo_texto', $pdo, '<i class="fas fa-language"></i> El Origen del Idioma Castellano', 'h2'); ?>
                    <?php editableText('cultura_idioma_p1', $pdo, 'Pocas regiones pueden enorgullecerse de ser la cuna de una lengua hablada por cientos de millones
                        de personas en todo el mundo. El castellano, nuestro español, dio sus primeros balbuceos en estas tierras.
                        Surgido del latín vulgar traído por los romanos, y enriquecido por las lenguas prerromanas y el contacto 
                        con el árabe durante la Edad Media, encontró en los monasterios y escritorios de Castilla la Vieja 
                        el caldo de cultivo perfecto para su desarrollo.', 'p'); ?>
                    <?php editableText('cultura_idioma_p2', $pdo, 'Documentos como las Glosas Emilianenses y Silenses, aunque no originarios exactamente de Cerezo,
                        son testimonios cercanos de esta fascinante evolución lingüística. La necesidad de comunicación en 
                        una zona de frontera, con una rica mezcla de culturas, impulsó la formación de un romance distintivo 
                        que, con el tiempo, se convertiría en el idioma oficial de un vasto imperio y en un vehículo de 
                        expresión literaria de primer orden.', 'p'); ?>
                    <div class="image-container-internal">
                        <img src="/assets/img/placeholder.jpg" alt="Detalle de un manuscrito medieval antiguo, representando los primeros escritos en castellano">
                        <?php editableText('cultura_idioma_caption_texto', $pdo, '<i class="fas fa-scroll"></i> Fragmentos de historia: los primeros vestigios del castellano.', 'p', 'image-caption'); ?>
                    </div>
                </article>

                <article class="culture-article-block">
                    <?php editableText('cultura_tradiciones_titulo_texto', $pdo, '<i class="fas fa-glass-cheers"></i> Tradiciones y Fiestas Locales', 'h2'); ?>
                    <?php editableText('cultura_tradiciones_p1', $pdo, 'Las tradiciones de Cerezo de Río Tirón y sus alrededores son un reflejo vivo de su historia y del
                        carácter de sus habitantes. Muchas festividades tienen raíces ancestrales, combinando ritos paganos 
                        con celebraciones cristianas, fruto de siglos de sincretismo cultural.', 'p'); ?>
                    
                    <div class="featured-item">
                        <img src="/assets/img/placeholder.jpg" alt="Imagen de una romería o fiesta popular en la región de Castilla">
                        <div class="featured-item-content">
                            <?php editableText('cultura_tradiciones_featured_titulo', $pdo, 'Romerías y Celebraciones Patronales', 'h3'); ?>
                            <?php editableText('cultura_tradiciones_featured_p1', $pdo, 'Las romerías a ermitas cercanas, las fiestas en honor a los santos patronos (como San Vitores en agosto), y las celebraciones
                                ligadas a los ciclos agrícolas marcan el calendario anual. Estos eventos son una oportunidad 
                                para la reunión comunitaria, la música folclórica, los bailes tradicionales y la degustación 
                                de productos típicos.', 'p'); ?>
                        </div>
                    </div>

                    <?php editableText('cultura_tradiciones_p2', $pdo, 'Otras costumbres, como los cantos populares, los juegos tradicionales o las reuniones en torno a la
                        lumbre en invierno, forman parte del rico tejido cultural que se ha transmitido de generación en generación.', 'p'); ?>
                </article>

                <article class="culture-article-block">
                    <?php editableText('cultura_artesania_titulo_texto', $pdo, '<i class="fas fa-palette"></i> Artesanía y <i class="fas fa-drumstick-bite"></i> Gastronomía Típica', 'h2'); ?>
                    <?php editableText('cultura_artesania_p1', $pdo, 'El saber hacer de los artesanos locales y los sabores de la tierra son dos pilares fundamentales
                        del legado cultural castellano.', 'p'); ?>
                    <?php editableText('cultura_artesania_subtitulo_manos', $pdo, 'Manos Artesanas', 'h3'); ?>
                    <?php editableText('cultura_artesania_manos_p1', $pdo, 'Aunque la industrialización ha transformado muchos oficios, aún perviven en la región artesanos
                        que trabajan la madera, el cuero, la forja o la cerámica con técnicas heredadas. Estos objetos, 
                        más allá de su utilidad, son portadores de una identidad y una historia.', 'p'); ?>
                    <div class="image-container-internal">
                         <img src="/assets/img/placeholder.jpg" alt="Piezas de artesanía tradicional castellana, como cerámica o tallas de madera">
                        <?php editableText('cultura_artesania_manos_caption_texto', $pdo, '<i class="fas fa-tools"></i> La habilidad de las manos que conservan la tradición.', 'p', 'image-caption'); ?>
                    </div>

                    <?php editableText('cultura_artesania_subtitulo_sabores', $pdo, 'Sabores de la Tierra', 'h3'); ?>
                    <?php editableText('cultura_artesania_sabores_p1', $pdo, 'La gastronomía castellana es recia y sabrosa, basada en los productos de la tierra y la ganadería.
                        Platos como el cordero asado, la morcilla de Burgos, las sopas de ajo, las alubias rojas de Ibeas, y los quesos 
                        artesanales son imprescindibles en cualquier mesa. Los vinos de la Rioja Alta y de la Ribera del Duero, denominaciones cercanas, acompañan a la perfección estos manjares.', 'p'); ?>
                     <div class="featured-item">
                         <img src="/assets/img/placeholder.jpg" alt="Plato de cordero asado, típico de la gastronomía castellana">
                        <div class="featured-item-content">
                            <?php editableText('cultura_artesania_featured_titulo', $pdo, 'El Sabor de la Tradición', 'h3'); ?>
                            <?php editableText('cultura_artesania_featured_p1', $pdo, 'En Cerezo y sus alrededores, podrás degustar recetas transmitidas de abuelas a nietas,
                                que conservan la esencia de la cocina de siempre. No dejes de probar el "picadillo" de la matanza, 
                                las "patatas a la riojana" o las "rosquillas" caseras.', 'p'); ?>
                        </div>
                    </div>
                </article>

                <article class="culture-article-block">
                    <?php editableText('cultura_arqueologia_titulo_texto', $pdo, '<i class="fas fa-landmark-dome"></i> Investigación y Arqueología: Descubriendo el Pasado', 'h2'); ?>
                    <?php editableText('cultura_arqueologia_p1', $pdo, 'El subsuelo de Cerezo de Río Tirón y del antiguo Condado de Castilla sigue guardando innumerables
                        secretos. Los trabajos de investigación arqueológica son fundamentales para seguir desvelando 
                        capítulos de nuestra historia, desde los primeros asentamientos hasta la consolidación medieval.', 'p'); ?>
                    <?php editableText('cultura_arqueologia_p2', $pdo, 'Proyectos de excavación en la Civitate Auca Patricia, estudios sobre el Alcázar de Casio, y la
                        búsqueda de nuevos yacimientos contribuyen a un mejor entendimiento de nuestro pasado. Apoyar 
                        estos esfuerzos y visitar los centros de interpretación o museos locales es una forma de conectar 
                        directamente con este legado y contribuir a su preservación.', 'p'); ?>
                    <div class="image-container-internal">
                        <img src="/assets/img/placeholder.jpg" alt="Fotografía de una excavación arqueológica en curso en el yacimiento de Auca Patricia">
                        <?php editableText('cultura_arqueologia_caption_texto', $pdo, '<i class="fas fa-search-location"></i> Arqueólogos desenterrando los secretos del pasado.', 'p', 'image-caption'); ?>
                    </div>
                    <p class="text-center" style="margin-top: 2em;">
                        <?php editableText('cultura_link_explora_lugares_texto', $pdo, 'Explora los Lugares Emblemáticos', 'a', 'cta-button cta-button-small', 'href="/lugares/lugares.html"'); ?>
                    </p>
                </article>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../includes/components/footer/footer.php'; ?>

    <script src="/js/layout.js"></script>
</body>
</html>
