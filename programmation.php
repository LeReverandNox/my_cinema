<?php include "include/database.php" ?>

<!DOCTYPE html>
<html>
<head>
    <title>Programmation</title>
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <meta charset="UTF-8" />
</head>
<body>
    <header>
        <h1>Programmation</h1>
        <?php include "template/nav.html"; ?>
    </header>
    <div class="main_wrapper">
        <div class="seances">
            <h2>Les séances programmées</h2>

            <form action="programmation.php" method="GET" class="center">
            <ul>
                <li>
                    <label for="id_salle">Salle : </label>
                    <select name="id_salle" id="id_salle">
                        <option value="">Toute</option>

                        <?php

                        if (!isset($_GET["id_salle"]))
                        {
                            $_GET["id_salle"] = "";
                        }
                        if (!isset($_GET["page"]) || $_GET["page"] < 1)
                        {
                            $_GET["page"] = 1;
                        }
                        if (!isset($_GET["limit"]) || $_GET["limit"] < 1)
                        {
                            $_GET["limit"] = 10;
                        }
                        $start = ($_GET["page"] * $_GET["limit"]) - $_GET["limit"];

                        $queryListSalles = $database->query("SELECT * FROM tp_salle");

                        while ($data = $queryListSalles->fetch())
                        {
                            ?>
                            <option value="<?php echo $data["id_salle"]; ?>" <?php if($_GET["id_salle"] == $data["id_salle"]) { echo "selected"; } ?>>#<?php echo $data["numero_salle"] . " - " . $data["nom_salle"]; ?></option>

                            <?php
                        }
                        $queryListSalles->closeCursor();
                        ?>
                    </select>
                </li>
                <li>
                    <label for="date">A partir du : </label>
                    <input type="date" name="date" id="date" />
                </li>
                <li>
                    <label for="heure">à</label>
                    <input type="time" name="heure" id="heure" />
                </li>
                <li>
                    <label for="limit">Films par page</label>
                    <input type="number" name="limit" id="limit" value="<?php echo $_GET["limit"]; ?>" />
                </li>
                <li>
                    <input type="submit" value="Voir" class="submit" />
                </li>
            </ul>
            </form>

            <table>
                <tr>
                    <td>Film</td>
                    <td>Jour</td>
                    <td>Heure</td>
                    <td>Salle</td>
                    <td>Places dispo.</td>
                    <td>Action</td>
                </tr>

                <?php

                if ($_GET["id_salle"] != "" || !empty($_GET["date"]) || !empty($_GET["heure"]))
                {
                    $where = [];
                    if($_GET["id_salle"] != "")
                    {
                        array_push($where, "tpgp.id_salle = " . $_GET["id_salle"] . "");
                    }
                    if (!empty($_GET["date"]) && !empty($_GET["heure"]))
                    {
                        $date = $_GET["date"] . " " . $_GET["heure"];
                        array_push($where, "tpgp.debut_sceance >= \"" . $date . "\"");
                    }
                    if (!empty($_GET["date"]) && empty($_GET["heure"]))
                    {
                        array_push($where, "tpgp.debut_sceance >= \"" . $_GET["date"] . "\"");
                    }
                    $where =  "WHERE " . implode($where, " AND ");
                }
                else
                {
                    $where = "";
                }

                $queryListProg = $database->prepare("SELECT tpf.titre AS titre, tpf.id_film AS id_film, tps.nom_salle AS salle, tps.id_salle AS id_salle, tps.nbr_siege AS places, tpgp.debut_sceance AS debut, tpgp.fin_sceance AS fin
                    FROM tp_grille_programme AS tpgp
                    LEFT JOIN tp_film AS tpf
                    ON tpgp.id_film = tpf.id_film
                    LEFT JOIN tp_salle AS tps
                    ON tpgp.id_salle = tps.id_salle
                    $where
                    ORDER BY debut DESC
                    LIMIT " . $start . ", " . $_GET["limit"] ."");

                $queryListProg->execute();

                if (empty($data = $queryListProg->fetch()))
                {
                    ?>
                        <tr>
                            <td colspan=6>Aucune programmation pour cette salle</td>
                        </tr>
                    <?php
                }
                else
                {
                    $queryListProg->closeCursor();
                    $queryListProg->execute();
                }

                while ($data = $queryListProg->fetch())
                {
                    $date = substr($data["debut"], 0, 10);
                    $heure = substr($data["debut"], 10, 6);
                    $datecomplete = str_replace(" ", "%20", $data["debut"]);
                ?>
                    <tr>
                        <td><?php echo $data["titre"] ;?></td>
                        <td><?php echo $date ;?></td>
                        <td><?php echo $heure ;?></td>
                        <td><?php echo $data["salle"] ;?></td>
                        <td><?php echo $data["places"] ;?></td>
                        <td><a href="include/deprogFilm.php?id_salle=<?php echo $data["id_salle"]; ?>&amp;id_film=<?php echo $data["id_film"]; ?>&amp;debut=<?php echo $datecomplete; ?>">Déprogrammer</a></td>
                    </tr>
                <?php
                }
                $queryListProg->closeCursor();

                $queryCountProg = $database->prepare("SELECT  COUNT(tpf.titre) AS nb_films
                    FROM tp_grille_programme AS tpgp
                    LEFT JOIN tp_film AS tpf
                    ON tpgp.id_film = tpf.id_film
                    LEFT JOIN tp_salle AS tps
                    ON tpgp.id_salle = tps.id_salle
                    $where");
                $queryCountProg->execute();
                $nb_films = $queryCountProg->fetch();
                $queryCountProg->closeCursor();

                $nb_pages = ceil($nb_films["nb_films"] / $_GET["limit"]);
                ?>
            </table>
                <div id="liens">
                <p class="center">Page <?php echo $_GET["page"]; ?> /  <?php echo $nb_pages; ?></p>
                <?php
                if ($start > 0)
                {
                ?>
                    <a href="programmation.php?id_salle=<?php echo $_GET["id_salle"]; ?>&amp;date=<?php echo $_GET["date"]; ?>&amp;heure=<?php echo $_GET["heure"]; ?>&amp;limit=<?php echo $_GET["limit"]; ?>&amp;page=<?php echo $_GET["page"] - 1;?>" id="precedent">Précédent</a>
                <?php
                }
                if (($_GET["page"] * $_GET["limit"]) < $nb_films["nb_films"])
                {
                ?>
                    <a href="programmation.php?id_salle=<?php echo $_GET["id_salle"]; ?>&amp;date=<?php echo $_GET["date"]; ?>&amp;heure=<?php echo $_GET["heure"]; ?>&amp;limit=<?php echo $_GET["limit"]; ?>&amp;page=<?php echo $_GET["page"] + 1;?>" id="suivant">Suivant</a>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="prog">
            <h2>Programmer des séances</h2>
            <form action="include/progFilm.php" method="POST" class="center">
                <ul>
                    <li>
                        <label for="id_salleProg">Salle : </label>
                        <select name="id_salle" id="id_salleProg">
                            <?php
                            $queryListSalles = $database->query("SELECT * FROM tp_salle");

                            while ($data = $queryListSalles->fetch())
                            {
                                ?>
                                <option value="<?php echo $data["id_salle"]; ?>">#<?php echo $data["numero_salle"] . " - " . $data["nom_salle"]; ?></option>

                                <?php
                            }
                            $queryListSalles->closeCursor();
                            ?>
                        </select>
                    </li>
                    <li>
                        <label for="id_film">Film : </label>
                        <select name="id_film" id="id_film">
                            <?php
                            $querySelectFilms = $database->query("SELECT titre, id_film
                                FROM tp_film
                                ORDER BY titre");
                            while ($data = $querySelectFilms->fetch())
                            {
                            ?>
                                <option value="<?php echo $data["id_film"]; ?>"><?php echo ucfirst($data["titre"]); ?></option>
                            <?php
                            }
                            $querySelectFilms->closeCursor();
                            ?>
                        </select>
                    </li>
                    <li>
                        <label for="dateProg">Jour : </label>
                        <input type="date" name="date" id="dateProg" required />
                    </li>
                    <li>
                        <label for="heureProg">Heure : </label>
                        <input type="time" name="heure" id="heureProg" required />
                    </li>
                    <li>
                        <input type="submit" value="Ajouter" class="submit" />
                    </li>
                </ul>
            </form>
        </div>
    </div>
</body>
</html>