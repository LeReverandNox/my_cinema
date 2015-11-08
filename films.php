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
                    if (!isset($_GET["page"]) || $_GET["page"] < 1)
                    {
                        $_GET["page"] = 1;
                    }
                    if (!isset($_GET["limit"]) || $_GET["limit"]  < 1)
                    {
                        $_GET["limit"] = 20;
                    }
                    if (!isset($_GET["titre"]))
                    {
                        $_GET["titre"] = "";
                    }
                    if (!isset($_GET["genre"]))
                    {
                        $_GET["genre"] = "";
                    }
                    if (!isset($_GET["distributeur"]))
                    {
                        $_GET["distributeur"] = "";
                    }

                    $start = ($_GET["page"] * $_GET["limit"]) - $_GET["limit"];
                    ?>

                    <ul>
                        <li>
                            <label for="titre">Titre :</label>
                            <input type="text" name="titre" id="titre" value="<?php echo $_GET["titre"]; ?>"/>
                        </li>
                        <li>
                            <label for="genre">Genre :</label>
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
                            <label for="distributeur">Distributeur :</label>
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
                    if ($_GET["titre"] != "" || $_GET["genre"] != "" || $_GET["distributeur"] != "")
                    {
                        $where = [];
                        if($_GET["titre"] != "")
                        {
                            array_push($where, "tp_film.titre LIKE \"%" . htmlspecialchars($_GET["titre"]) . "%\"");
                        }
                        if($_GET["genre"] != "")
                        {
                            array_push($where, "tp_genre.id_genre = " . (int)htmlspecialchars($_GET["genre"]));
                        }
                        if($_GET["distributeur"] != "")
                        {
                            array_push($where, "tp_distrib.id_distrib = " . (int)htmlspecialchars($_GET["distributeur"]));
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
                        LIMIT " . abs($start) . ", " . abs($_GET["limit"]) ."");
                    $querySelectFilms->execute();

                    if ($querySelectFilms->rowcount() == 0)
                    {
                        ?>
                        <tr>
                            <td colspan="5">Aucun film ne correspond à cette recherche !</td>
                        </tr>
                        <?php
                    }

                    while ($data = $querySelectFilms->fetch())
                    {
                        ?>
                        <tr>
                            <td><?php echo $data["titre"]; ?></td>
                            <?php
                            if (!empty($data["genre"]))
                            {
                                ?>
                                <td><?php echo $data["genre"]; ?></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td>inconnu</td>
                                <?php
                            }
                            if (!empty($data["distrib"]))
                            {
                                ?>
                                <td><?php  echo $data["distrib"]; ?></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td>inconnu</td>
                                <?php
                            }
                            if (!empty($data["annee_prod"]))
                            {
                                ?>
                                <td><?php echo $data["annee_prod"] ;?></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td>inconnu</td>
                                <?php
                            }
                            ?>
                            <td><a href="detailsFilm.php?id=<?php echo $data["id_film"] ;?>">Détails</a></td>
                        </tr>
                        <?php
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

                    $nb_pages = ceil($nb_films["nb_films"] / $_GET["limit"]);
                    ?>
                </table>
                <div id="liens">
                    <?php
                    if ($nb_pages > 1)
                    {
                        ?>
                        <form action="films.php" method="GET" class="center">
                        <ul>
                            <li>
                            <label for="select_page">Page : </label>
                                <select name="page" id="select_page">
                                <?php
                                for ($i=1; $i <= $nb_pages; $i++)
                                {
                                ?>
                                <option value="<?php echo $i; ?>" <?php if ($i == $_GET["page"]) { echo "selected"; } ?>><?php echo "$i sur $nb_pages"; ?></option>
                                <?php
                                }
                                ?>
                                </select>
                            </li>
                            <li>
                                <input type="hidden" name="titre" value="<?php echo $_GET["titre"]; ?>">
                                <input type="hidden" name="genre" value="<?php echo $_GET["genre"]; ?>">
                                <input type="hidden" name="distributeur" value="<?php echo $_GET["distributeur"]; ?>">
                                <input type="hidden" name="limit" value="<?php echo $_GET["limit"]; ?>">
                                <input type="submit" value="Aller" />
                            </li>
                        </ul>
                        </form>
                        <?php
                    }
                    if ($start > 0)
                    {
                        ?>
                        <a href="films.php?titre=<?php echo $_GET["titre"]; ?>&amp;genre=<?php echo $_GET["genre"]; ?>&amp;distributeur=<?php echo $_GET["distributeur"]; ?>&amp;limit=<?php echo $_GET["limit"]; ?>&amp;page=<?php echo $_GET["page"] - 1; ?>" id="precedent">Précédent</a>
                        <?php
                    }
                    if (($_GET["page"] * $_GET["limit"]) < $nb_films["nb_films"])
                    {
                        ?>
                        <a href="films.php?titre=<?php echo $_GET["titre"]; ?>&amp;genre=<?php echo $_GET["genre"]; ?>&amp;distributeur=<?php echo $_GET["distributeur"]; ?>&amp;limit=<?php echo $_GET["limit"]; ?>&amp;page=<?php echo $_GET["page"] + 1; ?>" id="suivant">Suivant</a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>