# Setting Up Scheduled AI Content Evaluation via Cron

This guide explains how to set up a cron job on your Ubuntu server to automatically run the AI content evaluation script (`scripts/evaluate_content_ai.php`) on a regular basis (e.g., daily). This ensures that the AI recommendations in your dashboard are kept up-to-date.

## Prerequisites

1.  **PHP CLI:** Ensure that PHP CLI (Command Line Interface) is installed on your server. You can usually install it with:
    ```bash
    sudo apt update
    sudo apt install php-cli
    ```
2.  **Script Path:** You'll need the absolute path to the `evaluate_content_ai.php` script within your web project. For example, if your project is in `/var/www/html`, the path might be `/var/www/html/scripts/evaluate_content_ai.php`.
3.  **Writable Logs (Optional but Recommended):** Decide where you want to store logs from the cron job. For example, `/var/www/html/logs/cron_eval.log`. Ensure the directory exists and is writable by the user running the cron job (often `www-data` or your own user if you set it up under your user's crontab).

## 1. Command to Execute the Script

The basic command to run the script is:
```bash
php /path/to/your/project/scripts/evaluate_content_ai.php
```
Replace `/path/to/your/project/` with the actual absolute path to your project's root directory.

**Example:**
If your project is in `/var/www/html/condado_castilla_web`, the command would be:
```bash
php /var/www/html/condado_castilla_web/scripts/evaluate_content_ai.php
```

## 2. Editing the Crontab

You'll add a line to your server's crontab file. To edit the crontab for the current user, use:
```bash
crontab -e
```
If this is the first time, it might ask you to choose an editor (e.g., nano).

If you want the cron job to run as a specific user (like `www-data` if your web server runs as `www-data` and needs to manage file permissions consistently, though this script primarily interacts with the DB and AI API), you might use:
```bash
sudo crontab -u www-data -e
```
However, running as your own user is often simpler if file permission issues aren't a concern for this script.

## 3. Example Cron Job Line

Here's an example cron line to run the script daily at 3:00 AM:

```cron
0 3 * * * /usr/bin/php /path/to/your/project/scripts/evaluate_content_ai.php >> /path/to/your/project/logs/cron_eval.log 2>&1
```

Let's break this down:
*   `0 3 * * *`: This is the schedule. It means:
    *   `0`: Minute (0th minute)
    *   `3`: Hour (3 AM)
    *   `*`: Day of the month (every day)
    *   `*`: Month (every month)
    *   `*`: Day of the week (every day of the week)
*   `/usr/bin/php`: It's good practice to use the full path to the `php` executable. You can find this with `which php`.
*   `/path/to/your/project/scripts/evaluate_content_ai.php`: The absolute path to your script. **Remember to change this.**
*   `>> /path/to/your/project/logs/cron_eval.log 2>&1`: This part handles logging:
    *   `>>`: Appends the standard output (what the script `echo`s) to the specified log file.
    *   `/path/to/your/project/logs/cron_eval.log`: The path to your log file. **Remember to change this and ensure the `logs` directory exists and is writable.**
    *   `2>&1`: Redirects standard error (any error messages) to the same place as standard output (i.e., into your log file).

## 4. Saving and Verifying

*   After adding the line to your crontab, save and exit the editor.
    *   If using `nano`, it's usually `Ctrl+X`, then `Y` (yes), then `Enter`.
*   You can verify that the cron job has been added (for the current user) with:
    ```bash
    crontab -l
    ```

## Important Considerations

*   **Absolute Paths:** Always use absolute paths for everything in cron jobs (the PHP executable, the script itself, log files). Cron jobs don't usually inherit the same environment variables as your interactive shell.
*   **Permissions:** The script needs execute permissions. The user running the cron job must have read access to the script and any included files, and write access to the log directory/file. Database credentials used in `db_connect.php` must be valid for the environment where the cron job runs.
*   **PHP Configuration:** The PHP CLI environment might have a different `php.ini` configuration than your web server. If your script relies on specific PHP extensions (like `pdo_pgsql` or `curl`), ensure they are enabled for the CLI version as well. The `evaluate_content_ai.php` script currently uses `pdo` and `curl` (via `gemini_api_client.php`).
*   **API Keys & Config:** Ensure `config/ai_config.php` (containing the Gemini API key) is readable by the user running the cron job.
*   **Error Logging:** The `2>&1` redirection is crucial for capturing errors. Check this log file if the script doesn't seem to be running as expected.
*   **Testing:** You can temporarily set the cron job to run every few minutes for testing (e.g., `*/5 * * * * ...` for every 5 minutes), then set it back to your desired schedule once you confirm it's working. Remember to check the log file.

By following these steps, you can automate the AI content evaluation process, ensuring your dashboard always has fresh insights.
