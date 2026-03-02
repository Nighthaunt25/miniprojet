<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$token = $_GET['token'] ?? '';
$email = trim($_GET['email'] ?? '');
$msg   = '';
$type  = 'error';

if ($token && $email) {
    $user = findUser($email);
    if ($user && $user['validated'] === '0' && $user['token_validation'] === $token) {
        updateUser($email, ['validated' => '1', 'token_validation' => '']);
        $msg  = 'Votre compte est maintenant activé ! Vous pouvez vous connecter.';
        $type = 'success';
    } elseif ($user && $user['validated'] === '1') {
        $msg  = 'Ce compte est déjà validé.';
        $type = 'info';
    } else {
        $msg  = 'Lien de validation invalide ou expiré.';
    }
} else {
    $msg = 'Lien invalide.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validation – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="brand">
            <h1>Le Monde</h1>
            <p>Validation de compte</p>
        </div>
        <div class="alert alert-<?= $type ?>"><?= clean($msg) ?></div>
        <p style="text-align:center;">
            <a href="index.php" class="btn btn-primary">Se connecter</a>
        </p>
    </div>
</div>
</body>
</html>
