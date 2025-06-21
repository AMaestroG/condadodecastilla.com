<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <?php require_once __DIR__.'/includes/head_common.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Condado de Castilla - Página Principal</title>
    <link rel="stylesheet" href="/assets/vendor/css/tailwind.min.css">
    <link rel="stylesheet" href="/assets/css/tailwind_custom.css">
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body class="font-body bg-[color:var(--epic-alabaster-bg)] text-gray-900">
    <header class="fixed w-full bg-imperial-purple bg-opacity-90 text-old-gold shadow z-20">
        <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
            <h1 class="font-headings text-xl md:text-2xl gradient-text">Condado de Castilla</h1>
            <button id="menu-toggle" aria-label="Abrir menú" class="text-old-gold md:hidden focus:outline-none">☰</button>
            <nav class="hidden md:flex space-x-4 font-body" aria-label="Navegación principal">
                <a href="#hero" class="hover:underline font-body">Inicio</a>
                <a href="#timeline" class="hover:underline font-body">Historia</a>
                <a href="#arqueologia" class="hover:underline font-body">Arqueología</a>
                <a href="#foro" class="hover:underline font-body">Foro</a>
            </nav>
        </div>
        <div id="mobile-menu" class="slide-menu left texture-alabaster md:hidden" aria-label="Menú móvil">
            <ul class="py-4 font-body">
                <li><a href="#hero" class="block px-4 py-2">Inicio</a></li>
                <li><a href="#timeline" class="block px-4 py-2">Historia</a></li>
                <li><a href="#arqueologia" class="block px-4 py-2">Arqueología</a></li>
                <li><a href="#foro" class="block px-4 py-2">Foro</a></li>
            </ul>
        </div>
    </header>

    <main class="pt-20 space-y-20">
        <section id="hero" class="relative text-center text-old-gold texture-alabaster bg-imperial-purple bg-opacity-75 py-20">
            <h2 class="font-headings text-4xl sm:text-5xl lg:text-6xl mb-4 gradient-text">Cuna de la Cultura Hispana</h2>
            <p class="text-lg sm:text-xl max-w-prose mx-auto font-body">Promocionamos el turismo en Cerezo de Río Tirón y gestionamos su patrimonio arqueológico y cultural.</p>
        </section>

        <section id="timeline" class="max-w-5xl mx-auto px-4">
            <h2 class="font-headings text-3xl text-imperial-purple mb-6">Línea de Tiempo Histórica</h2>
            <div class="grid sm:grid-cols-2 gap-6">
                <article class="p-4 bg-white/70 border-l-4 border-old-gold">
                    <h3 class="font-headings text-2xl">Antigüedad</h3>
                    <p class="font-body">Espacio para describir los orígenes y primeros asentamientos.</p>
                </article>
                <article class="p-4 bg-white/70 border-l-4 border-old-gold">
                    <h3 class="font-headings text-2xl">Edad Media</h3>
                    <p class="font-body">Breve introducción a la formación del Condado de Castilla.</p>
                </article>
            </div>
        </section>

        <section id="arqueologia" class="texture-alabaster bg-old-gold bg-opacity-80 py-16">
            <div class="max-w-4xl mx-auto px-4 text-imperial-purple">
                <h2 class="font-headings text-3xl mb-4">Exploración Arqueológica</h2>
                <p class="mb-6 font-body">Zona preparada para mostrar hallazgos y excavaciones de Cerezo de Río Tirón.</p>
                <img loading="lazy" src="/assets/img/Muralla.jpg" alt="Muralla histórica de Cerezo de Río Tirón" class="w-full h-auto rounded shadow-md" />
            </div>
        </section>

        <section id="foro" class="max-w-4xl mx-auto px-4">
            <h2 class="font-headings text-3xl text-imperial-purple mb-4">Foro de Expertos</h2>
            <p class="mb-8 font-body">Cinco especialistas comparten su conocimiento para impulsar la comunidad.</p>
            <ul class="grid md:grid-cols-5 gap-4 text-center font-body">
                <li>Alicia la Historiadora</li>
                <li>Bruno el Arqueólogo</li>
                <li>Clara la Guía Turística</li>
                <li>Diego el Gestor Cultural</li>
                <li>Elena la Tecnóloga</li>
            </ul>
        </section>
    </main>

    <footer class="bg-imperial-purple text-old-gold text-center py-4 texture-alabaster">
        <p class="font-headings">© 2024 Condado de Castilla</p>
        <p class="font-body">Difundiendo el legado de Castilla y Cerezo de Río Tirón.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('menu-toggle');
            const menu = document.getElementById('mobile-menu');
            if (!toggle || !menu) return;
            toggle.addEventListener('click', function () {
                const open = menu.classList.toggle('open');
                document.body.classList.toggle('menu-open-left', open);
            });
        });
    </script>

</body>
</html>
