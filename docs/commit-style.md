# Guía de mensajes de commit

Sigue estas pautas para mantener un historial claro y uniforme.

## Estructura

1. **Línea de resumen**: utiliza el modo imperativo y procura no sobrepasar los 72 caracteres.
2. **Cuerpo opcional**: explica *qué* y *por qué* en párrafos breves.
3. **Footer** (opcional): referencia incidencias o tareas relacionadas.

## Ejemplos

- `Add menu group translations (es/en/gl)`

  Cuerpo: "Adds `about.us` and `forum.rules` keys".
- `Fix broken link in footer`
- `Update gallery API docs`
- `Add GSAP page transitions with overlay and tests`

Al añadir traducciones, enumera en el cuerpo las claves nuevas o modificadas.

## Plantilla

El repositorio incluye `.gitmessage.txt` como ejemplo de plantilla. Puedes
configurarla con:

```bash
git config commit.template .gitmessage.txt
```
