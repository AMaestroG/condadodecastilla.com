# Test Results - 2025-06-20

## `vendor/bin/phpunit`
34 tests executed with 3 errors and 19 failures. Tras instalar `php-cgi` y
`pdo_pgsql` las pruebas que requieren conexión a PostgreSQL siguen fallando
porque la base de datos no está disponible.

## `python -m unittest`
All tests passed.

## `npm run test:puppeteer`
The suite failed with a timeout while waiting for `#google_translate_element`.

Consulta la sección [Solución de problemas](testing.md#solucion-de-problemas) de
la guía de testing para intentar corregir estos fallos.

