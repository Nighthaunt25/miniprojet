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
            <h4>Bienvenue, <?php // Votre code ici pour afficher le nom de l'utilisateur connecté ?>
            </h4>
            <p>Email :
                <strong><?php?></strong>
            </p>
            <p>Connecté depuis :
                <strong><?php // Votre code ici pour afficher la date et l'heure de connexion ?></strong>
            </p>
            <form method="post" action="">
                <button type="submit" name="logout" value="0" class="btn btn-warning">Se
                    déconnecter</button>
            </form>
        </div>
    <?php else: ?>
        <form method="post" action="">
            <div >
                <label for="nom">Nom :</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div >
                <label for="email">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" name="login" value="0" class="btn btn-success">Se connecter</button>
        </form>
    <?php endif; ?>
</body>
</html>