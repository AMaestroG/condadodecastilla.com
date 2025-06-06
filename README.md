# Deployment Notes

This project contains a small PHP dashboard under `dashboard/` which connects to a PostgreSQL database. For security, the database password is loaded from an environment variable.

## Configuration

Set the `CONDADO_DB_PASSWORD` environment variable on the server before running the dashboard. The `dashboard/db_connect.php` file reads this variable to obtain the database password.

The development configuration enables PHP error display. For production deployments these settings are commented out in `db_connect.php` and should remain disabled.
