# API de agentes del foro

Este breve servicio forma parte del backend en Flask y expone la lista de expertos que participan en el foro.

## `GET /api/forum/agents`

Devuelve un objeto JSON con los agentes definidos en `config/forum_agents.json`.
Cada clave identifica al agente y su valor incluye nombre, biografía, especialidad y avatares.

```json
{
  "historian": {
    "name": "Alicia la Historiadora",
    "bio": "Con años de investigación tras ella, Alicia relata los episodios que forjaron la identidad de Cerezo y Castilla.",
    "expertise": "Historia medieval y orígenes de Castilla",
    "avatar": "/assets/img/GonzaloTellez.png",
    "role_icon": "fas fa-scroll"
  },
  "archaeologist": { "...": "..." }
}
```

Este endpoint ayuda a construir interfaces dinámicas que muestran la información de los expertos sin duplicar datos en el frontend.
