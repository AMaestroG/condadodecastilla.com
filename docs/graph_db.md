# Base de datos de grafo

`graph_db_interface.py` implementa una interfaz sencilla para almacenar recursos web y enlaces en formato JSON. El archivo de persistencia predeterminado es `knowledge_graph_db.json`.

## Características principales
- Guarda nodos (recursos) en un diccionario donde la clave es la URL. Cada nodo incluye un `id`, la fecha de rastreo y campos de contenido o metadatos.
- Los enlaces se almacenan como una lista de diccionarios con `source_url`, `target_url` y un identificador propio.
- Al agregar enlaces se crean recursos "placeholder" si la fuente o destino no existen aún.
- Todos los cambios se guardan automáticamente en `knowledge_graph_db.json` para conservar el estado entre ejecuciones.

## Estructura de `knowledge_graph_db.json`
El archivo contiene dos claves principales:

```json
{
  "nodes": {
    "http://example.com": {
      "id": "...",
      "url": "http://example.com",
      "content": "Texto o título",
      "last_crawled_at": "fecha ISO",
      "metadata": {"title": "Example Domain"}
    }
  },
  "edges": [
    {
      "id": "...",
      "source_url": "http://example.com",
      "target_url": "http://www.iana.org/domains/example",
      "anchor_text": "More information...",
      "created_at": "fecha ISO",
      "source_resource_id": "...",
      "target_resource_id": "..."
    }
  ]
}
```

## Uso básico
1. Instancia la clase en tu aplicación:

```python
from graph_db_interface import GraphDBInterface

db = GraphDBInterface()
```

2. Añade recursos o enlaces:

```python
resource = {"url": "http://example.com", "content": "Example"}
db.add_or_update_resource(resource)

link = {"source_url": "http://example.com", "target_url": "http://example.net", "anchor_text": "Ejemplo"}
db.add_link(link)
```

3. Consulta la base de datos:

```python
all_resources = db.get_all_resources()
all_links = db.get_all_links()
```

Esta herramienta sirve de apoyo para organizar la información relacionada con Cerezo de Río Tirón y facilitar su análisis dentro del proyecto de promoción turística y gestión patrimonial.
