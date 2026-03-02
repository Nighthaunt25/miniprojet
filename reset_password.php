<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$token  = $_GET['token'] ?? ($_POST['token'] ?? '');
$email  = $token ? validateResetToken($token) : null;
$errors = [];
$done   = false;

if (!$token || !$email) {
    $invalid = true;
} else {
    $invalid = false;
}

if (!$invalid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    if (!$errors) {
        updateUser($email, ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        deleteResetToken($token);
        $done = true;
        flash('success', 'Mot de passe mis à jour ! Vous pouvez vous connecter.');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nouveau mot de passe – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="brand">
            <h1>Le Monde</h1>
            <p>Nouveau mot de passe</p>
        </div>

        <?php if ($invalid): ?>
            <div class="alert alert-error">
                Ce lien est invalide ou a expiré (durée de vie : 1h).
            </div>
            <p style="text-align:center;">
                <a href="forgot_password.php" class="btn btn-primary">Demander un nouveau lien</a>
            </p>

        <?php elseif ($done): ?>
            <div class="alert alert-success">Mot de passe mis à jour avec succès !</div>
            <p style="text-align:center;">
                <a href="index.php" class="btn btn-primary">Se connecter</a>
            </p>

        <?php else: ?>
            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>• <?= clean($e) ?><br><?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="token" value="<?= clean($token) ?>">
                <div class="form-group">
                    <label for="password">Nouveau mot de passe (min. 8 caractères)</label>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label for="confirm">Confirmer le mot de passe</label>
                    <input type="password" id="confirm" name="confirm"
                           placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
