<?php
require_once __DIR__ . '/../../../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../../../includes/auth.php';
require_admin_login();

$agents_file = __DIR__ . '/../../../config/forum_agents.php';
$agents = require $agents_file;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Empezar con los agentes existentes para preservar los no modificados
    // y sus campos como 'avatar' y 'role_icon' que no están en este formulario.
    $updatedAgents = $agents;

    if (isset($_POST['agent']) && is_array($_POST['agent'])) {
        foreach ($_POST['agent'] as $id_raw => $data) {
            // Sanitizar el ID del agente que viene de la clave del array del formulario
            $id = preg_replace('/[^a-zA-Z0-9_-]/', '', $id_raw);
            if (empty($id)) {
                continue; // Omitir si el ID sanitizado es inválido o vacío
            }

            if (isset($updatedAgents[$id])) { // Modificar solo si el agente existe
                $updatedAgents[$id]['name'] = htmlspecialchars(trim($data['name'] ?? $updatedAgents[$id]['name']), ENT_QUOTES, 'UTF-8');
                $updatedAgents[$id]['bio'] = htmlspecialchars(trim($data['bio'] ?? $updatedAgents[$id]['bio']), ENT_QUOTES, 'UTF-8');
                $updatedAgents[$id]['expertise'] = htmlspecialchars(trim($data['expertise'] ?? $updatedAgents[$id]['expertise']), ENT_QUOTES, 'UTF-8');
                // 'avatar' y 'role_icon' se mantienen de $updatedAgents (que se inicializó con $agents)
            }
        }
    }

    $newId_raw = trim($_POST['new_agent_id'] ?? '');
    if ($newId_raw !== '') {
        // Sanitizar el ID del nuevo agente
        $newId = preg_replace('/[^a-zA-Z0-9_-]/', '', $newId_raw);
        if ($newId !== '' && !isset($updatedAgents[$newId])) { // Solo añadir si el ID es válido y no existe ya
            $updatedAgents[$newId] = [
                'name' => htmlspecialchars(trim($_POST['new_agent_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'bio' => htmlspecialchars(trim($_POST['new_agent_bio'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'expertise' => htmlspecialchars(trim($_POST['new_agent_expertise'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'avatar' => '', // Valor por defecto para nuevos agentes
                'role_icon' => ''  // Valor por defecto para nuevos agentes
            ];
        } elseif ($newId === '') {
            // Opcional: feedback al usuario que el ID del nuevo agente es inválido
        } elseif (isset($updatedAgents[$newId])) {
            // Opcional: feedback al usuario que el ID del nuevo agente ya existe
        }
    }

    // Antes de guardar, asegurar que todos los agentes tengan los campos esperados
    // (avatar, role_icon) para evitar errores si se accede a ellos después.
    foreach ($updatedAgents as $id => &$agentData) {
        $agentData['avatar'] = $agentData['avatar'] ?? ($agents[$id]['avatar'] ?? '/assets/img/placeholder_personaje.png');
        $agentData['role_icon'] = $agentData['role_icon'] ?? ($agents[$id]['role_icon'] ?? 'fas fa-user');
    }
    unset($agentData); // Romper la referencia del último elemento

    $export = "<?php\nreturn " . var_export($updatedAgents, true) . ";\n";
    if (file_put_contents($agents_file, $export) === false) {
        // Error al guardar el archivo
        $saved = false; // Indicar que no se guardó
        // Considerar loguear el error
    } else {
        $agents = $updatedAgents; // Actualizar la variable local $agents
        $saved = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Agentes del Foro</title>
    <link rel="stylesheet" href="/assets/css/admin_theme.css">
</head>
<body class="alabaster-bg admin-page">
<?php if (!empty($saved)) echo '<p class="success">Cambios guardados.</p>'; ?>
<form method="post">
    <h1>Editar Agentes Existentes</h1>
    <?php foreach ($agents as $id => $agent): ?>
        <fieldset>
            <legend><?php echo htmlspecialchars($id); ?></legend>
            <label>Nombre:<br><input type="text" name="agent[<?php echo htmlspecialchars($id); ?>][name]" value="<?php echo htmlspecialchars($agent['name']); ?>"></label><br>
            <label>Biografía:<br><textarea name="agent[<?php echo htmlspecialchars($id); ?>][bio]" rows="3" cols="40"><?php echo htmlspecialchars($agent['bio']); ?></textarea></label><br>
            <label>Experiencia:<br><input type="text" name="agent[<?php echo htmlspecialchars($id); ?>][expertise]" value="<?php echo htmlspecialchars($agent['expertise']); ?>"></label>
        </fieldset>
    <?php endforeach; ?>
    <h2>Añadir Nuevo Agente</h2>
    <label>ID:<br><input type="text" name="new_agent_id"></label><br>
    <label>Nombre:<br><input type="text" name="new_agent_name"></label><br>
    <label>Biografía:<br><textarea name="new_agent_bio" rows="3" cols="40"></textarea></label><br>
    <label>Experiencia:<br><input type="text" name="new_agent_expertise"></label><br>
    <button type="submit">Guardar</button>
</form>
</body>
</html>

