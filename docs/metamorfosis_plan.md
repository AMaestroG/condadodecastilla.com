# Plan de Metamorfosis 2025

## 1. Diagnóstico de la plataforma actual
- Arquitectura híbrida con PHP tradicional, fragmentos heredados y un backend Flask ya operativo pero infrautilizado en la portada.
- Estilos fragmentados entre múltiples hojas CSS, con superposiciones y reglas redundantes que dificultan la evolución visual.
- Experiencia de inicio centrada en contenido estático; la interacción comunitaria y los datos dinámicos del foro no se muestran de forma protagonista.
- Navegación basada en menús fijos, sin mecanismos proactivos que incentiven la exploración ni la participación continua.
- Jerarquía de carpetas extensa, sin componentes reutilizables para la portada ni un plan que articule la misión (turismo, patrimonio y comunidad).

## 2. Objetivos estratégicos
1. **Reimaginar la home** como un hub dinámico autoevolutivo que combine turismo, arqueología y comunidad.
2. **Unificar la identidad visual** con una paleta morado–oro viejo, fondos alabastro y tipografía con degradados de alto contraste.
3. **Activar la capa comunitaria** integrando los agentes expertos y el foro mediante el backend Flask.
4. **Simplificar el código** creando componentes PHP reutilizables y una hoja de estilo única, fácil de extender.
5. **Garantizar escalabilidad** preparando la estructura para que nuevas secciones se integren como "librerías" independientes.

## 3. Estrategia de rediseño
- **Frontend**: reemplazar completamente `index.php` y la antigua hoja `css/style.css` por una nueva interfaz construida a partir de componentes PHP, CSS modular y JavaScript moderno.
- **Backend**: aprovechar los endpoints existentes (`/api/mission`, `/api/forum/*`) y preparar ganchos para futuras fuentes de datos (por ejemplo, rutas turísticas y agenda cultural).
- **Interacción**: incorporar menús deslizantes laterales, paneles de agentes con datos dinámicos y microanimaciones que refuercen la narrativa.
- **Contenido**: sintetizar automáticamente fragmentos de los documentos clave en `docs/` para mantener la portada sincronizada con el corpus histórico.
- **Infraestructura**: documentar los cambios y establecer convenciones para futuros módulos (PHP, Flask, TypeScript, etc.).

## 4. Hoja de ruta inmediata
1. Crear biblioteca de componentes PHP (`includes/components/aurora_components.php`) con funciones recursivas para generar secciones.
2. Construir la nueva hoja de estilos (`css/aurora.css`) alineada con la identidad visual 2025 y preparada para efectos de transparencia y degradados.
3. Diseñar la portada (`index.php`) utilizando los componentes, mostrando misión, rutas, arqueología, agenda y foro vivo.
4. Implementar JavaScript (`js/aurora.js`) para menús deslizantes, carga de agentes desde Flask y animaciones.
5. Validar enlaces clave y documentar el flujo en esta hoja de plan para facilitar mantenibilidad.

## 5. Evolución a medio plazo
- Incorporar microservicios adicionales (FastAPI, NestJS, etc.) mediante una pasarela unificada.
- Migrar gradualmente páginas interiores a la nueva biblioteca de componentes.
- Integrar motores de recomendación turística y gamificación con IA ética.

> Este plan sirve como guía para la metamorfosis total solicitada, asegurando coherencia entre identidad, tecnología y comunidad.
