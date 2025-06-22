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
2. Sirve `index.php` con el servidor integrado de PHP desde la raíz del repositorio:
   ```bash
   php -S localhost:8000 -t nuevaweb
   ```
3. Ejecuta la API Flask en otra terminal:
   ```bash
   python3 flask_app.py
   ```

4. Como alternativa a los pasos anteriores puedes usar Docker Compose para
   levantar ambos servicios ya configurados:
   ```bash
   docker-compose up nuevaweb_php nuevaweb_flask
   ```
   Esto expondrá la web en `http://localhost:8082` y la API en
   `http://localhost:5002`.

5. Abre tu navegador y navega a
   `http://localhost:8000/nuevaweb/index.php` para ver la página inicial
   (o `http://localhost:8082/nuevaweb/index.php` si utilizas Docker).

Consulta la [guía de estilo](../docs/style-guide.md) para ver la paleta completa y otras recomendaciones.
