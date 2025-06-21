<?php
require_once __DIR__ . '/../../../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../../../includes/auth.php';
require_admin_login();

$agents_file = __DIR__ . '/../../../config/forum_agents.php';
$agents = require $agents_file;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedAgents = [];
    if (isset($_POST['agent']) && is_array($_POST['agent'])) {
        foreach ($_POST['agent'] as $id => $data) {
            $updatedAgents[$id] = [
                'name' => trim($data['name'] ?? ''),
                'bio' => trim($data['bio'] ?? ''),
                'expertise' => trim($data['expertise'] ?? '')
            ];
        }
    }

    $newId = trim($_POST['new_agent_id'] ?? '');
    if ($newId !== '') {
        $updatedAgents[$newId] = [
            'name' => trim($_POST['new_agent_name'] ?? ''),
            'bio' => trim($_POST['new_agent_bio'] ?? ''),
            'expertise' => trim($_POST['new_agent_expertise'] ?? '')
        ];
    }

    $export = "<?php\nreturn " . var_export($updatedAgents, true) . ";\n";
    file_put_contents($agents_file, $export);
    $agents = $updatedAgents;
    $saved = true;
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
