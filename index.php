<?php
require_once __DIR__ . '/includes/session.php';
ensure_session_started();
require_once 'includes/auth.php';      // For is_admin_logged_in()
require_once __DIR__ . '/includes/db_connect.php'; // Provides $pdo
/** @var PDO $pdo */
$db_warning = '';
if (!$pdo) {
    $db_warning = "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
}
require_once 'includes/text_manager.php';// For editableText()
require_once 'includes/ai_utils.php';
require_once __DIR__ . '/includes/homonexus.php';?><!DOCTYPE html>
<html lang="es">
<head>
    <title>Condado de Castilla - Cuna de tu Cultura y Lengua</title>
    <?php include __DIR__ . '/includes/head_common.php'; ?>
    <?php
    require_once __DIR__ . '/includes/load_page_css.php';
    load_page_css();
    ?>

</head>
<body class="alabaster-bg <?php echo homonexus_body_class(); ?>">
    <div id="layer-sky" class="parallax-layer" data-speed="0.2"></div>
    <div id="layer-ruins" class="parallax-layer" data-speed="0.4"></div>
    <div id="layer-grass" class="parallax-layer" data-speed="0.6"></div>
<?php echo $db_warning; ?>
<?php
require_once __DIR__ . '/fragments/header.php';
?>

    <main class="container-epic px-4 sm:px-6 lg:px-8 py-8">
        <!-- Nueva sección Hero inspirada en modern.css -->
        <section id="hero-main" data-aos="fade-up">
            <?php editableText('hero_titulo_index_modern', $pdo, 'Condado de Castilla', 'h1', ''); // Clases se aplicarán desde #hero-main h1 en CSS ?>
            <?php editableText('hero_subtitulo_index_modern', $pdo, 'Un lugar donde la historia y la naturaleza se encuentran.', 'p', ''); // Clases se aplicarán desde #hero-main p ?>
            <a href="/historia/historia.php" class="cta-button-modern">Explora Nuestra Historia</a>
            <?php
            // El hero_summary y el segundo cta-button del hero original se omiten por ahora para seguir la estructura de modern.css
            // Se podrían reintroducir si es necesario.
            ?>
        </section>

        <!-- Sección Hero Original Comentada -->
        <!--
        <section id="hero" class="section hero-section text-center py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <?php editableText('hero_titulo_index', $pdo, 'Condado de Castilla', 'h1', 'text-4xl lg:text-6xl font-bold gradient-text tagline-background font-headings mb-4'); ?>
            <p class="hero-summary">
                <a href="/historia/historia.php" class="block">
                    Desde su origen romano como <span class="gradient-text">Auca Patricia</span>, Cerezo de Río Tirón conserva calzadas y murallas junto al Ebro. En el siglo VIII se alzó el <span class="gradient-text">Alcázar de Cerasio</span>, construido con alabastro reutilizado y sede de los primeros condes.
                </a>
            </p>
            <?php editableText('hero_subtitulo_index', $pdo, 'Donde la historia, la cultura y tu lengua tomaron forma.', 'p', 'text-xl lg:text-2xl text-gray-700 dark:text-gray-300 font-body mb-8'); ?>
            <p class="cta-group">
                <a href="/historia/historia.php" class="cta-button">Descubre la Historia</a>
                <a href="/lugares/lugares.php" class="cta-button-secondary">Explora los Lugares</a>
            </p>
        </section>
        -->

        <section id="video-intro" class="section video-intro-section py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="section-title text-3xl font-headings mb-4">Un Vistazo a Nuestra Tierra</h2>
                    <?php editableText('video_descripcion_index', $pdo, 'Sumérgete en la belleza y el misterio del Condado de Castilla a través de nuestro video introductorio. Descubre paisajes que han sido testigos de la historia y maravíllate con el legado que perdura.', 'p', 'text-lg font-body mb-6'); ?>
                    <a href="#video-modal" class="cta-button-small open-video-modal">Ver Video</a>
                </div>
                <figure class="video-placeholder-container rounded-lg shadow-xl overflow-hidden">
                    <img src="/assets/img/hero_mis_tierras.jpg" alt="Paisaje del Condado de Castilla" class="w-full h-auto object-cover">
                    <figcaption class="text-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Pulsa para ver el video promocional.
                    </figcaption>
                </figure>
            </div>
        </section>

        <!-- Modal para el video -->
        <div id="video-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 hidden z-50" aria-labelledby="video-modal-title" role="dialog" aria-modal="true">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-3xl w-full">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="video-modal-title" class="text-xl font-headings">Video Promocional</h3>
                    <button class="close-video-modal text-2xl text-gray-700 dark:text-gray-300 hover:text-red-500">&times;</button>
                </div>
                <div class="aspect-w-16 aspect-h-9">
                    <iframe class="w-full h-full"
                        src="https://drive.google.com/file/d/1wm74VmKH21Nz7zFUkY8a8Z9672D4cyHN/preview"
                        title="Video promocional del Condado de Castilla y Cerezo de Río Tirón"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        loading="lazy"
                        allowfullscreen></iframe>
                </div>
                <p class="text-center mt-2 text-sm">
                    <a href="/docs/transcripts/video_promocional.md" class="video-transcript-link underline">
                        Ver transcripción del video
                    </a>
                </p>
            </div>
        </div>

        <!-- Nueva sección Highlights inspirada en modern.css -->
        <section id="highlights-main" data-aos="fade-up">
            <h2>Destacados</h2> <!-- Se aplicarán estilos desde CSS -->
            <div class="highlight-grid-modern">
                <div class="highlight-item-modern">
                    <!-- Idealmente, las imágenes también deberían ser gestionables o al menos consistentes en tamaño/aspecto -->
                    <img loading="lazy" src="/assets/img/PrimerEscritoCastellano.jpg" alt="Manuscrito medieval, simbolizando la historia de Castilla" class="w-full h-auto object-contain mb-4 max-h-48"> <!-- Ajustes de clase de imagen -->
                    <h3>Nuestra Historia</h3>
                    <p>Desde los albores de la civilización hasta la formación del Condado. Sumérgete en los relatos que definieron Castilla.</p>
                    <a href="/historia/historia.php">Leer Más</a>
                </div>
                <div class="highlight-item-modern">
                    <img loading="lazy" src="/assets/img/RodrigoTabliegaCastillo.jpg" alt="Ruinas del Alcázar de Casio" class="w-full h-auto object-contain mb-4 max-h-48">
                    <h3>Lugares Emblemáticos</h3>
                    <p>Descubre el imponente Alcázar de Casio, la misteriosa Civitate Auca y otros tesoros arqueológicos.</p>
                    <a href="/lugares/lugares.php">Explorar Sitios</a>
                </div>
                <div class="highlight-item-modern">
                    <img loading="lazy" src="/assets/img/Yanna.jpg" alt="Iglesia de Santa María de la Llana" class="w-full h-auto object-contain mb-4 max-h-48">
                    <h3>Cultura Viva</h3>
                    <p>Participa en nuestras tradiciones, explora el arte y la gastronomía local. Conecta con el espíritu de Castilla.</p>
                    <a href="/cultura/cultura.php">Descubrir Cultura</a>
                </div>
            </div>
        </section>

        <!-- Sección Legado Destacado Original Comentada -->
        <!--
        <section id="legado-destacado" class="section alternate-bg spotlight-active py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <h2 class="section-title text-3xl font-headings text-center mb-12">Explora Nuestro Legado</h2>
            <div class="card-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card transform hover:scale-105 transition-transform duration-300 ease-in-out shadow-lg rounded-lg overflow-hidden">
                    <img loading="lazy" class="w-full h-56 object-cover" src="/assets/img/PrimerEscritoCastellano.jpg" alt="Manuscrito medieval, simbolizando la historia de Castilla">
                    <div class="card-content p-6">
                        <h3 class="font-headings text-xl mb-2">Nuestra Historia</h3>
                        <p class="text-lg font-body mb-4">Desde los albores de la civilización hasta la formación del Condado. Sumérgete en los relatos que definieron Castilla.</p>
                        <a href="/historia/historia.php" class="read-more-button">Leer Más <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                </div>
                <div class="card transform hover:scale-105 transition-transform duration-300 ease-in-out shadow-lg rounded-lg overflow-hidden">
                    <img loading="lazy" class="w-full h-56 object-cover" src="/assets/img/RodrigoTabliegaCastillo.jpg" alt="Ruinas del Alcázar de Casio">
                    <div class="card-content p-6">
                        <h3 class="font-headings text-xl mb-2">Lugares Emblemáticos</h3>
                        <p class="text-lg font-body mb-4">Descubre el imponente Alcázar de Casio, la misteriosa Civitate Auca y otros tesoros arqueológicos.</p>
                        <a href="/lugares/lugares.php" class="read-more-button">Explorar Sitios <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                </div>
                <div class="card transform hover:scale-105 transition-transform duration-300 ease-in-out shadow-lg rounded-lg overflow-hidden">
                    <img loading="lazy" class="w-full h-56 object-cover" src="/assets/img/Yanna.jpg" alt="Iglesia de Santa María de la Llana">
                    <div class="card-content p-6">
                        <h3 class="font-headings text-xl mb-2">Cultura Viva</h3>
                        <p class="text-lg font-body mb-4">Participa en nuestras tradiciones, explora el arte y la gastronomía local. Conecta con el espíritu de Castilla.</p>
                        <a href="/cultura/cultura.php" class="read-more-button">Descubrir Cultura <i class="fas fa-arrow-right ml-2"></i></a>
                    </div>
                </div>
            </div>
        </section>
        -->

        <section id="llamada-accion" class="section py-12 sm:py-16 lg:py-20 text-center" data-aos="fade-up">
                    </div>
                </div>
            </div>
        </section>

        <section id="llamada-accion" class="section py-12 sm:py-16 lg:py-20 text-center" data-aos="fade-up">
            <h2 class="text-3xl font-headings mb-6">¿Listo para el Viaje?</h2>
            <p class="text-xl font-body mb-8 max-w-2xl mx-auto">
                Tu aventura en el corazón de la historia castellana comienza aquí. Planifica tu visita, únete a nuestra comunidad o simplemente aprende más sobre este fascinante rincón del mundo.
            </p>
            <div class="cta-group">
                <a href="/visitas/visitas.php" class="cta-button">Planifica tu Visita</a>
                <a href="/foro/index.php" class="cta-button-secondary">Únete al Foro</a>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/fragments/footer.php'; ?>
    
<!-- Script for video modal is now in /assets/js/video-modal.js -->
</body>
</html>
