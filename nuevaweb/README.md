# Nueva Web

Este subdirectorio contiene la evolución del proyecto Condado de Castilla.
Usa la paleta en morados y tonos de oro viejo con fondos de alabastro.
Integra PHP para el sitio público y un API minimal en Flask.

## Estructura
- `index.php` – página principal con menú deslizante.
- `assets/` – hojas de estilo y scripts.
- `flask_app.py` – API en Python.
- `templates/` – plantillas de Flask.

## Getting Started

Asegúrate de contar con **PHP 8.1+**, **Python 3.10+** y la librería **Flask**.

1. Servir `index.php` con el servidor embebido de PHP:
   ```bash
   cd nuevaweb
   php -S localhost:8000
   ```
   Luego visita `http://localhost:8000/index.php`.

2. Ejecutar la API de Flask:
   ```bash
   python flask_app.py
   ```
   Estará disponible en `http://localhost:5000/api/hello`.
