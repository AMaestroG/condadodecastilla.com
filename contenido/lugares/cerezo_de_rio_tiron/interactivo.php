<?php require_once __DIR__ . '/../../../includes/head_common.php'; ?>
<body>
    <?php require_once __DIR__ . '/../../../_header_template.php'; ?>

    <main class="container page-content-block">
        <h2>Participa y Descubre</h2>
<h3>Deja tu Comentario</h3>
<p>Comparte tus pensamientos, recuerdos o preguntas sobre Cerezo de Río Tirón.</p>
<!-- Formulario de comentarios y lista de comentarios se manejarán mediante la plantilla y JavaScript -->
<div id="comments-placeholder"></div>
<p class="text-center" style="font-size: 0.8em; margin-top: 1em;">
    <em><strong>Nota:</strong> Los comentarios se guardan localmente en tu navegador usando localStorage y no son visibles para otros usuarios en esta versión de demostración. Se requiere un backend para una funcionalidad completa.</em>
</p>

<h3>Comparte Tu Historia</h3>
<p>¿Tienes alguna anécdota personal, un recuerdo familiar o un dato curioso sobre Cerezo de Río Tirón que te gustaría compartir? Nos encantaría escucharlo. Envíanos tu historia y podríamos destacarla (con tu permiso).</p>
<div class="text-center" style="margin-top: 1em;">
    [Enviar Mi Historia](mailto:historias@condadodecastilla.com?subject=Historia%20sobre%20Cerezo%20de%20Río%20Tirón){: .cta-button .cta-button-small}
</div>

<h3>Pon a Prueba Tus Conocimientos</h3>
<!-- El Quiz interactivo se manejará mediante la plantilla y JavaScript -->
<div id="quiz-placeholder">
    <p><strong>1. ¿Cuál de estos nombres se asocia con una antigua ciudad romana cerca de Cerezo?</strong> (Auca Patricia)</p>
    <p><strong>2. ¿Qué conde es tradicionalmente asociado con la fortificación del primer castillo de Cerezo en el siglo VIII?</strong> (Conde Casio)</p>
    <p><strong>3. ¿Qué importante ruta de peregrinación pasaba por Cerezo de Río Tirón?</strong> (Camino de Santiago)</p>
</div>

<h3>Explora el Legado Visual</h3>
<p>Descubre más sobre la historia y el patrimonio de nuestra tierra a través de las aportaciones de la comunidad en nuestro Museo y Galería Colaborativa. Podrías encontrar piezas y fotografías directamente relacionadas con Cerezo.</p>
<p><img alt="Ejemplo de pieza del museo colaborativo" src="/assets/img/museo_ceramica_ejemplo.jpg" title="Piezas del Museo" />
<img alt="Ejemplo de foto de la galería colaborativa" src="/assets/img/galeria_colaborativa/ejemplo_atardecer_alcazar.jpg" title="Fotos de la Galería" /></p>
<div class="text-center" style="margin-top: 1em; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 15px;">
    [Visitar Museo](/museo/museo.php){: .cta-button .cta-button-small}
    [Visitar Galería](/galeria/galeria_colaborativa.php){: .cta-button .cta-button-small}
</div>
    </main>

    <?php require_once __DIR__ . '/../../../_footer.php'; ?>
    <script src="/js/layout.js"></script>
</body>
</html>
