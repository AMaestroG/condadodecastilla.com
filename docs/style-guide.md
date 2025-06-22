# Guía de Estilo

Esta guía resume la paleta de colores usada de forma consistente en todo el proyecto. Las variables CSS se definen en `assets/css/epic_theme.css` y pueden reutilizarse en cualquier página.

| Variable                    | Descripción                                   | Valor     |
| --------------------------- | --------------------------------------------- | --------- |
| `--color-primario-purpura`  | Morado principal inspirado en el escudo       | `#4A0D67` |
| `--color-secundario-dorado` | Oro viejo para detalles y bordes              | `#B8860B` |
| `--color-acento-amarillo`   | Acento amarillo brillante                     | `#FFD700` |
| `--color-piedra-clara`      | Beige claro para texto y fondos ligeros       | `#EAE0C8` |
| `--color-piedra-media`      | Arenisca media para fondos secundarios        | `#D2B48C` |
| `--color-texto-principal`   | Marrón oscuro para la tipografía              | `#2c1d12` |
| `--color-fondo-pagina`      | Blanco hueso sutil de fondo                   | `#fdfaf6` |
| `--epic-alabaster-bg`       | Fondo principal de alabastro                  | `#fdfaf6` |
| `--epic-alabaster-dawn`     | Variante gris suave para el amanecer          | `#e5e5e5` |
| `--epic-gold-soft`          | Oro pálido de las primeras horas              | `#f3e5ab` |
| `--epic-purple-vibrant`     | Morado intenso para el día                    | `#6b46c1` |
| `--epic-alabaster-dusk`     | Fondo cálido del atardecer                    | `#efe2d2` |
| `--epic-gold-dusk`          | Dorado profundo del atardecer                 | `#f0b429` |
| `--epic-orange-sunset`      | Naranja brillante del ocaso                   | `#f97316` |
| `--epic-text-night`         | Texto claro para el modo nocturno             | `#e0e0e0` |
| `--color-negro-contraste`   | Negro de alto contraste                       | `#1A1A1A` |
| `--alert-bg`                | Fondo de mensajes de alerta                   | `#ffdddd` |
| `--alert-border`            | Borde de mensajes de alerta                   | `#ff0000` |
| `--alert-text`              | Texto de mensajes de alerta                   | `#d8000c` |
| `--menu-extra-offset`       | Separación adicional para el menú consolidado | `60px`    |

## Tipografías

Se definen dos familias de fuentes base disponibles a través de Tailwind. Las
fuentes se gestionan mediante variables CSS:

| Clase            | Fuente                 |
| ---------------- | ---------------------- |
| `.font-headings` | `var(--font-headings)` |
| `.font-body`     | `var(--font-primary)`  |

Estas utilidades permiten asignar de forma coherente la tipografía a títulos y
textos de párrafo. Las clases se generan desde `tailwind.config.js`, donde se
declaran las familias `headings` y `body` basadas en estas variables:

```html
<h1 class="font-headings">Bienvenida</h1>
<p class="font-body">Texto principal de ejemplo.</p>
```

Los equivalentes `-rgb` se encuentran en la misma hoja de estilos para crear transparencias con `rgba()`.

## Paleta modo oscuro

El tema adapta automáticamente la interfaz cuando el navegador está en modo oscuro (`prefers-color-scheme: dark`).
En ese caso se redefinen varios colores principales para mantener el contraste.

| Variable                  | Descripción                  | Valor     |
| ------------------------- | ---------------------------- | --------- |
| `--epic-alabaster-bg`     | Fondo principal oscuro       | `#252B38` |
| `--epic-alabaster-medium` | Tono intermedio para paneles | `#2a2f39` |
| `--epic-text-color`       | Texto principal claro        | `#F0F0F0` |
| `--epic-text-light`       | Texto secundario             | `#D8D8D8` |

## Texto con degradado

Para destacar títulos y cabeceras se utiliza la clase `.gradient-text`,
definida en `assets/css/epic_theme.css`. Esta clase aplica un degradado
diagonal que combina el morado principal (`--epic-purple-emperor`) y el
oro viejo (`--epic-gold-main`) y aprovecha `background-clip: text` para
dejar a la vista los colores de la paleta.

Para dispositivos móviles existe una hoja específica,
`mobile_contrast.css`, que desactiva estos degradados y define
colores sólidos de alto contraste. Así se garantiza que los encabezados
se lean con claridad incluso en pantallas pequeñas.

```html
<h1 class="gradient-text">Descubre Cerezo</h1>
```

Si el navegador no soporta `background-clip: text` se aplica
automáticamente un color sólido mediante una regla `@supports`.
También puedes añadir la clase `.no-clip-text` al contenedor para
forzar esta versión.

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

Los navegadores que no admiten `mix-blend-mode` muestran estos
elementos en un color sólido. El CSS incluye una regla `@supports`
para gestionar este caso y una clase opcional `.no-blend` que puede
añadirse manualmente.

## Optimización de imágenes

Para agilizar la carga se recomienda emplear formatos modernos y limitar las dimensiones de las fotografías.

- Formato **WebP** para fotos y capturas; **PNG** solo cuando se necesite transparencia.
- Anchura máxima de **1920&nbsp;px** en imágenes de cabecera y **800&nbsp;px** para el resto.
- El peso ideal está por debajo de **300&nbsp;KB**. Cualquier imagen que supere **1&nbsp;MB** debería revisarse.
- Para generar versiones optimizadas o miniaturas se dispone de un script descrito en [script_catalog.md](script_catalog.md).

Actualmente existen archivos superiores a 2&nbsp;MB en `assets/img/` (por ejemplo `GonzaloTellez.png`), conviene reconvertirlos a WebP o aplicar compresión.

## Accesibilidad

Para resaltar los estados activos se emplean los atributos `aria-expanded` y `aria-hidden`.
Cuando un botón está expandido (`aria-expanded="true"`) o un panel visible
(`aria-hidden="false"`), se aplican colores variables para mayor contraste.

```css
#consolidated-menu-button[aria-expanded="true"] {
  background-color: var(--epic-gold-main);
  color: var(--epic-purple-emperor);
}
.menu-panel[aria-hidden="false"] {
  border: 2px solid var(--epic-gold-main);
}
```

## Desplazamiento Suave

Todas las páginas emplean la regla `scroll-behavior: smooth;` definida en
`assets/css/epic_theme.css`. Basta con incluir una lista de enlaces internos,
como el índice generado por `toc-generator.js`, para que los saltos entre
secciones realicen una animación de desplazamiento fluida.

## Menú móvil

Los paneles de menú deslizante utilizan la clase `.open` para mostrarse y
desaparecer al quitársela. Al activarse, `assets/js/main.js` añade la clase
`menu-compressed` al elemento `<body>` junto con `menu-open-left` o
`menu-open-right` según el lateral. Esta combinación desplaza la página y aplica
detalles en morado principal (`--color-primario-purpura`) con bordes en oro viejo
(`--color-secundario-dorado`) para resaltar el estado activo.
