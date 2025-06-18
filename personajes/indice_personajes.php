<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Índice de Personajes Históricos - Condado de Castilla</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">
    <link rel="stylesheet" href="/assets/css/header.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <link rel="stylesheet" href="/assets/css/galeria_personajes.css">
    <link rel="stylesheet" href="/assets/vendor/css/bootstrap.min.css">
</head>
<body>

    <?php include __DIR__ . '/../_header.php'; ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.75), rgba(var(--color-negro-contraste-rgb), 0.88)), url('/assets/img/hero_personajes_background.jpg');">
        <div class="hero-content">
            <img src="/assets/img/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Índice de Personajes Históricos y Legendarios</h1>
            <p>Un compendio de las figuras que forjaron la historia y el mito de nuestras tierras.</p>
        </div>
    </header>

    <main>
        <section class="section detailed-intro-section"> <div class="container page-content-block"> <p class="intro-paragraph">
                    Explora las biografías y el legado de las figuras más importantes que marcaron la historia de Cerezo de Río Tirón, el Alfoz de Cerasio y Lantarón, y el Condado de Castilla.
                </p>
                
                <div id="error-message-container" style="color: red; text-align: center;"></div>
                <ul class="indice-categorias">
                    <li>
                        <h3><i class="fas fa-chess-king"></i> Condes de Castilla, Álava y Lantarón</h3>
                        <ul class="indice-personajes-lista" id="condes-lista">
                            <li><a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/rodrigo_el_conde.php">Rodrigo (Rodrigo el Conde)</a></li>
                            <li><a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/diego_rodriguez_porcelos.php">Diego Rodríguez Porcelos</a></li>
                            <li><a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/fernando_diaz.php">Fernando Díaz</a></li>
                            <li><a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/gonzalo_tellez.php">Gonzalo Téllez</a></li>
                            <li><a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/alvaro_herramelliz.php">Álvaro Herramelliz</a></li>
                            <li><a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/dona_sancha.php">Doña Sancha</a></li>
                            <li><a href="/personajes/Condes_de_Castilla_Alava_y_Lantaron/fernan_gonzalez.php">Fernán González</a></li>
                        </ul>
                    </li>
                    <li>
                        <h3><i class="fas fa-shield-alt"></i> Militares y Gobernantes</h3>
                        <ul class="indice-personajes-lista" id="militares-lista">
                            <li><a href="/personajes/Militares_y_Gobernantes/conde_casio_cerasio.php">Conde Casio (Cerasio)</a></li>
                            <li><a href="/personajes/Militares_y_Gobernantes/ramiro_i_de_asturias.php">Ramiro I de Asturias</a></li>
                            <li><a href="/personajes/Militares_y_Gobernantes/agripa.php">Agripa</a></li>
                            <li><a href="/personajes/Militares_y_Gobernantes/cesar_augusto.php">César Augusto</a></li>
                            <li><a href="/personajes/Militares_y_Gobernantes/corocotta.php">Corocotta</a></li>
                            <li><a href="/personajes/Militares_y_Gobernantes/alba_esposa_corocotta.php">Alba (esposa de Corocotta)</a></li>
                            <li><a href="/personajes/Militares_y_Gobernantes/turok_nerea_amaia_hijos_corocotta.php">Turok, Nerea y Amaia (hijos de Corocotta)</a></li>
                        </ul>
                    </li>
                    <li>
                        <h3><i class="fas fa-cross"></i> Santos y Mártires</h3>
                        <ul class="indice-personajes-lista" id="santos-lista">
                            <li><a href="/personajes/Santos_y_Martires/abades_sonna_donnino_damian.php">Abades Sonna, Donnino y Damián</a></li>
                            <li><a href="/personajes/Santos_y_Martires/obispos_gudesteos_frunimio.php">Obispos Gudesteos y Frunimio</a></li>
                            <li><a href="/personajes/Santos_y_Martires/obispo_indalecio.php">Obispo Indalecio</a></li>
                            <li><a href="/personajes/Santos_y_Martires/obispo_sancho.php">Obispo Sancho</a></li>
                            <li><a href="/personajes/Santos_y_Martires/san_braulio.php">San Braulio</a></li>
                            <li><a href="/personajes/Santos_y_Martires/san_formerio.php">San Formerio</a></li>
                            <li><a href="/personajes/Santos_y_Martires/san_millan_de_la_cogolla.php">San Millán de la Cogolla</a></li>
                            <li><a href="/personajes/Santos_y_Martires/san_vitores.php">San Vitores</a></li>
                        </ul>
                    </li>
                    <li>
                        <h3><i class="fas fa-landmark"></i> Emperadores Romanos de Cuna Hispana</h3>
                        <ul class="indice-personajes-lista" id="emperadores-lista">
                            <li><a href="/personajes/Emperadores_Romanos_Hispanos_Auca/aureliano.php">Aureliano</a></li>
                            <li><a href="/personajes/Emperadores_Romanos_Hispanos_Auca/constantino.php">Constantino</a></li>
                            <li><a href="/personajes/Emperadores_Romanos_Hispanos_Auca/flavio_arcadio.php">Flavio Arcadio</a></li>
                            <li><a href="/personajes/Emperadores_Romanos_Hispanos_Auca/flavio_teodosio_i_el_grande.php">Flavio Teodosio I el Grande</a></li>
                            <li><a href="/personajes/Emperadores_Romanos_Hispanos_Auca/flavio_victor.php">Flavio Victor</a></li>
                            <li><a href="/personajes/Emperadores_Romanos_Hispanos_Auca/magno_clemente_maximo.php">Magno Clemente Máximo</a></li>
                        </ul>
                    </li>
                    <li>
                        <h3><i class="fas fa-hands-helping"></i> Órdenes y Legados</h3>
                        <ul class="indice-personajes-lista" id="ordenes-lista">
                            <li><a href="/personajes/Ordenes_y_Legados/monjes_hospitalarios_san_jorge_y_san_anton.php">Monjes Hospitalarios de San Jorge y San Antón</a></li>
                            <li><a href="/personajes/Ordenes_y_Legados/paterna_banucasi.php">Paterna (Banucasi)</a></li>
                        </ul>
                    </li>
                    <li>
                        <h3><i class="fas fa-crown"></i> Reyes y Reinas</h3>
                        <ul class="indice-personajes-lista" id="reyes-lista">
                            <li><a href="/personajes/Militares_y_Gobernantes/alfonso_ii_el_casto.php">Alfonso II el Casto</a></li>
                            <li><a href="/personajes/Militares_y_Gobernantes/leovigildo.php">Leovigildo</a></li>
                        </ul>
                    </li>
                    <li>
                        <h3><i class="fas fa-pen-nib"></i> Escritores y Cronistas</h3>
                        <ul class="indice-personajes-lista" id="escritores-lista">
                            <li><a href="/personajes/Ordenes_y_Legados/fray_prudencio_de_sandoval.php">Fray Prudencio de Sandoval</a></li>
                        </ul>
                    </li>
                    <li>
                        <h3><i class="fas fa-book-open"></i> Leyendas y Cuentos</h3>
                        <ul class="indice-personajes-lista" id="leyendas-lista">
                            <li><a href="/personajes/Leyendas_y_Cuentos/beatriz.php">Beatriz de las Aguas Misteriosas</a></li>
                        </ul>
                    </li>
                </ul>

                <section class="character-gallery-container">
                    <h2>Galería de Personajes</h2>
                    <div class="gallery-grid" id="galeria-personajes-grid">
                        <div class="gallery-item">
                            <img src="/assets/img/placeholder_personaje.png" alt="Rodrigo el Conde">
                            <p>Rodrigo el Conde</p>
                        </div>
                        <div class="gallery-item">
                            <img src="/assets/img/placeholder_personaje.png" alt="Diego Rodríguez Porcelos">
                            <p>Diego Rodríguez Porcelos</p>
                        </div>
                        <div class="gallery-item">
                            <img src="/assets/img/placeholder_personaje.png" alt="Fernán González">
                            <p>Fernán González</p>
                        </div>
                        <div class="gallery-item">
                            <img src="/assets/img/placeholder_personaje.png" alt="Conde Casio">
                            <p>Conde Casio</p>
                        </div>
                        <div class="gallery-item">
                            <img src="/assets/img/placeholder_personaje.png" alt="San Formerio">
                            <p>San Formerio</p>
                        </div>
                        <div class="gallery-item">
                            <img src="/assets/img/placeholder_personaje.png" alt="Flavio Teodosio I el Grande">
                            <p>Flavio Teodosio I el Grande</p>
                        </div>
                    </div>
                </section>

                 <p class="text-center" style="margin-top: 3em;">
                    <a href="/index.php" class="cta-button cta-button-small">Volver a la Página Principal</a>
                </p>
            </div>
        </section>
    </main>

    <div id="footer-placeholder"></div>


    <script src="/js/layout.js"></script>
    <script src="/js/personajes_loader.js"></script>
</body>
</html>
