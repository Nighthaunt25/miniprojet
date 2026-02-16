<?php
session_start()

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="">
    <title></title>
</head>

<body>
    <?php if (false): ?>
        <div class="">
            <form method="post" action="">
                <button type="submit" name="logout" value="0" class="btn btn-warning">Se
                    déconnecter</button>
            </form>
        </div>
    <?php else: ?>
        <form method="post" action="">
            <div >
                <label for="nom">Nom :</label>
                <input type="text" name="nom" required>
            </div>
            <div >
                <label for="email">Email :</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label for="mdp">Mots de Passe :</label>
                <input type="text" name="mdp" required>
            </div>
            <button type="submit" name="login" value="0">Se connecter</button>
        </form>
    <?php endif; ?>
</body>
</html>