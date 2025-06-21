# Nueva Web

Este subdirectorio contiene la evolución del proyecto Condado de Castilla.
Usa la paleta en morados y tonos de oro viejo con fondos de alabastro.
Integra PHP para el sitio público y un API minimal en Flask.

## Estructura
- `index.php` – página principal con menú deslizante.
- `assets/` – hojas de estilo y scripts.
- `flask_app.py` – API en Python.
- `templates/` – plantillas de Flask.

## Uso rápido
1. Instala las dependencias de PHP con `composer install` y las de Python con `pip install -r ../requirements.txt`.
2. Sirve `index.php` con el servidor integrado de PHP:
   - Instala las dependencias de Python y PHP si aún no lo has hecho:
     ```bash
     pip install -r ../requirements.txt
     composer install
     ```
   ```bash
   php -S localhost:8000
   ```
3. Ejecuta la API Flask en otra terminal:
   ```bash
   python3 flask_app.py
   ```

Consulta la [guía de estilo](../docs/style-guide.md) para ver la paleta completa y otras recomendaciones.
