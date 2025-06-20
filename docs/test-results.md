# Test Results - 2025-06-20

## `vendor/bin/phpunit`
34 tests executed with 3 errors and 27 failures. Most failures stem from `php-cgi` not being available and database drivers missing.

## `python -m unittest`
All tests passed.

## `npm run test:puppeteer`
The suite failed with a timeout while waiting for `#google_translate_element`.

