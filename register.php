<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';
    $subs      = $_POST['subscriptions'] ?? [];

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    if (empty($subs)) {
        $errors[] = 'Veuillez sélectionner au moins un centre d\'intérêt.';
    }
    // Vérifier si email déjà pris
    if (!$errors && findUser($email)) {
        $errors[] = 'Cette adresse email est déjà utilisée.';
    }

    // Filtrer les abonnements valides
    $validFeeds   = array_keys(RSS_FEEDS);
    $subs         = array_intersect($subs, $validFeeds);

    if (!$errors) {
        $token = createUser($email, $password, array_values($subs));
        sendValidationEmail($email, $token);
        $success = true;
    }
}

$feeds = array_keys(RSS_FEEDS);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrapper" style="background:#1a1a1a; padding:2rem 1rem; align-items:flex-start; min-height:100vh;">
    <div style="width:100%; max-width:600px; margin:2rem auto;">
        <div class="card">
            <div class="brand" style="text-align:center; margin-bottom:1.5rem;">
                <h1 style="font-size:1.8rem; color:#e20025;">Le Monde</h1>
                <p style="color:#888; font-family:Arial,sans-serif; font-size:.85rem;">Créer un compte</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <strong>Inscription réussie !</strong><br>
                    Un email de validation a été envoyé à <strong><?= clean($_POST['email']) ?></strong>.
                    Cliquez sur le lien dans l'email pour activer votre compte.
                </div>
                <p style="text-align:center; font-family:Arial,sans-serif;">
                    <a href="index.php" class="btn btn-primary">Retour à la connexion</a>
                </p>
            <?php else: ?>

                <?php if ($errors): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $e): ?>
                            • <?= clean($e) ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Adresse email *</label>
                        <input type="email" id="email" name="email"
                               value="<?= clean($_POST['email'] ?? '') ?>"
                               placeholder="votre@email.fr" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe * (min. 8 caractères)</label>
                        <input type="password" id="password" name="password"
                               placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm">Confirmer le mot de passe *</label>
                        <input type="password" id="confirm" name="confirm"
                               placeholder="••••••••" required>
                    </div>

                    <div class="form-group">
                        <label>Centres d'intérêt * (sélectionnez au moins un flux)</label>
                        <div class="subscriptions-grid">
                            <?php foreach ($feeds as $cat): ?>
                                <?php
                                $checked = isset($_POST['subscriptions'])
                                    && in_array($cat, $_POST['subscriptions']);
                                ?>
                                <div class="sub-item">
                                    <input type="checkbox" id="sub_<?= md5($cat) ?>"
                                           name="subscriptions[]"
                                           value="<?= clean($cat) ?>"
                                           <?= $checked ? 'checked' : '' ?>>
                                    <label for="sub_<?= md5($cat) ?>">📰 <?= clean($cat) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
                </form>

                <div style="text-align:center; margin-top:1rem; font-family:Arial,sans-serif; font-size:.85rem;">
                    <a href="index.php" style="color:#e20025; text-decoration:none;">
                        Déjà un compte ? Se connecter
                    </a>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
