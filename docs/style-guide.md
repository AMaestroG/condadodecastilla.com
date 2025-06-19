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
| `--color-negro-contraste` | Negro de alto contraste | `#1A1A1A` |
| `--alert-bg` | Fondo de mensajes de alerta | `#ffdddd` |
| `--alert-border` | Borde de mensajes de alerta | `#ff0000` |
| `--alert-text` | Texto de mensajes de alerta | `#d8000c` |
| `--menu-extra-offset` | Separación adicional para el menú consolidado | `60px` |

Los equivalentes `-rgb` se encuentran en la misma hoja de estilos para crear transparencias con `rgba()`.

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
