<!DOCTYPE html>
<html lang="es">
<head>
<?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <title>Lugares Emblemáticos - Condado de Castilla</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">


    <link rel="stylesheet" href="/assets/css/custom.css">

</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . '/../fragments/header.php'; ?>

    <header class="page-header hero bg-[url('/assets/img/hero_lugares_background.jpg')] bg-cover bg-center md:bg-center">
        <div class="hero-content">
            <img loading="lazy" src="/assets/img/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Lugares Emblemáticos: Huellas de la Historia</h1>
            <p>Un recorrido por los vestigios que narran el pasado milenario de Cerezo de Río Tirón y el Condado de Castilla.</p>
        </div>
    </header>

    <main>
        <section class="section detailed-intro-section"> <div class="container page-content-block"> <p class="intro-paragraph">
                    Cerezo de Río Tirón y sus alrededores son un museo al aire libre, donde cada piedra y cada sendero 
                    cuentan una historia. Desde imponentes fortalezas hasta humildes vestigios de antiguas civilizaciones,
                    te invitamos a descubrir los lugares que han definido la identidad de esta tierra.
                </p>

                <section class="section alfoz-list-section">
                    <div class="container page-content-block">
                        <h2>Pueblos del Alfoz de Cerezo y Lantarón</h2>
                        <p>Explora los diversos pueblos que forman parte del histórico Alfoz de Cerezo y Lantarón, cada uno con su propia historia y encanto.</p>
                        <div id="dynamic-alfoz-list-container" class="card-grid">
                            <!-- Places will be loaded here by JavaScript -->
                            <p>Cargando lista de pueblos...</p>
                        </div>
                    </div>
                </section>

                <article class="emblematic-place">
                    <h2><i class="fas fa-chess-rook"></i> El Alcázar de Cerezo y las "Setefenestras"</h2>
                    <div class="place-description">
                        <p>
                            Coronando la villa, el Alcázar de Cerezo, también conocido como Castillo de los Condes de Haro, 
                            es el símbolo por excelencia del poderío medieval de la región. Aunque sus orígenes podrían ser 
                            anteriores, su fisonomía actual data principalmente de los siglos XIV y XV. Sus robustos muros y 
                            torres fueron testigos de disputas nobiliarias y de la defensa del territorio.
                        </p>
                        <p>
                            Un elemento distintivo y enigmático son las llamadas "Setefenestras" (Siete Ventanas), una serie 
                            de vanos en uno de sus muros que han alimentado leyendas e interpretaciones. Algunos sugieren 
                            funciones defensivas específicas, mientras que otros les atribuyen un significado simbólico. 
                            Explorar el alcázar es sumergirse en la época de los condes y caballeros.
                        </p>
                    </div>
                    <div class="place-gallery">
                        <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Detalle de las Setefenestras en el Alcázar de Cerezo">
                        <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Vista general del Alcázar de Cerezo dominando el paisaje">
                        <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Primer plano de las antiguas murallas de piedra del Alcázar">
                    </div>
                     <!-- <p class="link-to-page"><a href="/lugares/alcazar_casio.php" class="read-more">Más sobre el Alcázar de Casio</a></p> -->
                </article>

                <article class="emblematic-place">
                    <h2><i class="fas fa-landmark"></i> Civitate Auca Patricia: Esplendor Romano y Visigodo</h2>
                    <div class="place-description">
                        <p>
                            A pocos kilómetros de la actual Cerezo, se encuentran los restos de lo que fue una de las ciudades 
                            más importantes de la región en la antigüedad: la Civitate Auca Patricia. Fundada por los romanos, 
                            probablemente sobre un asentamiento prerromano Autrigón, Auca alcanzó un notable desarrollo como 
                            nudo de comunicaciones y centro administrativo.
                        </p>
                        <p>
                            Los hallazgos arqueológicos, que incluyen mosaicos, restos de edificaciones públicas, calzadas y 
                            necrópolis, atestiguan su importancia. Durante la época visigoda, Auca fue sede episcopal, lo que 
                            refuerza su estatus como centro de poder religioso y político.
                        </p>
                    </div>
                    <div class="place-gallery">
                         <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Mosaico romano descubierto en el yacimiento de Auca Patricia">
                        <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Ruinas excavadas de edificaciones en la Civitate Auca Patricia">
                    </div>
                </article>

                <article class="emblematic-place">
                    <h2><i class="fas fa-mountain" aria-hidden="true"></i> Huellas Prerromanas: El Culebrón y Charrera</h2>
                     <div class="place-description">
                        <p>
                            Antes de la llegada de Roma, estas tierras estaban habitadas por pueblos como los Concanos o Caucanos, 
                            que dejaron su impronta en el paisaje. Aunque los vestigios son a menudo más esquivos que los de 
                            épocas posteriores, algunos lugares conservan ecos de este pasado ancestral.
                        </p>
                        <h3>El Culebrón</h3>
                        <p>
                            El paraje conocido como "El Culebrón" es un sitio de gran interés arqueológico, donde se han
                            identificado posibles restos de asentamientos o lugares de culto prerromanos. Su nombre, evocador y
                            misterioso, podría estar relacionado con antiguas leyendas o con la topografía del terreno.
                            <a href="/lugares/el_culebron.php" class="read-more">Leer más</a>
                        </p>
                        <h3>Charrera</h3>
                        <p>
                            Similarmente, "Charrera" es otro topónimo que resuena con la historia más antigua. Se asocia con 
                            hallazgos que sugieren la presencia de comunidades prerromanas, posiblemente cántabras (caucanas o concanas). 
                            La exploración y estudio de estos sitios son fundamentales para reconstruir el puzle de las 
                            civilizaciones que nos precedieron.
                        </p>
                    </div>
                     <div class="place-gallery">
                         <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Paisaje del yacimiento prerromano de El Culebrón">
                        <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Hallazgos arqueológicos del sitio de Charrera">
                    </div>
                </article>
                
                <article class="emblematic-place">
                    <h2><i class="fas fa-archway"></i> Otras Ruinas y Vestigios en Cerezo</h2>
                    <div class="place-description">
                        <p>
                            Más allá de los grandes nombres, el término municipal de Cerezo de Río Tirón está salpicado de 
                            numerosas ruinas y vestigios que atestiguan su denso pasado histórico. Ermitas abandonadas, 
                            restos de antiguas construcciones defensivas, puentes medievales y fragmentos de calzadas 
                            nos hablan de una ocupación continua y de la importancia estratégica del lugar a lo largo de los siglos.
                            Cada rincón puede esconder una historia, invitando al visitante curioso a una exploración atenta 
                            del paisaje.
                        </p>
                    </div>
                     <div class="place-gallery">
                         <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Ermita en ruinas en los alrededores de Cerezo">
                        <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Antiguo puente medieval sobre el Río Tirón">
                        <img loading="lazy" src="/assets/img/placeholder.jpg" alt="Restos de una antigua muralla o construcción defensiva en Cerezo">
                    </div>
                </article>

                <div class="map-placeholder">
                    <i class="fas fa-map-marked-alt" style="font-size: 3em; margin-bottom: 0.5em; color: var(--epic-gold-main);"></i>
                    <h3>Mapa Interactivo de Lugares</h3>
                    <p>Próximamente: Un mapa interactivo con la ubicación de todos los lugares emblemáticos para facilitar tu exploración.</p>
                    <a href="/visitas/visitas.php" class="cta-button cta-button-small">Planifica Cómo Visitarlos</a>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../fragments/footer.php'; ?>

    
    <script src="/js/lugares-data.js"></script>
    <script src="/js/lugares-dynamic-list.js"></script>

</body>
</html>
