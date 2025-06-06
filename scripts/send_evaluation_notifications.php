<?php
// scripts/send_evaluation_notifications.php

// Allow running from CLI
if (php_sapi_name() !== 'cli' && php_sapi_name() !== 'cgi-fcgi') {
    die("This script is primarily intended for CLI execution.");
}

require_once __DIR__ . '/../config/ai_config.php';
require_once __DIR__ . '/../dashboard/db_connect.php'; // $pdo

// Ensure constants are defined (user must configure them)
if (!defined('ADMIN_EMAIL_NOTIFICATIONS') || ADMIN_EMAIL_NOTIFICATIONS === 'your_admin_email@example.com' ||
    !defined('BASE_URL_FOR_NOTIFICATIONS') || BASE_URL_FOR_NOTIFICATIONS === 'http://yourwebsite.com' ||
    !defined('SITE_NAME_FOR_NOTIFICATIONS')) {
    echo "Error: Please configure ADMIN_EMAIL_NOTIFICATIONS, BASE_URL_FOR_NOTIFICATIONS, and SITE_NAME_FOR_NOTIFICATIONS in config/ai_config.php\n";
    error_log("Notification Script Error: Admin email, base URL, or site name not configured.");
    exit(1);
}
if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY') {
    echo "Warning: GEMINI_API_KEY is not configured in config/ai_config.php. This script doesn't use it directly, but other related AI scripts will fail.\n";
    // Not exiting, as this script might still be testable for email logic if DB has data.
}


date_default_timezone_set('Europe/Madrid'); // Or your server's timezone

echo "Starting evaluation notification script...\n";
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 0); // No time limit for CLI script
set_time_limit(0);


$date_threshold = date('Y-m-d H:i:s', strtotime('-1 day'));
$flag_threshold_score = 5; // Scores at or below this will be flagged

$recent_evaluations = [];
$flagged_items = [];
$total_evaluated_count = 0;

try {
    $sql = "SELECT eval_id, text_id_fk, evaluated_at, clarity_score, engagement_score, seo_score, overall_assessment
            FROM ai_content_evaluations
            WHERE evaluated_at >= :date_threshold
            ORDER BY evaluated_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':date_threshold', $date_threshold);
    $stmt->execute();
    $recent_evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching recent evaluations: " . $e->getMessage() . "\n";
    error_log("Notification Script DB Error: " . $e->getMessage());
    exit(1);
}

$total_evaluated_count = count($recent_evaluations);

if ($total_evaluated_count === 0) {
    echo "No new evaluations in the last 24 hours. No notification will be sent.\n";
    // Optional: Send a "No new activity" email if desired
    // $no_activity_subject = "[" . SITE_NAME_FOR_NOTIFICATIONS . "] No New AI Evaluations " . date('Y-m-d');
    // $no_activity_body = "No new content pieces were evaluated by the AI in the last 24 hours.";
    // $no_activity_headers = "From: noreply@" . parse_url(BASE_URL_FOR_NOTIFICATIONS, PHP_URL_HOST);
    // mail(ADMIN_EMAIL_NOTIFICATIONS, $no_activity_subject, $no_activity_body, $no_activity_headers);
    exit(0);
}

foreach ($recent_evaluations as $eval) {
    $low_scores_found = [];
    if ($eval['clarity_score'] !== null && $eval['clarity_score'] <= $flag_threshold_score) $low_scores_found[] = "Claridad: " . $eval['clarity_score'];
    if ($eval['engagement_score'] !== null && $eval['engagement_score'] <= $flag_threshold_score) $low_scores_found[] = "Interés: " . $eval['engagement_score'];
    if ($eval['seo_score'] !== null && $eval['seo_score'] <= $flag_threshold_score) $low_scores_found[] = "SEO: " . $eval['seo_score'];

    if (!empty($low_scores_found)) {
        $flagged_items[] = [
            'text_id' => $eval['text_id_fk'],
            'scores' => implode(', ', $low_scores_found),
            'assessment' => mb_substr($eval['overall_assessment'] ?? 'N/A', 0, 150) . "..."
        ];
    }
}

// Build Email
$subject = "[" . SITE_NAME_FOR_NOTIFICATIONS . "] Resumen de Evaluación IA - " . date('Y-m-d');
$email_body_html = "<html><head><title>" . htmlspecialchars($subject) . "</title>";
$email_body_html .= "<style>body {font-family: Arial, sans-serif; line-height: 1.6; color: #333;} table {border-collapse: collapse; width: 100%; margin-bottom: 20px;} th, td {border: 1px solid #ddd; padding: 8px; text-align: left;} th {background-color: #f2f2f2;} h2, h3 {color: #333;} a {color: #007bff; text-decoration: none;} a:hover {text-decoration: underline;}</style>";
$email_body_html .= "</head><body>";
$email_body_html .= "<h2>Resumen de Evaluación de Contenido por IA</h2>";
$email_body_html .= "<p>" . $total_evaluated_count . " elemento(s) de contenido han sido evaluados en las últimas 24 horas.</p>";

if (!empty($flagged_items)) {
    $email_body_html .= "<h3>Elementos Marcados para Revisión (Puntuación &lt;= " . $flag_threshold_score . "):</h3>";
    $email_body_html .= "<table><thead><tr><th>Text ID</th><th>Puntuaciones Bajas</th><th>Resumen IA</th><th>Acción</th></tr></thead><tbody>";
    foreach ($flagged_items as $item) {
        $recommendations_url = rtrim(BASE_URL_FOR_NOTIFICATIONS, '/') . '/dashboard/ai_recommendations.php#textrow-' . urlencode($item['text_id']);
        $email_body_html .= "<tr>";
        $email_body_html .= "<td>" . htmlspecialchars($item['text_id']) . "</td>";
        $email_body_html .= "<td>" . htmlspecialchars($item['scores']) . "</td>";
        $email_body_html .= "<td>" . htmlspecialchars($item['assessment']) . "</td>";
        $email_body_html .= "<td><a href='" . htmlspecialchars($recommendations_url) . "'>Ver Recomendaciones</a></td>";
        $email_body_html .= "</tr>";
    }
    $email_body_html .= "</tbody></table>";
} else {
    $email_body_html .= "<p>¡Buenas noticias! No se marcaron elementos con puntuaciones consistentemente bajas (<= $flag_threshold_score) en este período.</p>";
}

$recommendations_main_url = rtrim(BASE_URL_FOR_NOTIFICATIONS, '/') . '/dashboard/ai_recommendations.php';
$email_body_html .= "<p style='margin-top:20px;'>Puede ver todas las evaluaciones en el <a href='" . htmlspecialchars($recommendations_main_url) . "'>Dashboard de Recomendaciones IA</a>.</p>";
$email_body_html .= "<hr><p style='font-size:0.9em; color:#777;'>Este es un correo automático generado por " . htmlspecialchars(SITE_NAME_FOR_NOTIFICATIONS) . ".</p>";
$email_body_html .= "</body></html>";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$from_email_domain = parse_url(BASE_URL_FOR_NOTIFICATIONS, PHP_URL_HOST);
// Sanitize domain in case parse_url returns false or null for some reason
$from_email_domain = $from_email_domain ? str_replace('www.', '', $from_email_domain) : 'yourwebsite.com'; // Fallback domain
$headers .= "From: noreply@" . $from_email_domain . "\r\n";

// Send email
if (mail(ADMIN_EMAIL_NOTIFICATIONS, $subject, $email_body_html, $headers)) {
    echo "Correo de notificación enviado a " . ADMIN_EMAIL_NOTIFICATIONS . "\n";
} else {
    echo "Error al enviar el correo de notificación. Verifique la configuración del servidor de correo (sendmail/postfix) y los logs de PHP.\n";
    error_log("Notification Script: mail() function failed. Target: " . ADMIN_EMAIL_NOTIFICATIONS . ", Subject: " . $subject);
}

echo "Script de notificación de evaluación finalizado.\n";
?>
