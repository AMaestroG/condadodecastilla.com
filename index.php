<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once 'includes/auth.php';      // For is_admin_logged_in()
require_once 'dashboard/db_connect.php'; // Provides $pdo
require_once 'includes/text_manager.php';// For editableText()
?>
<!--
    IMPORTANTE: Este archivo ha sido convertido a PHP para permitir contenido editable.
    Las llamadas a `editableText()` que verá en este archivo son ejemplos. Usted necesitará:
    1. Identificar los elementos de texto específicos que desea hacer editables en esta página.
    2. Elegir 'text_id' únicos y descriptivos para cada elemento editable.
    3. Proporcionar el contenido por defecto apropiado para su sitio dentro de la función `editableText()`.
    4. Asegurarse de que la etiqueta HTML contenedora (ej: 'h1', 'p', 'div') y cualquier clase CSS sean correctas para su diseño.
    5. Verificar que 'dashboard/db_connect.php' tenga credenciales de base de datos válidas.
    6. Iniciar sesión con el usuario administrador (ej: Rodrigo Tabliega / Rudericus) para ver los enlaces de edición (✏️).
    7. La tabla `site_texts` debe existir en su base de datos (creada por `01_create_tables.sql`).
    8. Puede que necesite ajustar las rutas de CSS/JS si la conversión a .php afecta las rutas relativas.
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Condado de Castilla - Cuna de tu Cultura y Lengua</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">

</head>
<body>
    <?php require_once __DIR__ . '/_header.html'; ?>

    <header class="hero">
        <div class="hero-content">
            <img src="/assets/img/escudo.jpg" alt="Escudo del Condado de Castilla: castillo dorado sobre fondo púrpura con una estrella de 8 puntas dorada encima." class="hero-escudo">
            <div>
                <?php editableText('hero_titulo_index', $pdo, 'Condado de Castilla: Cuna de Tu Cultura e Idioma', 'h1', ''); ?>
                <?php editableText('hero_parrafo_index', $pdo, 'Explora las ruinas del Alcázar de Casio, la Civitate Auca Patricia y descubre el origen de tu cultura milenaria en Cerezo de Río Tirón.', 'p', ''); ?>
            </div>
        </div>
        <a href="/historia/historia.html" class="cta-button">Descubre la Historia</a>
    </header>

    <section class="video-section section spotlight-active">
        <div class="container">
            <h2 class="section-title">Un Vistazo a Nuestra Tierra</h2>
            <div class="video-container">
                <iframe 
                    src="https://drive.google.com/file/d/1wm74VmKH21Nz7zFUkY8a8Z9672D4cyHN/preview" 
                    title="Video promocional del Condado de Castilla y Cerezo de Río Tirón" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </section>

    <main>
        <section class="section detailed-intro-section spotlight-active">
            <div class="container">
                <?php editableText('memoria_titulo_index', $pdo, 'Recuperando la Memoria de la Hispanidad Castellana', 'h2', ''); ?>
                <?php editableText('memoria_parrafo_index', $pdo, 'Un profundo análisis de nuestras raíces culturales, la importancia de la arqueología y el legado de la Civitate Auca Patricia. Descubre cómo el pasado de Cerezo de Río Tirón es fundamental para entender la Hispanidad.', 'p', ''); ?>
                <p style="margin-top: 2.5em;">
                    <a href="/secciones_index/memoria_hispanidad.html" class="cta-button">Leer Más Sobre Nuestra Memoria</a>
                </p>
            </div>
        </section>

        <section class="section alternate-bg spotlight-active">
            <div class="container">
                <h2 class="section-title">Explora Nuestro Legado</h2>
                <div class="card-grid">
                    <div class="card">
                        <img src="/assets/img/PrimerEscritoCastellano.jpg" alt="Página de un manuscrito medieval iluminado, simbolizando la rica historia de Castilla">
                        <div class="card-content">
                            <h3>Nuestra Historia</h3>
                            <p>Desde los Concanos y la Civitate Auca Patricia hasta la formación del Condado. Sumérgete en los relatos que definieron Castilla.</p>
                            <a href="/historia/historia.html" class="read-more">Leer Más</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="/assets/img/RodrigoTabliegaCastillo.jpg" alt="Imponentes ruinas del Alcázar de Casio recortadas contra un cielo dramático">
                        <div class="card-content">
                            <h3>Lugares Emblemáticos</h3>
                            <p>Descubre el imponente Alcázar de Casio, los secretos de la Civitate Auca y otros tesoros arqueológicos que esperan ser explorados.</p>
                            <a href="/lugares/lugares.html" class="read-more">Explorar Sitios</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="/assets/img/Yanna.jpg" alt="Iglesia de Santa María de la Llana, ejemplo del patrimonio arquitectónico de Cerezo">
                        <div class="card-content">
                            <h3>Planifica Tu Visita</h3>
                            <p>Encuentra toda la información que necesitas para tu aventura en Cerezo de Río Tirón: cómo llegar, dónde alojarte y qué no te puedes perder.</p>
                            <a href="/visitas/visitas.html" class="read-more">Organizar Viaje</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
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
                 <p style="margin-top: 2.5em;">
                    <a href="/personajes/indice_personajes.html" class="cta-button">Personajes</a>
                </p>
            </div>
        </section>
        
        <section class="section timeline-section-summary alternate-bg">
            <div class="container">
                <h2 class="section-title">Nuestra Historia en el Tiempo</h2>
                <p class="timeline-intro">Un recorrido conciso por los momentos más determinantes de nuestra región, desde la prehistoria hasta la consolidación del Condado. Cada época ha dejado una huella imborrable.</p>
                <p style="margin-top: 2.5em;">
                    <a href="/secciones_index/historia_tiempo_resumen.html" class="cta-button">Explorar Resumen de la Historia</a>
                </p>
            </div>
        </section>

        <section class="section immersion-section">
            <div class="container">
                <h2>Sumérgete en la Historia Viva de Tu Cultura</h2>
                <p>
                    Esta web es más que información; es una puerta a tus raíces. Un viaje al origen del castellano y la identidad hispana te espera.
                    Siente la llamada de la historia y conecta con el legado que nos une.
                </p>
                <a href="/cultura/cultura.html" class="cta-button">Cultura</a>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/_footer.html'; ?>

    <script src="/js/layout.js"></script>

</body>
</html>
