<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <link rel="stylesheet" href="/assets/css/pages/historia.css">
    <title>Influencia Romana</title>
    <style>
        #roman-chart { position: relative; width: 100%; max-width: 700px; margin: 40px auto; }
        .tooltip { position: absolute; background: rgba(255,255,255,0.9); color: var(--color-negro-contraste); padding: 4px 8px; border-radius: var(--global-border-radius); pointer-events: none; opacity: 0; transition: opacity 0.2s ease-in-out; }
        @media (prefers-color-scheme: dark) {
            .tooltip { background: rgba(0,0,0,0.85); color: var(--epic-text-light); }
            #roman-chart .axis path,
            #roman-chart .axis line { stroke: var(--epic-text-light); }
        }
    </style>
</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . '/../fragments/header.php'; ?>
    <header class="page-header hero bg-[url('/assets/img/hero_historia_background.jpg')] bg-cover bg-center md:bg-center">
        <div class="hero-content">
            <h1 class="gradient-text">Influencia Romana en la Región</h1>
            <p>Una mirada a la huella romana desde la Antigüedad hasta hoy.</p>
        </div>
    </header>
    <main>
        <section class="section alternate-bg">
            <div class="container-epic">
                <h2 class="section-title gradient-text">Evolución Histórica</h2>
                <div id="roman-chart"></div>
            </div>
        </section>
    </main>
    <?php require_once __DIR__ . '/../fragments/footer.php'; ?>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="/assets/js/influencia_romana.js"></script>
    
</body>
</html>
