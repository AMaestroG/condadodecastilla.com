# Test Results - 2025-06-20

## `npm test`
The test suite failed with a timeout while waiting for `#google_translate_element`.
Log excerpt:

17:            this.#timeoutError = new Errors_js_1.TimeoutError(`Waiting failed: ${options.timeout}ms exceeded`);
20:TimeoutError: Waiting for selector `#google_translate_element` failed: Waiting failed: 30000ms exceeded


## `vendor/bin/phpunit`
PHPUnit did not run because `composer install` failed and the `vendor` directory was not created.
