# Guía de Estilo

Esta guía resume la paleta de colores usada de forma consistente en todo el proyecto. Las variables CSS se definen en `assets/css/epic_theme.css` y pueden reutilizarse en cualquier página.

| Variable | Descripción | Valor |
|----------|-------------|-------|
| `--color-primario-purpura` | Morado principal inspirado en el escudo | `#4A0D67` |
| `--color-secundario-dorado` | Oro viejo para detalles y bordes | `#B8860B` |
| `--color-acento-amarillo` | Acento amarillo brillante | `#FFD700` |
| `--color-piedra-clara` | Beige claro para texto y fondos ligeros | `#EAE0C8` |
| `--color-piedra-media` | Arenisca media para fondos secundarios | `#D2B48C` |
| `--color-texto-principal` | Marrón oscuro para la tipografía | `#2c1d12` |
| `--color-fondo-pagina` | Blanco hueso sutil de fondo | `#fdfaf6` |
| `--epic-alabaster-bg` | Fondo principal de alabastro | `#fdfaf6` |
| `--epic-alabaster-dawn` | Variante gris suave para el amanecer | `#e5e5e5` |
| `--epic-gold-soft` | Oro pálido de las primeras horas | `#f3e5ab` |
| `--epic-purple-vibrant` | Morado intenso para el día | `#6b46c1` |
| `--epic-alabaster-dusk` | Fondo cálido del atardecer | `#efe2d2` |
| `--epic-gold-dusk` | Dorado profundo del atardecer | `#f0b429` |
| `--epic-orange-sunset` | Naranja brillante del ocaso | `#f97316` |
| `--epic-text-night` | Texto claro para el modo nocturno | `#e0e0e0` |
| `--color-negro-contraste` | Negro de alto contraste | `#1A1A1A` |
| `--alert-bg` | Fondo de mensajes de alerta | `#ffdddd` |
| `--alert-border` | Borde de mensajes de alerta | `#ff0000` |
| `--alert-text` | Texto de mensajes de alerta | `#d8000c` |
| `--menu-extra-offset` | Separación adicional para el menú consolidado | `60px` |

## Tipografías

Se definen dos familias de fuentes base disponibles a través de Tailwind. Las
fuentes se gestionan mediante variables CSS:

| Clase | Fuente |
|-------|-------|
| `.font-headings` | `var(--font-headings)` |
| `.font-body` | `var(--font-primary)` |

Estas utilidades permiten asignar de forma coherente la tipografía a títulos y
textos de párrafo. Las clases se generan desde `tailwind.config.js`, donde se
declaran las familias `headings` y `body` basadas en estas variables:

```html
<h1 class="font-headings">Bienvenida</h1>
<p class="font-body">Texto principal de ejemplo.</p>
```

Los equivalentes `-rgb` se encuentran en la misma hoja de estilos para crear transparencias con `rgba()`.

## Paleta Modo luna

El modo luna ofrece un aspecto nocturno con gran contraste. Se activa añadiendo la clase
`luna` al elemento `<body>` y define las siguientes variables:

| Variable | Descripción | Valor |
|----------|-------------|-------|
| `--moon-bg` | Fondo principal oscuro | `#0d1117` |
| `--moon-text` | Texto claro de alto contraste | `#e0e0e0` |
| `--moon-accent` | Detalle en púrpura intenso | `#663399` |

## Texto con degradado

Para destacar títulos y cabeceras se utiliza la clase `.gradient-text`,
definida en `assets/css/epic_theme.css`. Esta clase aplica un degradado
diagonal que combina el morado principal (`--epic-purple-emperor`) y el
oro viejo (`--epic-gold-main`) y aprovecha `background-clip: text` para
dejar a la vista los colores de la paleta.

## Botones de llamada a la acción

Los botones de tipo CTA están definidos en
`assets/css/epic_theme.css` y utilizan color de fondo morado con un
ligero sombreado interior. La regla `.cta-button` establece la base del
estilo en las líneas 599‑623 mientras que la variante
`.cta-button--large-legacy` (líneas 1080‑1102) aplica un degradado
dorado y un efecto de desplazamiento al pasar el ratón.

```html
<a href="#" class="cta-button cta-button--large-legacy">Descubre más</a>
```

En el estado _hover_ el botón gana contraste y su sombra interna se
acentúa, proporcionando un aspecto de relieve y realce.

## Mezcla de color en textos

Para integrar titulares sobre fondos complejos se disponen las clases `.blend-screen` y `.blend-overlay` en `assets/css/epic_theme.css`. Ambas usan la propiedad `mix-blend-mode` para fusionar el color del texto con el fondo.

```html
<h1 class="blend-overlay">Título</h1>
<p class="blend-screen">Texto destacado</p>
```

## Optimización de imágenes

Para agilizar la carga se recomienda emplear formatos modernos y limitar las dimensiones de las fotografías.

- Formato **WebP** para fotos y capturas; **PNG** solo cuando se necesite transparencia.
- Anchura máxima de **1920&nbsp;px** en imágenes de cabecera y **800&nbsp;px** para el resto.
- El peso ideal está por debajo de **300&nbsp;KB**. Cualquier imagen que supere **1&nbsp;MB** debería revisarse.
- Utiliza el script [`scripts/compress_images.sh`](../scripts/compress_images.sh) para generar versiones optimizadas o miniaturas.

Actualmente existen archivos superiores a 2&nbsp;MB en `assets/img/` (por ejemplo `GonzaloTellez.png`), conviene reconvertirlos a WebP o aplicar compresión.
