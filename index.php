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
// Use dynamic PHP header if available so menu fragments can contain
// server-side logic like the admin login menu.
require_once __DIR__ . '/_header.php';
?>

    <header id="hero-video" class="relative h-screen w-full overflow-hidden">
        <video class="absolute inset-0 object-cover w-full h-full" src="https://samplelib.com/lib/preview/mp4/sample-5s.mp4" autoplay muted loop playsinline></video>
        <div id="hero-content" class="relative z-10 flex flex-col items-center justify-center h-full text-center opacity-0 transition-opacity duration-1000 ease-out">
            <h1 class="font-serif text-yellow-300 text-3xl md:text-5xl shadow-lg drop-shadow-lg shine-gold purple-shadow">Condado de Castilla: Cuna de Emperadores</h1>
            <p class="mt-4 bg-black bg-opacity-50 text-white p-4 font-sans stone-texture">Promocionamos el turismo en Cerezo de Río Tirón y cuidamos su patrimonio arqueológico y cultural.</p>
        </div>
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-yellow-300">
            <svg class="w-8 h-8 animate-bounce" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </header>

    <nav id="page-nav" class="section-nav cta-group">
        <a href="#video" class="cta-button-small">Video</a>
        <a href="#memoria" class="cta-button-small">Memoria</a>
        <a href="#legado" class="cta-button-small">Legado</a>
        <a href="#personajes" class="cta-button-small">Personajes</a>
        <a href="#timeline" class="cta-button-small">Historia</a>
        <a href="#inmersion" class="cta-button-small">Cultura Viva</a>
    </nav>

    <section id="video" class="video-section section spotlight-active" data-aos="fade-up">
        <div class="container-epic">
            <h2 class="section-title">Un Vistazo a Nuestra Tierra</h2>
            <div class="video-container">
                <iframe
                    src="https://drive.google.com/file/d/1wm74VmKH21Nz7zFUkY8a8Z9672D4cyHN/preview"
                    title="Video promocional del Condado de Castilla y Cerezo de Río Tirón"
                    width="560" height="315"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    loading="lazy"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </section>

    <main>
        <section id="memoria" class="section detailed-intro-section spotlight-active" data-aos="fade-up">
            <div class="container-epic">
                <?php editableText('memoria_titulo_index', $pdo, 'Recuperando la Memoria de la Hispanidad Castellana', 'h2', 'gradient-text tagline-background'); ?>
                <?php editableText('memoria_parrafo_index', $pdo, 'Un profundo análisis de nuestras raíces culturales, la importancia de la arqueología y el legado de la Civitate Auca Patricia. Descubre cómo el pasado de Cerezo de Río Tirón es fundamental para entender la Hispanidad.', 'p', ''); ?>
                <p class="cta-group">
                    <a href="/secciones_index/memoria_hispanidad.html" class="cta-button">Leer Más Sobre Nuestra Memoria</a>
                </p>
            </div>
        </section>

        <section id="legado" class="section alternate-bg spotlight-active" data-aos="fade-up">
            <div class="container-epic">
                <h2 class="section-title">Explora Nuestro Legado</h2>
                <div class="card-grid">
                    <div class="card">
                        <img src="/assets/img/PrimerEscritoCastellano.jpg" alt="Página de un manuscrito medieval iluminado, simbolizando la rica historia de Castilla">
                        <div class="card-content">
                            <h3>Nuestra Historia</h3>
                            <p>Desde los Concanos y la Civitate Auca Patricia hasta la formación del Condado. Sumérgete en los relatos que definieron Castilla.</p>
                            <a href="/historia/historia.php" class="read-more">Leer Más</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="/assets/img/RodrigoTabliegaCastillo.jpg" alt="Imponentes ruinas del Alcázar de Casio recortadas contra un cielo dramático">
                        <div class="card-content">
                            <h3>Lugares Emblemáticos</h3>
                            <p>Descubre el imponente Alcázar de Casio, los secretos de la Civitate Auca y otros tesoros arqueológicos que esperan ser explorados.</p>
                            <a href="/lugares/lugares.php" class="read-more">Explorar Sitios</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="/assets/img/Yanna.jpg" alt="Iglesia de Santa María de la Llana, ejemplo del patrimonio arquitectónico de Cerezo">
                        <div class="card-content">
                            <h3>Planifica Tu Visita</h3>
                            <p>Encuentra toda la información que necesitas para tu aventura en Cerezo de Río Tirón: cómo llegar, dónde alojarte y qué no te puedes perder.</p>
                            <a href="/visitas/visitas.php" class="read-more">Organizar Viaje</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="personajes" class="section" data-aos="fade-up">
            <div class="container-epic">
                <h2 class="section-title">Personajes de la Historia</h2>
                <div class="card-grid">
                    <div class="card">
                        <img src="/assets/img/Casio.png" alt="Retrato idealizado o ilustración del Conde Casio, figura histórica del siglo VIII">
                        <div class="card-content">
                            <h3>Conde Casio</h3>
                            <p>Figura fundamental del siglo VIII, se le atribuye la construcción o refuerzo del Alcázar de Cerezo.</p>
                            <a href="/personajes/Militares_y_Gobernantes/conde_casio_cerasio.html" class="read-more">Saber Más</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="/assets/img/GonzaloTellez.png" alt="Ilustración representando a Gonzalo Téllez, Conde de Lantarón y Cerezo">
                        <div class="card-content">
                            <h3>Gonzalo Téllez</h3>
                            <p>Conde de Lantarón y Cerezo (c. 897 - c. 913), personaje clave en la consolidación de los territorios.</p>
                            <a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/gonzalo_tellez.html" class="read-more">Saber Más</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="/assets/img/FernandoDiaz.png" alt="Representación artística de Fernando Díaz, conde castellano">
                        <div class="card-content">
                            <h3>Fernando Díaz</h3>
                            <p>Sucesor de Gonzalo Téllez, continuó la labor de defensa y organización en la primitiva Castilla.</p>
                            <a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/fernando_diaz.html" class="read-more">Saber Más</a>
                        </div>
                    </div>
                </div>
                 <p class="cta-group">
                    <a href="/personajes/indice_personajes.html" class="cta-button">Personajes</a>
                </p>
            </div>
        </section>
        
        <section id="timeline" class="section timeline-section-summary alternate-bg" data-aos="fade-up">
            <div class="container-epic">
                <h2 class="section-title">Nuestra Historia en el Tiempo</h2>
                <p class="timeline-intro">Un recorrido conciso por los momentos más determinantes de nuestra región, desde la prehistoria hasta la consolidación del Condado. Cada época ha dejado una huella imborrable.</p>
                <p class="cta-group">
                    <a href="/secciones_index/historia_tiempo_resumen.html" class="cta-button">Explorar Resumen de la Historia</a>
                </p>
            </div>
        </section>

        <section id="inmersion" class="section immersion-section" data-aos="fade-up">
            <div class="container-epic">
                <h2>Sumérgete en la Historia Viva de Tu Cultura</h2>
                <p>
                    Esta web es más que información; es una puerta a tus raíces. Un viaje al origen del castellano y la identidad hispana te espera.
                    Siente la llamada de la historia y conecta con el legado que nos une.
                </p>
                <a href="/cultura/cultura.php" class="cta-button">Cultura</a>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/_footer.php'; ?>
    <script src="/js/layout.js"></script>

</body>
</html>
