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
require_once __DIR__ . '/fragments/header.php';
?>

    <header id="hero-video" class="relative h-screen w-full overflow-hidden">
        <div id="hero-content" class="relative z-10 flex flex-col items-center justify-center h-full text-center opacity-0 transition-opacity duration-1000 ease-out">
            <h1 class="gradient-text blend-overlay text-4xl font-headings sm:text-4xl md:text-5xl lg:text-6xl drop-shadow-lg">Condado de Castilla: Cuna de Emperadores</h1>
            <p class="mission-tagline text-lg font-body mt-4 bg-black bg-opacity-50 text-white p-4 sm:p-6 md:p-8">Promocionamos el turismo en Cerezo de Río Tirón y cuidamos su patrimonio arqueológico y cultural.</p>
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

    <section id="video" class="video-section section spotlight-active py-12 sm:py-16 lg:py-20" data-aos="fade-up">
        <div class="container-epic px-4 sm:px-6 lg:px-8">
            <h2 class="section-title text-2xl font-headings">Un Vistazo a Nuestra Tierra</h2>
            <div class="video-container mx-auto">
                <iframe class="w-full h-full"
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
        <section id="memoria" class="section detailed-intro-section spotlight-active py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="container-epic px-4 sm:px-6 lg:px-8">
                <?php editableText('memoria_titulo_index', $pdo, 'Recuperando la Memoria de la Hispanidad Castellana', 'h2', 'gradient-text tagline-background text-2xl font-headings'); ?>
                <?php editableText('memoria_parrafo_index', $pdo, 'Un profundo análisis de nuestras raíces culturales, la importancia de la arqueología y el legado de la Civitate Auca Patricia. Descubre cómo el pasado de Cerezo de Río Tirón es fundamental para entender la Hispanidad.', 'p', 'text-lg font-body'); ?>
                <p class="cta-group">
                    <a href="/secciones_index/memoria_hispanidad.html" class="cta-button">Leer Más Sobre Nuestra Memoria</a>
                </p>
            </div>
        </section>

        <section id="updates" class="section py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="container-epic px-4 sm:px-6 lg:px-8">
                <h2 class="section-title text-2xl font-headings gradient-text">Novedades</h2>
                <h3 class="font-headings text-xl mt-4">Últimos artículos</h3>
                <ul id="latest-articles" class="space-y-2"></ul>
                <h3 class="font-headings text-xl mt-8">Próximas visitas</h3>
                <ul id="upcoming-visits" class="space-y-2"></ul>
            </div>
        </section>

        <section id="legado" class="section alternate-bg spotlight-active py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="container-epic px-4 sm:px-6 lg:px-8">
                <h2 class="section-title text-2xl font-headings">Explora Nuestro Legado</h2>
                <div class="card-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="card">
                        <img class="w-full h-auto" src="/assets/img/PrimerEscritoCastellano.jpg" alt="Página de un manuscrito medieval iluminado, simbolizando la rica historia de Castilla">
                        <div class="card-content">
                            <h3 class="font-headings">Nuestra Historia</h3>
                            <p class="text-lg font-body">Desde los Concanos y la Civitate Auca Patricia hasta la formación del Condado. Sumérgete en los relatos que definieron Castilla.</p>
                            <a href="/historia/historia.php" class="read-more">Leer Más</a>
                        </div>
                    </div>
                    <div class="card">
                        <img class="w-full h-auto" src="/assets/img/RodrigoTabliegaCastillo.jpg" alt="Imponentes ruinas del Alcázar de Casio recortadas contra un cielo dramático">
                        <div class="card-content">
                            <h3 class="font-headings">Lugares Emblemáticos</h3>
                            <p class="text-lg font-body">Descubre el imponente Alcázar de Casio, los secretos de la Civitate Auca y otros tesoros arqueológicos que esperan ser explorados.</p>
                            <a href="/lugares/lugares.php" class="read-more">Explorar Sitios</a>
                        </div>
                    </div>
                    <div class="card">
                        <img class="w-full h-auto" src="/assets/img/Yanna.jpg" alt="Iglesia de Santa María de la Llana, ejemplo del patrimonio arquitectónico de Cerezo">
                        <div class="card-content">
                            <h3 class="font-headings">Planifica Tu Visita</h3>
                            <p class="text-lg font-body">Encuentra toda la información que necesitas para tu aventura en Cerezo de Río Tirón: cómo llegar, dónde alojarte y qué no te puedes perder.</p>
                            <a href="/visitas/visitas.php" class="read-more">Organizar Viaje</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="personajes" class="section py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="container-epic px-4 sm:px-6 lg:px-8">
                <h2 class="section-title text-2xl font-headings">Personajes de la Historia</h2>
                <div class="card-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="card">
                        <img class="w-full h-auto" src="/assets/img/Casio.png" alt="Retrato idealizado o ilustración del Conde Casio, figura histórica del siglo VIII">
                        <div class="card-content">
                            <h3 class="font-headings">Conde Casio</h3>
                            <p class="text-lg font-body">Figura fundamental del siglo VIII, se le atribuye la construcción o refuerzo del Alcázar de Cerezo.</p>
                            <a href="/personajes/Militares_y_Gobernantes/conde_casio_cerasio.html" class="read-more">Saber Más</a>
                        </div>
                    </div>
                    <div class="card">
                        <img class="w-full h-auto" src="/assets/img/GonzaloTellez.png" alt="Ilustración representando a Gonzalo Téllez, Conde de Lantarón y Cerezo">
                        <div class="card-content">
                            <h3 class="font-headings">Gonzalo Téllez</h3>
                            <p class="text-lg font-body">Conde de Lantarón y Cerezo (c. 897 - c. 913), personaje clave en la consolidación de los territorios.</p>
                            <a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/gonzalo_tellez.html" class="read-more">Saber Más</a>
                        </div>
                    </div>
                    <div class="card">
                        <img class="w-full h-auto" src="/assets/img/FernandoDiaz.png" alt="Representación artística de Fernando Díaz, conde castellano">
                        <div class="card-content">
                            <h3 class="font-headings">Fernando Díaz</h3>
                            <p class="text-lg font-body">Sucesor de Gonzalo Téllez, continuó la labor de defensa y organización en la primitiva Castilla.</p>
                            <a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/fernando_diaz.html" class="read-more">Saber Más</a>
                        </div>
                    </div>
                </div>
                 <p class="cta-group">
                    <a href="/personajes/indice_personajes.html" class="cta-button">Personajes</a>
                </p>
            </div>
        </section>
        
        <section id="timeline" class="section timeline-section-summary alternate-bg py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="container-epic px-4 sm:px-6 lg:px-8">
                <h2 class="section-title text-2xl font-headings">Nuestra Historia en el Tiempo</h2>
                <p class="timeline-intro text-lg font-body">Un recorrido conciso por los momentos más determinantes de nuestra región, desde la prehistoria hasta la consolidación del Condado. Cada época ha dejado una huella imborrable.</p>
                <p class="cta-group">
                    <a href="/secciones_index/historia_tiempo_resumen.html" class="cta-button">Explorar Resumen de la Historia</a>
                </p>
            </div>
        </section>

        <section id="inmersion" class="section immersion-section py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="container-epic px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-headings">Sumérgete en la Historia Viva de Tu Cultura</h2>
                <p class="text-lg font-body">
                    Esta web es más que información; es una puerta a tus raíces. Un viaje al origen del castellano y la identidad hispana te espera.
                    Siente la llamada de la historia y conecta con el legado que nos une.
                </p>
                <a href="/cultura/cultura.php" class="cta-button">Cultura</a>
            </div>
        </section>

        <section id="colabora" class="section py-12 sm:py-16 lg:py-20" data-aos="fade-up">
            <div class="container-epic px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="section-title text-2xl font-headings gradient-text">¿Quieres colaborar?</h2>
                <p class="text-lg font-body mt-4">Únete a nuestro foro y comparte tus ideas para preservar y difundir el legado de Cerezo de Río Tirón.</p>
                <p class="cta-group mt-6">
                    <a href="/foro/index.php" class="cta-button">Participar en el Foro</a>
                </p>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/fragments/footer.php'; ?>
    

</body>
</html>
