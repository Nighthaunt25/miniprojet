<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Déjà connecté
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $user = findUser($email);
        if (!$user) {
            $error = 'Identifiants incorrects.';
        } elseif ($user['validated'] !== '1') {
            $error = 'Votre compte n\'est pas encore validé. Vérifiez votre email.';
        } elseif (!password_verify($password, $user['password'])) {
            $error = 'Identifiants incorrects.';
        } else {
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            exit;
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="brand">
            <h1>Le Monde</h1>
            <p>Aggrégateur de flux RSS</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><?= clean($flash['msg']) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"
                       value="<?= clean($_POST['email'] ?? '') ?>"
                       placeholder="votre@email.fr" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <div class="links">
            <a href="forgot_password.php">Mot de passe oublié ?</a>
            &nbsp;·&nbsp;
            <a href="register.php">Créer un compte</a>
        </div>
    </div>
</div>
</body>
</html>
