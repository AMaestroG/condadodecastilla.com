# Crawler

Esta página describe el pequeño crawler incluido en `crawler.py`. Su objetivo principal es recorrer páginas de manera controlada y extraer enlaces para poblar la base de datos de conocimiento. Actualmente el código ofrece una versión de demostración.

## ¿Qué hace `crawler.py`?
- Simula la descarga de páginas web mediante `fetch_page`. Por ahora sólo responde con HTML predeterminado para `http://example.com` y `/another-page`.
- Analiza el contenido con BeautifulSoup a través de `parse_html` para obtener el título y todos los enlaces válidos.
- Genera un diccionario `web_resource_data` que almacena la URL, un identificador único y la fecha de rastreo.
- Devuelve también una lista de enlaces como `links_data`, cada uno con su propio identificador.

Este módulo es un punto de partida para construir un rastreador más completo. En el futuro puede integrarse con comprobaciones de `robots.txt`, manejo de errores HTTP o almacenamiento directo en la base de datos de grafo.

## Uso básico
1. Instala las dependencias necesarias (ver `requirements.txt`).
2. Ejecuta el script directamente para probar con `example.com`:

```bash
python crawler.py
```

El resultado mostrará ejemplos de `WebResource` y de enlaces extraídos.

## Relación con el proyecto
El rastreador sirve para recopilar información de diferentes páginas relacionadas con Cerezo de Río Tirón. Los datos generados pueden almacenarse en la base `knowledge_graph_db.json` mediante `graph_db_interface.py`.
