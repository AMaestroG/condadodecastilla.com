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
