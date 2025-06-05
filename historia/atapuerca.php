<?php
if (session_status() == PHP_SESSION_NONE) {
    // Usar session_start() solo si no hay una sesión activa.
    // Es preferible no usar @ para suprimir errores aquí.
    if (session_status() !== PHP_SESSION_ACTIVE) {
         session_start();
    }
}
// Asumimos que db_connect.php establece $pdo
require_once __DIR__ . '/../dashboard/db_connect.php';
// text_manager.php incluye auth.php, así que $is_admin estará disponible indirectamente.
require_once __DIR__ . '/../includes/text_manager.php';
require_once __DIR__ . '/../includes/ai_utils.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atapuerca</title>
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
<?php require_once __DIR__ . '/../_header.html'; ?>
    <header class="page-header hero" style="background-image: linear-gradient(rgba(0,0,0, 0.5), rgba(0,0,0, 0.5)), url('../assets/img/placeholder.jpg');"> <!-- Basic hero style -->
        <div class="hero-content">
            <h1><?php editableText('atapuerca_titulo_hero', $pdo, 'Los Yacimientos de la Sierra de Atapuerca'); ?></h1>
        </div>
    </header>
    <main>
        <section class="section alternate-bg">
            <div class="container">
                <h2 class="section-title"><?php editableText('atapuerca_titulo_seccion', $pdo, 'Un Tesoro de la Prehistoria'); ?></h2>
                <p class="timeline-intro"><?php editableText('atapuerca_intro_p1', $pdo, 'La Sierra de Atapuerca, ubicada al norte de Ibeas de Juarros en la provincia de Burgos, es un enclave montañoso de modesta altitud que alberga un extraordinario conjunto de yacimientos arqueológicos y paleontológicos. Reconocida como Patrimonio de la Humanidad por la UNESCO, Espacio de Interés Natural y Bien de Interés Cultural, Atapuerca ha proporcionado al mundo testimonios fósiles de al menos cinco especies distintas de homínidos, arrojando luz invaluable sobre la evolución humana en Europa.'); ?></p>

                <div id="languageSelectorContainer" style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px dashed #ccc; text-align: center;">
                    <h4 style="font-family: 'Cinzel', serif; color: var(--epic-purple-emperor); margin-bottom: 10px;">Seleccionar Idioma (Demostración):</h4>
                    <button class="lang-btn active" data-lang="es" title="Ver contenido en Español (original)">Español (Original)</button>
                    <button class="lang-btn" data-lang="en-ai" title="Simular traducción al Inglés por IA">Inglés (Traducción IA)</button>
                    <button class="lang-btn" data-lang="fr-ai" title="Simular traducción al Francés por IA">Francés (Traducción IA)</button>
                </div>
                <article class="content-article"> <!-- Using a generic class for content styling -->
                    <div id="textoPrincipalAtapuerca">
                        <h3><?php editableText('atapuerca_ctx_geo_titulo', $pdo, 'Contexto Geográfico y Geológico'); ?></h3>
                        <p><?php editableText('atapuerca_ctx_geo_p1', $pdo, 'La sierra forma parte del "corredor de la Bureba", un paso histórico entre el valle del Ebro y la cuenca del Duero. Su formación se basa en calizas del Cretácico Superior (hace entre 80 y 100 millones de años). La acción fluvial y la naturaleza calcárea del terreno han originado un complejo sistema kárstico, con numerosas cuevas que, al colmatarse y sellarse, han preservado los restos durante milenios. La construcción de una línea de ferrocarril en el siglo XIX fue lo que inicialmente sacó a la luz estos importantes yacimientos.'); ?></p>

                        <h3><?php editableText('atapuerca_yacimientos_titulo', $pdo, 'Principales Yacimientos y Descubrimientos Clave'); ?></h3>
                    <p><?php editableText('atapuerca_yacimientos_p1', $pdo, 'Entre la multitud de cuevas y simas, destacan varias por la trascendencia de sus hallazgos:'); ?></p>
                    <ul>
                        <li><strong>Sima del Elefante:</strong><?php editableText('atapuerca_yac_sima_elefante', $pdo, 'Aquí se han encontrado restos de Homo sp. (probablemente una forma temprana de Homo erectus), con una antigüedad que ronda los 1.2 millones de años, representando algunos de los homínidos más antiguos de Europa.'); ?></li>
                        <li><strong>Gran Dolina (Estrato TD6):</strong><?php editableText('atapuerca_yac_gran_dolina', $pdo, 'Este yacimiento es famoso por el descubrimiento de Homo antecessor, una especie datada en al menos 800,000 años. Este hallazgo fue crucial para proponer una nueva especie de homínido, posiblemente ancestro común de neandertales y humanos modernos.'); ?></li>
                        <li><strong>Sima de los Huesos:</strong><?php editableText('atapuerca_yac_sima_huesos', $pdo, 'Un caso excepcional en el registro fósil mundial, esta sima ha proporcionado miles de fósiles pertenecientes a la especie Homo heidelbergensis. Entre ellos se encuentra el famoso Cráneo número 5, "Miguelón". Estos restos datan de hace unos 430,000 años y han permitido estudiar en detalle una población entera de homínidos.'); ?></li>
                    </ul>
                    <p><?php editableText('atapuerca_yacimientos_p2', $pdo, 'Además de estas especies, en Atapuerca también se ha documentado la presencia de Homo neanderthalensis y de nuestra propia especie, Homo sapiens, lo que demuestra una ocupación continuada del territorio a lo largo de la prehistoria.'); ?></p>

                        <h3><?php editableText('atapuerca_importancia_titulo', $pdo, 'Importancia Singular de Atapuerca'); ?></h3>
                        <p><?php editableText('atapuerca_importancia_p1', $pdo, 'La Sierra de Atapuerca no es solo un conjunto de cuevas con fósiles; es una ventana directa a la vida de nuestros antepasados. La increíble concentración de restos en buen estado de conservación, abarcando un periodo tan extenso (desde el Pleistoceno Inferior hasta el Holoceno), la convierten en un referente mundial para el estudio de la evolución humana. Los hallazgos han permitido comprender mejor las estrategias de caza, la tecnología lítica, las características físicas e incluso posibles prácticas rituales de las distintas especies de homínidos que habitaron esta sierra.'); ?></p>
                    </div>
                </article>
                <div style="text-align: center; margin-top: 30px; margin-bottom: 30px;"> <!-- Contenedor para centrar el botón -->
                    <button id="btnResumenIA" class="cta-button" style="padding: 12px 25px; font-size: 1.1em;">Ver Resumen Inteligente</button>
                </div>
                <div id="resumenIAContenedor" style="display:none; margin-top:20px; padding:20px; border:1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- El resumen generado por IA se insertará aquí -->
                </div>
                <div id="tagsSugeridosIA" class="tags-sugeridos-ia" style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #ccc;">
                    <h4 style="font-family: 'Cinzel', serif; color: var(--epic-purple-emperor); margin-bottom: 15px; text-align:center;">Etiquetas Sugeridas por IA:</h4>
                    <div id="listaTagsIA" class="lista-tags-ia" style="text-align:center;">
                        <?php
                        if (function_exists('get_suggested_tags_placeholder')) {
                            $suggested_tags = get_suggested_tags_placeholder('atapuerca');
                            if (!empty($suggested_tags)) {
                                foreach ($suggested_tags as $tag) {
                                    echo '<span class="tag-ia">' . htmlspecialchars($tag) . '</span> ';
                                }
                            } else {
                                echo '<p>No hay sugerencias de etiquetas por el momento.</p>';
                            }
                        } else {
                            // Esto no debería ocurrir si ai_utils.php se incluyó correctamente.
                            echo '<p>Error: La función de sugerencia de etiquetas no está disponible.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php require_once __DIR__ . '/../_footer.html'; ?>
    <script src="/js/layout.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnResumen = document.getElementById('btnResumenIA');
            const resumenContenedor = document.getElementById('resumenIAContenedor');

            if (btnResumen && resumenContenedor) {
                btnResumen.addEventListener('click', function() {
                    // Para esta demostración, obtenemos un resumen de placeholder generado por PHP.
                    // En una implementación real, aquí podría haber una llamada AJAX
                    // o se podría haber extraído el texto de 'textoPrincipalAtapuerca'
                    // y enviado a una función de resumen.

                    // Generamos el resumen placeholder directamente con PHP e imprimimos como string JS.
                    // Usamos una clave genérica ya que no estamos pasando el texto completo aquí.
                    const resumenHtml = <?php echo json_encode(get_smart_summary_placeholder('Contenido de Atapuerca')); ?>;

                    resumenContenedor.innerHTML = resumenHtml;
                    resumenContenedor.style.display = 'block';
                    // Opcional: Desplazar la vista al resumen
                    // resumenContenedor.scrollIntoView({ behavior: 'smooth' });
                });
            }
        });
        </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const langButtons = document.querySelectorAll('.lang-btn');
        const contentContainer = document.getElementById('textoPrincipalAtapuerca');
        const originalContentHTML = contentContainer ? contentContainer.innerHTML : ''; // Guardar el contenido HTML original

        // Preparar los textos de placeholder para cada idioma usando PHP
        // Esto se hace una vez al cargar la página para tenerlos listos en JS
        <?php
            // Asegurarse de que la función existe antes de llamarla
            $translation_placeholders = [];
            if (function_exists('get_simulated_translation_placeholder')) {
                // Para la demostración, no necesitamos pasar el texto original completo aquí,
                // la función get_simulated_translation_placeholder ya tiene una lógica de snippet.
                // Usaremos un content_id genérico para Atapuerca.
                $original_text_snippet_for_demo = "Contenido original de la página de Atapuerca..."; // Un snippet muy corto o incluso vacío
                $translation_placeholders['en-ai'] = get_simulated_translation_placeholder('atapuerca_main_content', 'en-ai', $original_text_snippet_for_demo);
                $translation_placeholders['fr-ai'] = get_simulated_translation_placeholder('atapuerca_main_content', 'fr-ai', $original_text_snippet_for_demo);
            }
        ?>
        const translations = <?php echo json_encode($translation_placeholders); ?>;

        if (contentContainer && langButtons.length > 0) {
            langButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Gestionar clase activa
                    langButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const targetLang = this.getAttribute('data-lang');

                    if (targetLang === 'es') {
                        contentContainer.innerHTML = originalContentHTML;
                    } else if (translations[targetLang]) {
                        contentContainer.innerHTML = translations[targetLang];
                    } else {
                        // Fallback si algo va mal (ej. idioma no definido en placeholders)
                        contentContainer.innerHTML = "<p>Traducción de demostración no disponible para este idioma.</p>";
                    }
                });
            });
        }
    });
    </script>
</body>
</html>
