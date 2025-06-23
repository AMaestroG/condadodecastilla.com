<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();

header('Content-Type: application/json');

$setupScript = __DIR__ . '/../scripts/setup_project.sh';
if (!is_file($setupScript)) {
    $setupScript = __DIR__ . '/../scripts/setup_environment.sh';
}

$response = [
    'success' => false,
    'message' => '',
    'output'  => '',
    'exit_code' => 1,
];

if (!is_file($setupScript)) {
    $response['message'] = 'Script de configuración no encontrado.';
    echo json_encode($response);
    exit;
}

$descriptorSpec = [
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$process = proc_open($setupScript, $descriptorSpec, $pipes);
if (is_resource($process)) {
    $output = stream_get_contents($pipes[1]);
    $errorOutput = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    $response['success'] = ($exitCode === 0);
    $response['exit_code'] = $exitCode;
    $response['output'] = $output . $errorOutput;
    $response['message'] = $response['success']
        ? 'Configuración completada.'
        : 'El script terminó con errores.';
} else {
    $response['message'] = 'No se pudo ejecutar el script.';
}

echo json_encode($response);
