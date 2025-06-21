# Estructura base del frontend

Este repositorio incluye un esqueleto para dos implementaciones
modernas: una con **React** y otra con **Svelte**. Ambas utilizan
**Vite** como empaquetador y comparten una organización paralela
de carpetas.

```
frontend/
├── react/
│   ├── package.json
│   ├── vite.config.ts
│   └── src/
│       ├── routes/
│       ├── views/
│       ├── components/
│       │   └── base/
│       └── services/
└── svelte/
    ├── package.json
    ├── vite.config.ts
    └── src/
        ├── routes/
        ├── views/
        ├── components/
        │   └── base/
        └── services/
```

- **`src/routes/`** contiene las rutas de página o las entradas de React
  Router y SvelteKit.
- **`src/views/`** alberga las vistas principales.
- **`src/components/base/`** reúne componentes reutilizables.
- **`src/services/`** implementa las llamadas a la API (por ejemplo, la
  existente en `flask_app.py`).

Cada subproyecto incluye un ejemplo mínimo de `package.json` y
`vite.config.ts` listo para ampliarse.

## Archivos iniciales

Se han añadido ejemplos básicos en cada carpeta:

- **React**: `index.html`, `src/main.tsx` y `src/routes/App.tsx` implementan un menú deslizante con la paleta de morado y oro viejo.
- **Svelte**: `index.html`, `src/main.ts` y `src/routes/App.svelte` reproducen la misma interfaz.
- Ambos proyectos comparten una hoja `style.css` donde se definen los colores de fondo alabastro y los textos degradados.

Estos archivos sirven de punto de partida y pueden eliminarse o ampliarse libremente.
