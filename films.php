<?php include "include/database.php" ?>
<!DOCTYPE html>
<html>
<head>
    <title>Les Films</title>
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <meta charset="UTF-8" />
</head>
<body>
    <div class="main_wrapper">
        <header>
            <h1>Rechercher des films</h1>
            <?php include "template/nav.html"; ?>
        </header>
        <div id="content">
            <div id="recherche">
                <h2>Recherche</h2>
                <form action="films.php" method="GET">

                    <?php
                    if (empty($_GET["page"]))
                    {
                        $_GET["page"] = 1;
                    }
                    if (empty($_GET["limit"]))
                    {
                        $_GET["limit"] = 20;
                    }
                    if (empty($_GET["titre"]))
                    {
                        $_GET["titre"] = "";
                    }
                    if (empty($_GET["genre"]))
                    {
                        $_GET["genre"] = "";
                    }
                    if (empty($_GET["distributeur"]))
                    {
                        $_GET["distributeur"] = "";
                    }

                    $start = ($_GET["page"] * $_GET["limit"]) - $_GET["limit"];
                    ?>

                    <ul>
                        <li>
                            <label for "titre">Titre :</label>
                            <input type="text" name="titre" id="titre" value="<?php echo $_GET["titre"]; ?>"/>
                        </li>
                        <li>
                            <label for "genre">Genre :</label>
                            <select name="genre" id="genre">
                                <option value="">Tout</option>
                                <?php
                                $querySelectGenre = $database->query("SELECT * FROM  tp_genre ORDER BY nom");
                                while ($data = $querySelectGenre->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data["id_genre"]; ?>" <?php if($_GET["genre"] == $data["id_genre"]) { echo "selected"; } ?>><?php echo ucfirst($data["nom"]); ?></option>
                                    <?php
                                }
                                $querySelectGenre->closeCursor();
                                ?>
                            </select>
                        </li>
                        <li>
                            <label for "distributeur">Distributeur :</label>
                            <select name="distributeur" id="distributeur">
                                <option value="">Tout</option>
                                <?php
                                $querySelectDistributeur = $database->query("SELECT * FROM  tp_distrib ORDER BY nom");
                                while ($data = $querySelectDistributeur->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data["id_distrib"]; ?>" <?php if($_GET["distributeur"] == $data["id_distrib"]) { echo "selected"; } ?>><?php echo ucfirst($data["nom"]); ?></option>
                                    <?php
                                }
                                $querySelectDistributeur->closeCursor();
                                ?>
                            </select>
                        </li>
                        <li>
                            <label for="limit">Films par page</label>
                            <input type="number" name="limit" id="limit" value="<?php echo $_GET["limit"]; ?>" />
                        </li>
                        <li>
                            <input type="submit" class="submit" value="Envoyer" />
                        </li>
                    </ul>
                </form>
            </div>
            <div id="resultats">
                <h2>Résulats</h2>
                <table>
                    <tr>
                        <th>Titre</th>
                        <th>Genre</th>
                        <th>Distributeur</th>
                        <th>Annee</th>
                        <th>Détails</th>
                    </tr>
                    <?php
                    if (!empty($_GET["titre"]) || !empty($_GET["genre"]) || !empty($_GET["distributeur"]))
                    {
                        $where = [];
                        if(!empty($_GET["titre"]))
                        {
                            array_push($where, "tp_film.titre LIKE \"%" . $_GET["titre"] . "%\"");
                        }
                        if(!empty($_GET["genre"]))
                        {
                            array_push($where, "tp_genre.id_genre = " . $_GET["genre"]);
                        }
                        if(!empty($_GET["distributeur"]))
                        {
                            array_push($where, "tp_distrib.id_distrib = " . $_GET["distributeur"]);
                        }
                        $where =  "WHERE " . implode($where, " AND ");
                    }
                    else
                    {
                        $where = "";
                    }

                    $querySelectFilms = $database->prepare(" SELECT tp_film.id_film AS id_film, tp_film.titre AS titre, tp_genre.nom AS genre, tp_distrib.nom AS distrib, tp_film.annee_prod AS annee_prod
                        FROM tp_film
                        LEFT JOIN tp_genre
                        ON tp_film.id_genre = tp_genre.id_genre
                        LEFT JOIN tp_distrib
                        ON tp_film.id_distrib = tp_distrib.id_distrib
                        $where
                        ORDER BY tp_film.titre
                        LIMIT " . $start . ", " . $_GET["limit"] ."");
                    $querySelectFilms->execute();

                    if (empty($data = $querySelectFilms->fetch()))
                    {
                        echo "<tr><td colspan=5>Aucun résultat !</td></tr>";
                    }
                    else
                    {
                        $querySelectFilms->closeCursor();
                        $querySelectFilms->execute();
                    }

                    while ($data = $querySelectFilms->fetch())
                    {
                        echo "<tr><td>". $data["titre"] ."</td>";
                        if (!empty($data["genre"]))
                        {
                            echo "<td>" . $data["genre"]. "</td>";
                        }
                        else
                        {
                            echo "<td>inconnu</td>";
                        }
                        if (!empty($data["distrib"]))
                        {
                            echo "<td>" . $data["distrib"]. "</td>";
                        }
                        else
                        {
                            echo "<td>inconnu</td>";
                        }
                        if (!empty($data["annee_prod"]))
                        {
                            echo "<td>" . $data["annee_prod"]. "</td>";
                        }
                        else
                        {
                            echo "<td>inconnu</td>";
                        }
                        echo "<td><a href=detailsFilm.php?id=" . $data["id_film"] . ">Détails</a></td></tr>";
                    }
                    $querySelectFilms->closeCursor();

                    $queryCountFilms = $database->prepare(" SELECT COUNT(tp_film.titre) AS nb_films
                        FROM tp_film
                        LEFT JOIN tp_genre
                        ON tp_film.id_genre = tp_genre.id_genre
                        LEFT JOIN tp_distrib
                        ON tp_film.id_distrib = tp_distrib.id_distrib
                        $where");
                    $queryCountFilms->execute();
                    $nb_films =$queryCountFilms->fetch();
                    $queryCountFilms->closeCursor();
                    ?>
                    <div id="liens">
                        <?php
                        if ($start > 0)
                        {
                            ?>
                            <a href="films.php?titre=<?php echo $_GET["titre"]; ?>&genre=<?php echo $_GET["genre"]; ?>&distributeur=<?php echo $_GET["distributeur"]; ?>&limit=<?php echo $_GET["limit"]; ?>&page=<?php echo $_GET["page"] - 1; ?>" id="precedent">Précédent</a>
                            <?php
                        }
                        if (($_GET["page"] * $_GET["limit"]) < $nb_films["nb_films"])
                        {
                            ?>
                            <a href="films.php?titre=<?php echo $_GET["titre"]; ?>&genre=<?php echo $_GET["genre"]; ?>&distributeur=<?php echo $_GET["distributeur"]; ?>&limit=<?php echo $_GET["limit"]; ?>&page=<?php echo $_GET["page"] + 1; ?>" id="suivant">Suivant</a>
                            <?php
                        }
                        ?>
                    </div>
                </table>
            </div>
        </div>
    </div>
</body>
</html>