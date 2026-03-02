<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$msg   = '';
$type  = '';
$sent  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg  = 'Adresse email invalide.';
        $type = 'error';
    } else {
        $user = findUser($email);
        // Par sécurité on affiche toujours le même message (même si user inconnu)
        if ($user && $user['validated'] === '1') {
            $token = generateResetToken($email);
            sendResetEmail($email, $token);
        }
        $msg  = 'Si cette adresse existe dans notre base, un email vous a été envoyé.';
        $type = 'info';
        $sent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="brand">
            <h1>Le Monde</h1>
            <p>Réinitialisation du mot de passe</p>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?= $type ?>"><?= clean($msg) ?></div>
        <?php endif; ?>

        <?php if (!$sent): ?>
            <p style="font-family:Arial,sans-serif; font-size:.88rem; margin-bottom:1rem; color:#555;">
                Entrez votre adresse email pour recevoir un lien de réinitialisation.
            </p>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email"
                           placeholder="votre@email.fr" required
                           value="<?= clean($_POST['email'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Envoyer le lien</button>
            </form>
        <?php endif; ?>

        <div class="links" style="text-align:center; margin-top:1rem; font-family:Arial,sans-serif; font-size:.85rem;">
            <a href="index.php" style="color:#e20025; text-decoration:none;">← Retour à la connexion</a>
        </div>
    </div>
</div>
</body>
</html>
