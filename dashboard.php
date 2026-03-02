<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$user = requireLogin();
// Rafraichir les données user depuis le fichier
$user = findUser($user['email']) ?? $user;
$_SESSION['user'] = $user;

$flash = getFlash();
$allFeeds   = RSS_FEEDS;
$userSubs   = $user['subscriptions'] ?? [];

// Récupérer les flux RSS
$feedData = [];
foreach ($userSubs as $cat) {
    if (isset($allFeeds[$cat])) {
        $items = fetchRSS($allFeeds[$cat]);
        if ($items) $feedData[$cat] = $items;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes flux RSS – <?= SITE_NAME ?></title>
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

<div class="container-wide">
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>" style="margin-top:1rem;">
            <?= clean($flash['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="feeds-header" style="margin-top:1.5rem;">
        <div>
            <h1>Bonjour 👋</h1>
            <p>Voici vos flux RSS du jour – <?= date('d/m/Y') ?></p>
        </div>
        <a href="subscriptions.php" class="btn btn-outline">⚙ Gérer mes abonnements</a>
    </div>

    <?php if (empty($userSubs)): ?>
        <div class="card" style="text-align:center; padding:3rem;">
            <p style="font-size:1.1rem; margin-bottom:1rem;">
                Vous n'avez pas encore de centres d'intérêt.
            </p>
            <a href="subscriptions.php" class="btn btn-primary">Choisir mes flux RSS</a>
        </div>

    <?php elseif (empty($feedData)): ?>
        <div class="card loading">
            <p>⏳ Impossible de charger les flux RSS pour le moment.<br>
            Vérifiez votre connexion internet ou réessayez plus tard.</p>
        </div>

    <?php else: ?>
        <?php foreach ($feedData as $cat => $articles): ?>
            <div class="category-block">
                <h3>📂 <?= clean($cat) ?></h3>
                <div class="articles-grid">
                    <?php foreach ($articles as $art): ?>
                        <div class="article-card">
                            <?php if (!empty($art['image'])): ?>
                                <img src="<?= clean($art['image']) ?>"
                                     alt="<?= clean($art['title']) ?>"
                                     loading="lazy"
                                     onerror="this.parentNode.innerHTML='<div class=\'placeholder-img\'>📰</div>'">
                            <?php else: ?>
                                <div class="placeholder-img">📰</div>
                            <?php endif; ?>
                            <div class="article-body">
                                <h4>
                                    <a href="<?= clean($art['link']) ?>" target="_blank" rel="noopener">
                                        <?= clean($art['title']) ?>
                                    </a>
                                </h4>
                                <p><?= clean(substr($art['description'], 0, 160)) ?>…</p>
                                <?php if ($art['pubDate']): ?>
                                    <div class="article-date">
                                        🕐 <?= clean($art['pubDate']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer>
    &copy; <?= date('Y') ?> Le Monde – Aggrégateur RSS &nbsp;|&nbsp;
    <a href="https://www.lemonde.fr" target="_blank" style="color:#aaa;">www.lemonde.fr</a>
</footer>

</body>
</html>
