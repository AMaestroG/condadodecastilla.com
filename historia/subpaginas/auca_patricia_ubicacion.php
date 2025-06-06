<?php
$id_tema_actual = "auca_patricia_ubicacion"; // ID de esta página específica

// Variables para los breadcrumbs
$breadcrumb_inicio_url = "/index.php"; // O la URL correcta de tu página de inicio
$breadcrumb_inicio_texto = "Inicio";

$breadcrumb_historia_url = "/historia/historia.html"; // Enlace a la página principal de historia
$breadcrumb_historia_texto = "Nuestra Historia"; // Texto por defecto

$breadcrumb_indice_url = "/historia/subpaginas_indice.php";
$breadcrumb_indice_texto = "Índice Detallado"; // Texto por defecto

$breadcrumb_tema_actual_texto = "Tema Actual"; // Texto por defecto

// Cargar JSON de índice detallado
$json_indice_detallado_path = __DIR__ . '/../../../data/historia/indice_detallado.json';
$indice_detallado_data = null;
$error_message_breadcrumb = '';

if (file_exists($json_indice_detallado_path)) {
    $json_content = file_get_contents($json_indice_detallado_path);
    $decoded_data = json_decode($json_content, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $indice_detallado_data = $decoded_data;
        $breadcrumb_indice_texto = $indice_detallado_data['titulo_pagina'] ?? $breadcrumb_indice_texto;

        // Encontrar el título del tema actual
        if (isset($indice_detallado_data['temas_detallados']) && is_array($indice_detallado_data['temas_detallados'])) {
            foreach ($indice_detallado_data['temas_detallados'] as $tema) {
                if (isset($tema['id_tema']) && $tema['id_tema'] === $id_tema_actual) {
                    $breadcrumb_tema_actual_texto = $tema['titulo_tema'] ?? $breadcrumb_tema_actual_texto;
                    break;
                }
            }
        }
    } else {
        $error_message_breadcrumb .= 'Error al decodificar indice_detallado.json. ';
    }
} else {
    $error_message_breadcrumb .= 'No se pudo encontrar indice_detallado.json. ';
}

// Cargar JSON de historia_indice (para el título de "Nuestra Historia", si aplica)
$json_historia_indice_path = __DIR__ . '/../../../data/historia/historia_indice.json';
if (file_exists($json_historia_indice_path)) {
    $json_content_hist = file_get_contents($json_historia_indice_path);
    $decoded_data_hist = json_decode($json_content_hist, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($decoded_data_hist['titulo_general'])) {
        // Podrías buscar una sección específica si la estructura lo permitiera mejor.
        // Por ahora, si queremos ser más específicos, podríamos tomar el título de una sección si coincide con "historia.html"
        // o simplemente usar el $breadcrumb_historia_texto = $decoded_data_hist['titulo_general']; si es apropiado.
        // Para este ejemplo, mantendré el "Nuestra Historia" fijo ya que la estructura de historia_indice.json no está diseñada para esto.
    } else {
        $error_message_breadcrumb .= 'Error al decodificar historia_indice.json o título no encontrado. ';
    }
} else {
    $error_message_breadcrumb .= 'No se pudo encontrar historia_indice.json. ';
}

// Título de la página actual (podría venir del JSON si esta página también fuera generada por el script)
$titulo_pagina_actual = $breadcrumb_tema_actual_texto; // Usar el título del tema para el <title>

// Asumiendo que $pdo no está disponible aún, o para asegurar que sí lo esté.
require_once __DIR__ . '/../../../dashboard/db_connect.php'; // Ajustar ruta si es necesario
/** @var PDO $pdo */
require_once __DIR__ . '/../../../includes/text_manager.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina_actual); ?> - Cerezo de Río Tirón</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">
    <style>
        .article-content p {
            margin-bottom: 1em;
            line-height: 1.6;
        }
        .article-content h3 {
            font-family: 'Cinzel', serif;
            color: var(--color-secundario-dorado);
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        .article-content h4 {
            font-family: 'Lora', serif;
            font-style: italic;
            color: var(--color-primario-purpura);
            margin-top: 1em;
            margin-bottom: 0.3em;
        }
        .article-content a {
            color: var(--color-acento-rojo);
            text-decoration: underline;
        }
        .article-content a:hover {
            color: var(--color-secundario-dorado);
        }
        .back-link {
            display: inline-block;
            margin-top: 1em; /* Adjusted from margin-bottom to be after breadcrumbs */
            margin-bottom: 1em;
            font-size: 0.9em;
            color: var(--color-primario-purpura);
            text-decoration: none;
            border: 1px solid var(--color-primario-purpura);
            padding: 0.5em 1em;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .back-link:hover {
            background-color: var(--color-primario-purpura);
            color: var(--color-blanco-fondo);
        }

        /* Breadcrumb Styles */
        .breadcrumb-container {
            padding: 0.75em 0;
            margin-bottom: 1em; /* Add some space below breadcrumbs */
        }
        .breadcrumb {
            display: flex;
            flex-wrap: wrap; /* Allow wrapping on small screens */
            list-style: none;
            padding: 0;
            margin: 0;
            font-family: 'Lora', serif;
            font-size: 0.9em;
        }
        .breadcrumb li {
            padding: 0 0.3em;
        }
        .breadcrumb li a {
            color: var(--color-primario-purpura);
            text-decoration: none;
        }
        .breadcrumb li a:hover {
            text-decoration: underline;
        }
        .breadcrumb li + li::before {
            content: ">"; /* Separador */
            padding-right: 0.5em;
            color: var(--color-texto-secundario);
        }
        .breadcrumb li.active {
            color: var(--color-texto-principal);
            font-weight: bold;
        }
        .breadcrumb-error {
            color: red;
            font-size: 0.8em;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="/index.html" class="logo-link">
                <img src="/imagenes/escudo.jpg" alt="Escudo del Condado de Castilla: castillo dorado sobre fondo púrpura con una estrella de 8 puntas dorada encima." class="logo-image">
            </a>
            <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>
            <ul class="nav-links">
                <li><a href="/index.html">Inicio</a></li>
                <li><a href="/historia/historia.html" class="active-link">Nuestra Historia</a></li>
                <li><a href="/lugares/lugares.html">Lugares Emblemáticos</a></li>
                <li><a href="/visitas/visitas.html">Planifica Tu Visita</a></li>
                <li><a href="/cultura/cultura.html">Cultura y Legado</a></li>
                <li><a href="/contacto/contacto.html">Contacto</a></li>
                <li><a href="https://www.facebook.com/groups/1052427398664069" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-square"></i> Comunidad</a></li>
            </ul>
        </div>
    </nav>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.6), rgba(var(--color-negro-contraste-rgb), 0.7)), url('/imagenes/paisaje_cerezo.jpg'); min-height: 40vh;">
        <div class="hero-content" style="padding: clamp(20px, 4vw, 40px);">
            <h1 style="font-size: clamp(2.2em, 5vw, 3.5em);"><?php echo htmlspecialchars($titulo_pagina_actual); ?></h1>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <div class="breadcrumb-container">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li><a href="<?php echo htmlspecialchars($breadcrumb_inicio_url); ?>"><?php echo htmlspecialchars($breadcrumb_inicio_texto); ?></a></li>
                            <li><a href="<?php echo htmlspecialchars($breadcrumb_historia_url); ?>"><?php echo htmlspecialchars($breadcrumb_historia_texto); ?></a></li>
                            <li><a href="<?php echo htmlspecialchars($breadcrumb_indice_url); ?>"><?php echo htmlspecialchars($breadcrumb_indice_texto); ?></a></li>
                            <li class="active" aria-current="page"><?php echo htmlspecialchars($breadcrumb_tema_actual_texto); ?></li>
                        </ol>
                    </nav>
                    <?php if (!empty($error_message_breadcrumb)): ?>
                        <p class="breadcrumb-error">Error en datos para breadcrumbs: <?php echo htmlspecialchars(trim($error_message_breadcrumb)); ?></p>
                    <?php endif; ?>
                </div>

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

    <footer class="footer">
        <div class="container">
            <p>© <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
            <p>Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/groups/1052427398664069" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Síguenos en Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="/en_construccion.html" aria-label="Instagram" title="Síguenos en Instagram"><i class="fab fa-instagram"></i></a>
                <a href="/en_construccion.html" aria-label="Twitter" title="Síguenos en Twitter"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

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
                }
            });
        });
    </script>

</body>
</html>
