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
    <form method="post" action="">
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label for="mdp">Mots de Passe :</label>
            <input type="text" name="mdp" required>
        </div>
        <button type="submit" name="singup" value="0">S'inscrire</button>
    </form>
    <form method="post" action="">
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label for="mdp">Mots de Passe :</label>
            <input type="text" name="mdp" required>
        </div>
        <button type="submit" name="singin" value="0">S'inscrire</button>
    </form>
    <form action="">
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label for="code">Code : </label>
            <input type="text" name="code" >
        </div>
    </form>
</body>

</html>