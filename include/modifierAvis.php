<?php include "database.php" ?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier un avis</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css" />
    <meta charset="UTF-8" />
</head>
<body>
    <header>
        <h1>Modifier un avis</h1>
        <?php include "../template/nav.html"; ?>
    </header>

    <div class="main_wrapper">
    <form class="center" action="postAvis.php" method="POST">
    <ul>
        <li>

        <?php
        $querySelectFilm = $database->prepare("SELECT titre FROM tp_film WHERE id_film = " . $_GET["id_film"]);
        $querySelectFilm->execute();
        $data= $querySelectFilm->fetch();
        $querySelectFilm->closeCursor();
        ?>
            <label for="avis">Votre avis sur <?php echo $data["titre"]; ?>: </label>
        </li>
        <li>

        <?php
        $querySelectAvis = $database->prepare("SELECT avis
            FROM tp_historique_membre
            WHERE id_film =  " . $_GET["id_film"] . " AND id_membre = " . $_GET["id"]);
        $querySelectAvis->execute();
        $data = $querySelectAvis->fetch();
        $querySelectAvis->closeCursor();
        ?>

            <textarea name="avis" id="avis"><?php echo $data["avis"]; ?></textarea>
        </li>
        <li>
            <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
            <input type="hidden" name="id_film" value="<?php echo $_GET["id_film"]; ?>">
            <input type="submit" class="submit" value="Envoyer" /  >
        </li>
    </ul>
    </form>
    </div>

</body>
</html>