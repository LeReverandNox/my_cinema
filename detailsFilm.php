<?php
include "include/database.php";
if (!isset($_GET["id"]) || $_GET["id"] == "")
{
        header('Location: films.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Détails</title>
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <meta charset="UTF-8" />
</head>
<body>
    <header>
        <h1>Détails du film</h1>
        <?php include "template/nav.html"; ?>
    </header>

    <div class="main_wrapper">
        <?php
            $queryDetailsFilm = $database->prepare("SELECT tp_film.titre AS titre, tp_genre.nom AS genre, tp_distrib.nom AS distrib, tp_film.annee_prod AS annee, tp_film.duree_min AS duree, tp_film.resum AS resum
                FROM tp_film
                LEFT JOIN tp_genre
                ON tp_film.id_genre = tp_genre.id_genre
                LEFT JOIN tp_distrib
                ON tp_film.id_distrib = tp_distrib.id_distrib
                WHERE tp_film.id_film = " . (int)$_GET["id"] . "");
            $queryDetailsFilm->execute();

            $data = $queryDetailsFilm->fetch();
            $queryDetailsFilm->closeCursor();
        ?>
            <h2>
                <?php if (!empty($data["titre"]))
                {
                    echo $data["titre"];
                }
                else
                {
                    echo "Film introuvable";
                } ?>
            </h2>
            <p>Genre :
                <?php if (!empty($data["genre"]))
                {
                    echo "inconnu";
                }
                else
                {
                    echo $data["genre"];
                } ?>
            </p>
            <p>Distributeur :
                <?php if (!empty($dta["distrib"]))
                {
                    echo $data["distrib"];
                }
                else
                {
                    echo "inconnu";
                } ?>
            </p>
            <p>Année de production : <?php echo $data["annee"]; ?></p>
            <p>Durée : <?php echo $data["duree"]; ?> min.</p>
            <p>Resumé : <?php echo $data["resum"]; ?></p>

            <a href="films.php">Retour à la recherche</a>
    </div>

</body>
</html>