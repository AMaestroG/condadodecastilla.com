<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . "/../../includes/head_common.php"; ?>
    <?php
    require_once __DIR__ . "/../../includes/load_page_css.php";
    load_page_css();
    ?>
    <title><?php echo htmlspecialchars($titulo_pagina_actual); ?> - Cerezo de Río Tirón</title>
</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . "/../../fragments/header.php"; ?>

<?php
$titulo_pagina_actual = "Auca Patricia: Ubicación";
$id_tema_actual = "auca_patricia_ubicacion"; // ID de esta página específica
// Título de la página actual (podría venir del JSON si esta página también fuera generada por el script)
// Asumiendo que $pdo no está disponible aún, o para asegurar que sí lo esté.
require_once __DIR__ . '/../../../includes/db_connect.php'; // Ajustar ruta si es necesario
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
}
require_once __DIR__ . '/../../../includes/text_manager.php';
?>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">
    <header id="hero-auca-patricia-ubicacion" class="page-header hero">
        <div class="hero-content">
            <h1><?php echo htmlspecialchars($titulo_pagina_actual); ?></h1>
        </div>
    </header>
    <main>
        <section class="section">
            <div class="container-epic">
                <?php
                require_once __DIR__ . "/../../fragments/breadcrumbs.php";
                render_breadcrumbs(["historia" => "Nuestra Historia", "subpaginas" => "Índice Detallado"]);
                ?>
                <div class="article-content">
                    <p><a href="/historia/subpaginas_indice.php" class="back-link">&laquo; Volver al Índice Detallado</a></p>
                    <h3>La Identificación de Auca Patricia</h3>
                    <?php editableText('auca_identificacion_p1', $pdo, 'Y hay muchas, muchas referencias a Auca como ciudad Patricia Romana antes de ser ser Patricia Visigoda. Capital de la provincia Cantabrica y Vardulias... Que luego no digan que Castilla y Vardulias y auka paterniani no están relacionadas con Cerasio y el origen de Castilla.', 'p', ''); ?>
                    <?php editableText('auca_identificacion_p2', $pdo, 'Aquí se evidencia la indefinición de la localización de la civitate, de la ciudad de Auca. Pues si sabemos dónde hay una ciudad Romana con 1200 metros de decumanus y 1200 metros de cardo... En Cerezo de Río Tirón. ciudad de Auca.', 'p', ''); ?>
                    <h4>Dimensiones y Estructura Urbana Romana</h4>
                    <?php editableText('auca_dimensiones_p1', $pdo, 'Y la ciudad de Oca, Auca Patricia, Civitate Auki Patriciani, Civita de Aucam, fue fundada por Cesar Augusto en Segisamam donde puso su cuartel General en las guerras cántabras, convirtiéndola en la Capital de la Cantábrica. Donde estaba el ultimo puerto Fluvial navegable del Río Ebro, en el Gurugu de Segisamam. Aun se puede ver el puerto romano en el termino de Glera de los Celox (barcos de legionarios romanos en ríos).', 'p', ''); ?>
                    <p>Y medir las puertas de la ciudad que encajan en tamaño posición y orientación con la decumanus de Auca Patricia.</p>
                    <p>En Cerezo de Río Tirón que actualmente y siempre es Oca, hay una civita Romana de 144 hectáreas rodeada de murallas romanas de hormigón romano de 6 metros de ancho, mas sus segunda murallas y fosos... una capital Patricia con Obispado.</p>
                    <p>Las calzadas romanas y el ultimo puerto fluvial romano sobre el rio Ebro esta en la Glera de los Celox (embarcación romana de legion en Rios). En Cerezo de Rio tirón aun puedes ver miles de ruinas enfrente de su Alcázar, puedes medir la carretera que va desde la caseta(cruce entre Tormmantos y Belorado) hasta el Túnel 1200 metros de Cardo (Norte-Sur) y 1200 metros de Decumanus (Este-Oeste) aun se puede apreciar los cimientos de la puerta decumanus siniestra. En esos 1200 km veras a ambos lados de la carreta las ruinas de la civita de Auca Patricia la capital de Cantabria pues Cesar Augusto puso su cuartel general en las Guerras Cántabras en territorio Berón, enfrente de Segisamam en el ultimo puerto navegable del río Ebro, en la calzada de Tarraco a Astorga, al irse Augusto, la provincia recién conquistada siguió gobernándose desde la silla de Cesar Augusto hasta su destrucción en el 714 por Muza Ibn Nuhasir.</p>
                    <p>Aun si dudas puedes ver que la civita esta rodeada por el paseo de la cárcava(foso romano) pues no hay cárcava vista, y si escabas un poco podrás ver la murallas de hormigón de 6 metros rodeando 144 hectáreas de ciudad con obispado. Pero un paseo por la civita Berona en los palacios de Cerezo te dejara ver miles de restos romanos.</p>
                    <h4>Relevancia como Capital</h4>
                    <p>Desde Cerezo se gobernaba las Bardulias, dese la cuidad de Auka Patricia (patriniani) con nuestra episcopi de San Martín con 116 metros de largo, toda la provincia Cantábrica en tiempos visigodos (los montes de Cantabria en la Rioja). Somos el Origen de Castilla y del Idioma Castellano y la ciudad de Auka Patricia. Con los restos de la ciudad construyeron el Alcázar con el el Alfoz de Castilla, y sus Condes de Castilla y Álava gobernando desde su Alcazar en la Alcazaba.</p>
                    <p>En la Ciudad de Auca Patricia capital visigoda de la Cantabrica, desde donde se gobiernan las bardulias, desde la civittate Patricia o patriciani, desde la episcopi de San Martín, desde el Monumental Alcázar con su alfoz que se construyo con los restos de una civita Romana de oca, capital de la cantábrica pues cesar desembarco en segisamaclo sus legiones para su conquista. En Cerezo de Río Tirón origen de Castilla de su Cultura y de su Idioma con Valuesta en su Alfoz.</p>
                    <p>Auca con titulo de patricia, capital de una provincia, Civitate Auca Patricia capital de Cantabria y las Bardulias. <a href="https://books.google.com/books?id=KVpRAAAAcAAJ&pg=RA3-PA291">Historia de los reyes de Castilla y de León, Don Fernando el Magno ... - Prudencio de SANDOVAL - Google</a></p>
                    <p>La Civitate Auca Patricia es la capital de Cantabria, dice San Braulio en la Vida de San Millan que Leovigildo conquista y destruye las murallas y torres de la civita Romana, solo deja tres torres descubiertas. Por eso tarda en entrar en los Concilios Visigodos, pues hasta el 574 que se conquista el ultimo reducto del imperio romano de occidente por Leovigildo, no puede entrar en los concilios visigodos.</p>
                    <h4>Interpretaciones y Evidencia Arqueológica</h4>
                    <p>Y por si a alguien del público no le queda claro cuánto mide una Civitate Patriciani, pues ahí van unas medias disuasorias para otras disparadas teorías sobre la localización de Auka Patricia capital de la Cantabrica Visigoda con las medidas de su Episcopi San Martín pues Auka Patricia es sede episcopal Así que quiero ver algo parecido a una Catedral. Esperando me tienen sus comparaciones. Me temo que insultos si, pero pruebas científicamente cuantificables.... Les faltan tegulas y les sobras letras.</p>
                    <p>Matizar que la ciudad fue Abandonada en el siglo VIII y no se construyo nada hasta finales del siglo XX, bueno, nada no es correcto, se edifico un ermita, la de San Martín, aun puedes ver su retablo en la iglesia de San Nicolas de Barí, en Cerezo de Rio Tirón.</p>
                </div> <!-- Cierre de .article-content -->
            </div> <!-- Cierre de .container (para la sección) -->
        </section>
    </main>
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
            });
    </script>

    <?php require_once __DIR__ . "/../../fragments/footer.php"; ?>
    
</body>
</html>
