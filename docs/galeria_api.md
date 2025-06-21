# API de la Galería Colaborativa

Este endpoint permite consultar y subir fotos de la comunidad.

## Obtener todas las fotos

`GET /api/galeria/fotos`

Devuelve una lista con todas las entradas ordenadas por `fecha_subida`.
Cada elemento incluye la URL completa de la imagen en el campo `imagenUrl`.

## Obtener una foto por ID

`GET /api/galeria/fotos/{id}`

Si el identificador existe se devuelve un objeto con los mismos campos que
la lista anterior. Si no existe la foto se responde con `404`.
