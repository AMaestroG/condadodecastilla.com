# Gráfica de Influencia Romana

Esta gráfica interactiva ilustra la evolución de la huella romana en Cerezo de Río Tirón. Forma parte de nuestra misión de **promocionar el turismo** y **gestionar el patrimonio** según se detalla en [nuevo4.md](../nuevo4.md).

## Propósito de la visualización

La visualización se implementa con **D3.js** y emplea la paleta de morado y oro viejo de la [guía de estilo](style-guide.md). Muestra un recorrido temporal, resaltando en cada punto hechos clave de la romanización. La línea principal utiliza un degradado de `--epic-purple-emperor` a `--epic-gold-main` y un resplandor sutil para enfatizar los momentos de mayor influencia.

## Compatibilidad con modo oscuro

El código CSS incluye una regla `@media (prefers-color-scheme: dark)` que adapta el color de la gráfica y de las etiquetas flotantes (tooltips) cuando el visitante activa el **modo luna**. De este modo los ejes, las leyendas y el cuadro informativo utilizan `var(--epic-text-light)` y otros colores de la paleta nocturna para seguir siendo legibles sobre el fondo de alabastro oscuro.

## Fragmento de código

La siguiente porción de `historia/influencia_romana.php` muestra dónde se incrusta el contenedor de la gráfica y cómo se cargan los scripts necesarios:

```php
<section class="section alternate-bg">
    <div class="container-epic">
        <h2 class="section-title gradient-text">Evolución Histórica</h2>
        <div id="roman-chart"></div>
    </div>
</section>
<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="/assets/js/influencia_romana.js"></script>
```

Dentro del `<head>` de la misma página se define la adaptación al modo oscuro:

```html
<style>
  #roman-chart {
    position: relative;
    width: 100%;
    max-width: 700px;
    margin: 40px auto;
  }
  .tooltip {
    background: rgba(255, 255, 255, 0.9);
    color: var(--color-negro-contraste);
  }
  @media (prefers-color-scheme: dark) {
    .tooltip {
      background: rgba(0, 0, 0, 0.85);
      color: var(--epic-text-light);
    }
    #roman-chart .axis path,
    #roman-chart .axis line {
      stroke: var(--epic-text-light);
    }
  }
</style>
```

Este fragmento demuestra cómo la visualización se integra en la página siguiendo el diseño con colores morados, oro viejo y fondo de alabastro.
