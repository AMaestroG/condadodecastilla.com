<!DOCTYPE html>
<html lang="es">
<head>
<?php $load_aos = true; require_once __DIR__ . '/../includes/head_common.php'; ?>
</head>
<body class="alabaster-bg">

    <?php require_once __DIR__ . '/../fragments/header.php'; ?>

    <header class="page-header hero bg-[url('/assets/img/hero_historia_background.jpg')] bg-cover bg-center md:bg-center">
        <div class="hero-content">
            <h1 class="gradient-text">Línea de Tiempo de Nuestra Historia</h1>
            <p>Un recorrido cronológico por los eventos y eras que forjaron el Condado de Castilla y la identidad de Cerezo de Río Tirón.</p>
        </div>
    </header>

    <main>
        <section class="section timeline-section alternate-bg">
            <div class="container-epic">
                <h2 class="section-title gradient-text">Un Viaje Milenario</h2>
                <p class="timeline-intro">
                    Desde los primeros homínidos en la Sierra de Atapuerca hasta la configuración actual de nuestra comarca, 
                    te invitamos a explorar los hitos cruciales que han modelado nuestra rica identidad a lo largo de los siglos.
                </p>
                <ul class="timeline">
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                            <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Prehistoria">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Prehistoria: Atapuerca y Primeros Pobladores</h3>
                            <p>Los yacimientos de la Sierra de Atapuerca, Patrimonio de la Humanidad, revelan la presencia humana en Europa desde hace más de un millón de años. Descubre los vestigios de nuestros ancestros más remotos y su forma de vida en un entorno que ya prefiguraba la importancia estratégica de la región.</p>
                            <a href="atapuerca.php" class="read-more timeline-read-more">Explorar Atapuerca</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                             <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Pueblos Prerromanos">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Pueblos Prerromanos: Berones, Autrigones y Cántabros</h3>
                            <p>Antes de la llegada de Roma, la región fue habitada por diversas tribus celtíberas como los Berones, Autrigones y los aguerridos Cántabros (Concanos/Caucanos). Estos pueblos dejaron una profunda huella en el territorio a través de sus castros, ritos y una feroz resistencia que definiría su carácter.</p>
                             <a href="#prerromanos-detalle" class="read-more timeline-read-more">Conocer los Pueblos</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                             <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Romanización">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Romanización: Auca Patricia y el Legado Imperial</h3>
                            <p>La conquista romana transformó la península. Auca Patricia (cerca de Cerezo) se erigió como una importante civitas y sede episcopal, integrando la región en la vasta red del Imperio Romano y dejando un legado arqueológico monumental que incluye calzadas, villas y obras públicas.</p>
                             <a href="#romanizacion-detalle" class="read-more timeline-read-more">Ver Época Romana</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                             <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Época Visigoda">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Época Visigoda: Continuidad y Transformación</h3>
                            <p>Tras la caída del Imperio Romano de Occidente, los visigodos establecieron su reino en Hispania. Auca Patricia mantuvo su relevancia como sede episcopal, y la región experimentó una mezcla de continuidad romana y nuevas influencias germánicas, sentando las bases para la futura Castilla.</p>
                             <a href="#visigoda-detalle" class="read-more timeline-read-more">Explorar Periodo Visigodo</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                             <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Presencia Árabe">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Presencia Árabe y Resistencia Cristiana (Siglos VIII-X)</h3>
                            <p>La conquista musulmana de la península en el 711 transformó el panorama político. Aunque la presencia árabe directa en estas tierras del norte pudo ser menos duradera o intensa que en otras zonas, la región se convirtió en una crucial zona de frontera. El Alcázar de Casio, atribuido al Conde Casio (figura de transición), y la posterior organización de los condados cristianos fueron respuestas directas a esta nueva realidad, marcando el inicio de la Reconquista y la forja de la identidad castellana.</p>
                             <a href="#arabe-detalle" class="read-more timeline-read-more">La Frontera y el Origen</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                             <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Alta Edad Media">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Alta Edad Media: El Nacimiento del Condado de Castilla</h3>
                            <p>En este contexto de reinos cambiantes y la Reconquista, surge y se consolida el Condado de Castilla. Figuras como Rodrigo, Diego Rodríguez Porcelos y Gonzalo Téllez forjaron en Cerezo y Lantarón un bastión fundamental, marcando el inicio de una nueva entidad política y cultural que daría forma a la futura España.</p>
                             <a href="#altaedadmedia-detalle" class="read-more timeline-read-more">El Condado</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                            <img src="/assets/img/estrellaGordita.png" alt="Hito en la línea de tiempo: Explorando los Orígenes de Castilla">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Explorando Auca Patricia, Cerasio y los Orígenes de Castilla</h3>
                            <p>Un análisis detallado sobre la importancia de Auca Patricia, el Alcázar de Cerasio y su papel fundamental en la historia y origen de Castilla, basado en investigaciones y textos históricos.</p>
                            <a href="/historia/nuestra_historia_nuevo4.php" class="read-more timeline-read-more">Leer Más Sobre los Orígenes de Castilla</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                            <img src="/assets/img/estrellaGordita.png" alt="Hito en la línea de tiempo: Documentos Históricos">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Documentos Históricos del Condado</h3>
                            <p>Accede a una colección de documentos, manuscritos y archivos relevantes para la investigación y comprensión de la historia de Cerezo de Río Tirón y el Condado de Castilla. Incluye PDFs, textos y otros formatos.</p>
                            <a href="/historia/documentos/index.html" class="read-more timeline-read-more">Explorar Documentos</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                            <img src="/assets/img/estrellaGordita.png" alt="Hito en la línea de tiempo: Galería Histórica">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Galería Visual de la Historia</h3>
                            <p>Descubre imágenes, mapas antiguos, fotografías de artefactos y otras representaciones visuales que ilustran la rica herencia histórica de nuestra región. Cada elemento incluye una descripción detallada.</p>
                            <a href="/historia/galeria_historica/index.html" class="read-more timeline-read-more">Visitar Galería</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                             <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Plena y Baja Edad Media">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Plena y Baja Edad Media: Consolidación y Señoríos</h3>
                            <p>El Condado se convierte en Reino, expandiendo su influencia. La región de Cerezo y el Alfoz de Lantarón viven bajo diferentes señoríos, con la construcción y reforma de castillos, murallas e iglesias que aún hoy definen el paisaje y narran historias de poder y fe.</p>
                             <a href="#plenabajaedadmedia-detalle" class="read-more timeline-read-more">Ver la Evolución</a>
                        </div>
                    </li>
                    <li class="timeline-item scroll-fade opacity-0 transition-opacity duration-700" data-aos="fade-up" tabindex="0">
                        <div class="timeline-icon">
                             <img src="/assets/img/estrella.png" alt="Hito en la línea de tiempo: Edad Moderna y Contemporánea">
                        </div>
                        <div class="timeline-content">
                            <h3 class="gradient-text">Edad Moderna y Contemporánea: De los Austrias a Nuestros Días</h3>
                            <p>Los avatares de la historia de España siguen marcando la región, desde el Antiguo Régimen, pasando por las guerras y transformaciones sociales del siglo XIX y XX, hasta el presente, donde se busca preservar y difundir un rico patrimonio cultural e histórico.</p>
                             <a href="#edadmodernacontemporanea-detalle" class="read-more timeline-read-more">Historia Reciente</a>
                        </div>
                    </li>
                </ul>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../fragments/footer.php'; ?>
    
</body>
</html>
