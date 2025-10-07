<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
ensure_session_started();
require_once __DIR__ . '/includes/components/aurora_components.php';

use function AuroraComponents\collect_knowledge;
use function AuroraComponents\gradient_heading;
use function AuroraComponents\markdown_excerpt;
use function AuroraComponents\mission_text;
use function AuroraComponents\render_story_cards;
use function AuroraComponents\render_tapestry;
use function AuroraComponents\timeline;

$knowledgeTree = [
    [
        'title' => 'Historia fundacional',
        'path' => 'docs/historia.md',
        'link' => '/historia/historia.php',
        'paragraphs' => 2,
    ],
    [
        'title' => 'Arqueología viva',
        'path' => 'docs/arqueologia.md',
        'link' => '/lugares/lugares.php',
        'paragraphs' => 2,
    ],
    [
        'title' => 'Tradición y cultura',
        'path' => 'docs/tradicion.md',
        'link' => '/cultura/cultura.php',
        'paragraphs' => 2,
    ],
    [
        'title' => 'Crónica ampliada',
        'path' => 'docs/historia_ampliada_nuevo4.md',
        'link' => '/historia/historia.php#cronica-ampliada',
        'paragraphs' => 1,
    ],
];

$knowledgeDeck = collect_knowledge($knowledgeTree);
$mission = mission_text();

$tapestryData = [
    [
        'heading' => 'Cerezo de Río Tirón, faro del norte',
        'body' => markdown_excerpt('docs/historia_ampliada_nuevo4.md', 1, 260),
    ],
    [
        'heading' => 'Ruta viva por el Condado',
        'body' => markdown_excerpt('docs/tradicion.md', 1, 240),
    ],
    [
        'heading' => 'Innovación y memoria',
        'body' => markdown_excerpt('docs/arqueologia.md', 1, 220),
    ],
];

$timelineItems = [
    [
        'label' => 'Siglo I',
        'description' => 'Nacimiento de Auca Patricia en la vía romana que articuló el valle del Tirón.',
    ],
    [
        'label' => 'Siglo VIII',
        'description' => 'Alzamiento del Alcázar de Cerasio como bastión del despertar castellano.',
    ],
    [
        'label' => 'Siglo XI',
        'description' => 'Consolidación del condado y expansión de rutas peregrinas hacia Santiago.',
    ],
    [
        'label' => 'Siglo XXI',
        'description' => 'Renacimiento digital para custodiar el patrimonio y activar el turismo regenerativo.',
    ],
];

$agentSeed = [];
$agentsPath = __DIR__ . '/config/forum_agents.json';
if (is_file($agentsPath)) {
    $decoded = json_decode((string) file_get_contents($agentsPath), true);
    if (is_array($decoded)) {
        $agentSeed = $decoded;
    }
}
$agentSeedJson = htmlspecialchars((string) json_encode($agentSeed, JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Condado de Castilla 2025 · Turismo, Patrimonio y Comunidad</title>
    <meta name="description" content="Descubre Cerezo de Río Tirón: turismo cultural, arqueología viva y comunidad participativa en el corazón de Castilla.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Lora:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css" integrity="sha256-n1OJ7Pc0I6/2DYUg9s1SZ0gWzLqcGfnlYBBYzYuyPRU=" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/aurora.css">
</head>
<body>
    <button class="menu-toggle left" data-menu="left" aria-label="Abrir mapa de exploración">
        <i class="fas fa-compass"></i>
    </button>
    <button class="menu-toggle right" data-menu="right" aria-label="Abrir panel comunitario">
        <i class="fas fa-people-group"></i>
    </button>

    <aside id="menu-left" class="side-menu left" aria-hidden="true">
        <header>
            <h3>Mapa cultural</h3>
            <p>Selecciona un eje para empezar tu travesía.</p>
        </header>
        <nav aria-label="Navegación principal">
            <ul>
                <li><a href="/historia/historia.php">Historia fundacional</a></li>
                <li><a href="/lugares/lugares.php">Yacimientos y rutas</a></li>
                <li><a href="/cultura/cultura.php">Cultura viva</a></li>
                <li><a href="/visitas/visitas.php">Planifica tu visita</a></li>
                <li><a href="/foro/index.php">Foro participativo</a></li>
            </ul>
        </nav>
        <div class="menu-footer">Inspirado por el legado de Castilla y el pulso del Tirón.</div>
    </aside>

    <aside id="menu-right" class="side-menu right" aria-hidden="true">
        <header>
            <h3>Comunidad viva</h3>
            <p>Sigue las decisiones estratégicas de nuestros agentes expertos.</p>
        </header>
        <nav aria-label="Panel comunitario">
            <ul>
                <li><a href="/foro/index.php#agentes">Consejo de expertos</a></li>
                <li><a href="/dashboard/index.php">Inteligencia turística</a></li>
                <li><a href="/museo/index.php">Museo interactivo</a></li>
                <li><a href="/tienda/index.php">Artesanía y producto local</a></li>
                <li><a href="/blog.php">Crónicas del Condado</a></li>
            </ul>
        </nav>
        <div class="menu-footer">Participa, propone y co-crea el futuro de Cerezo de Río Tirón.</div>
    </aside>

    <div class="site-shell" data-agent-seed="<?= $agentSeedJson; ?>" data-mission-endpoint="/api/mission" data-agents-endpoint="/api/forum/agents" data-comments-endpoint="/api/forum/comments">
        <header class="hero" role="banner">
            <div class="hero-inner">
                <div class="hero-badge">
                    <span class="gradient-display">Cerezo de Río Tirón</span>
                </div>
                <p class="hero-lead">
                    <?= $mission; ?>
                </p>
                <div class="hero-cta">
                    <a class="button-aurora" href="/visitas/visitas.php"><i class="fas fa-route"></i> Planifica tu travesía</a>
                    <a class="button-aurora" href="/foro/index.php"><i class="fas fa-comments"></i> Únete al foro</a>
                    <a class="button-aurora" href="/dashboard/index.php"><i class="fas fa-chart-line"></i> Inteligencia territorial</a>
                </div>
            </div>
        </header>

        <main>
            <section class="section-shell" id="atlas-conocimiento">
                <?= gradient_heading('Atlas del conocimiento del Condado'); ?>
                <?= render_story_cards($knowledgeDeck); ?>
            </section>

            <section class="section-shell" id="relato-vivo">
                <?= gradient_heading('Relato vivo de Castilla'); ?>
                <?= render_tapestry($tapestryData); ?>
            </section>

            <section class="section-shell" id="agenda-evolutiva">
                <?= gradient_heading('Agenda evolutiva'); ?>
                <p>Un itinerario que conecta pasado, presente y futuro con decisiones medibles.</p>
                <?= timeline($timelineItems); ?>
                <div class="cta-evolution">
                    <a class="button-aurora" href="/dashboard/index.php#indicadores">Ver indicadores estratégicos</a>
                </div>
            </section>

            <section class="section-shell community-panel" id="consejo-expertos">
                <?= gradient_heading('Consejo de 5 expertos guardianes'); ?>
                <p>Consulta las perspectivas de nuestro foro permanente y aporta tus ideas.</p>
                <div class="agent-grid" data-role="agents-grid">
                    <!-- Renderizado inicial desde PHP -->
                    <?php foreach ($agentSeed as $key => $agent): ?>
                        <article class="agent-card" data-agent="<?= htmlspecialchars((string) $key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                            <span class="agent-badge"><i class="<?= htmlspecialchars($agent['role_icon'] ?? 'fas fa-star', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"></i> <?= htmlspecialchars($agent['name'] ?? 'Agente', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></span>
                            <h4><?= htmlspecialchars($agent['expertise'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h4>
                            <p><?= htmlspecialchars($agent['bio'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
                            <?php if (!empty($agent['vision'])): ?>
                                <p><em><?= htmlspecialchars($agent['vision'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></em></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section-shell reactive-layout" id="explorador-activo">
                <?= gradient_heading('Explorador activo'); ?>
                <div class="flex-duo">
                    <div class="transparent-card">
                        <h3>Planificación y visitas</h3>
                        <p>Organiza itinerarios, consulta horarios y reserva experiencias tematizadas.</p>
                        <div class="link-grid">
                            <a href="/visitas/visitas.php"><i class="fas fa-calendar-check"></i> Calendario de visitas</a>
                            <a href="/lugares/lugares.php"><i class="fas fa-landmark"></i> Circuito arqueológico</a>
                            <a href="/camino_santiago/index.php"><i class="fas fa-shoe-prints"></i> Camino de Santiago en Cerezo</a>
                        </div>
                    </div>
                    <div class="transparent-card">
                        <h3>Participa y difunde</h3>
                        <p>Súmate a campañas de voluntariado, comparte crónicas y apoya el archivo vivo.</p>
                        <div class="link-grid">
                            <a href="/foro/index.php"><i class="fas fa-comments"></i> Foro y debates</a>
                            <a href="/museo/index.php"><i class="fas fa-vr-cardboard"></i> Museo inmersivo</a>
                            <a href="/tienda/index.php"><i class="fas fa-store"></i> Artesanía y km 0</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?= date('Y'); ?> Condado de Castilla · Comunidad viva de Cerezo de Río Tirón.</p>
            <p><a href="/docs/">Centro de documentación</a> · <a href="/contacto/index.php">Contacto</a> · <a href="/sitemap.xml">Mapa del sitio</a></p>
        </footer>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" integrity="sha256-uX2sEvhczHxVn9Yx8RJWb1x1o1t4bm/FYnGV8eK3op0=" crossorigin="anonymous"></script>
    <script type="module" src="/js/aurora.js"></script>
</body>
</html>
