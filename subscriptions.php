<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$user = requireLogin();
$user = findUser($user['email']) ?? $user;

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subs = $_POST['subscriptions'] ?? [];
    // Valider les abonnements
    $validFeeds = array_keys(RSS_FEEDS);
    $subs = array_values(array_intersect($subs, $validFeeds));

    if (empty($subs)) {
        $errors[] = 'Sélectionnez au moins un flux RSS.';
    } else {
        updateUser($user['email'], ['subscriptions' => $subs]);
        $user['subscriptions'] = $subs;
        $_SESSION['user'] = array_merge($_SESSION['user'], ['subscriptions' => $subs]);
        flash('success', 'Vos abonnements ont été mis à jour !');
        header('Location: dashboard.php');
        exit;
    }
}

$userSubs = $user['subscriptions'] ?? [];
$allFeeds = array_keys(RSS_FEEDS);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes abonnements – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <a href="dashboard.php" class="logo">Le <span>Monde</span> RSS</a>
    <nav>
        <a href="dashboard.php">Mes flux</a>
        <a href="subscriptions.php">Mes abonnements</a>
        <a href="logout.php" class="btn-nav">Déconnexion</a>
    </nav>
</header>

<div class="container">
    <div class="card" style="margin-top:2rem;">
        <h2>⚙ Gérer mes abonnements</h2>
        <p style="font-family:Arial,sans-serif; font-size:.88rem; color:#666; margin-bottom:1.5rem;">
            Sélectionnez les catégories que vous souhaitez suivre.
        </p>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>• <?= clean($e) ?><br><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="subscriptions-grid">
                <?php foreach ($allFeeds as $cat): ?>
                    <?php $checked = in_array($cat, $userSubs); ?>
                    <div class="sub-item">
                        <input type="checkbox"
                               id="sub_<?= md5($cat) ?>"
                               name="subscriptions[]"
                               value="<?= clean($cat) ?>"
                               <?= $checked ? 'checked' : '' ?>>
                        <label for="sub_<?= md5($cat) ?>">📰 <?= clean($cat) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:2rem; display:flex; gap:1rem; align-items:center;">
                <button type="submit" class="btn btn-primary">Enregistrer mes abonnements</button>
                <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>👤 Mon compte</h2>
        <p style="font-family:Arial,sans-serif; font-size:.9rem;">
            <strong>Email :</strong> <?= clean($user['email']) ?><br>
            <strong>Compte créé le :</strong> <?= clean($user['created_at']) ?><br>
            <strong>Abonnements actifs :</strong> <?= count($userSubs) ?> flux RSS
        </p>
        <p style="margin-top:1rem;">
            <a href="forgot_password.php" class="btn btn-outline" style="font-size:.85rem;">
                🔑 Changer mon mot de passe
            </a>
        </p>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Le Monde – Aggrégateur RSS
</footer>

</body>
</html>
