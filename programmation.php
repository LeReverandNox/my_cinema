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
                <label for="id_salle">Salle : </label>
                <select name="id_salle" id="id_salle">
                    <option value="">Toute</option>

                    <?php

                    if (!isset($_GET["id_salle"]))
                    {
                        $_GET["id_salle"] = "";
                    }
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
                <label for="date">A partir du : </label>
                <input type="date" name="date" id="date" />
                <label for="heure">à</label>
                <input type="time" name="heure" id="heure" />
                <input type="submit" value="Voir" class="submit" />
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

                if (!empty($_GET["id_salle"]) || $_GET["id_salle"] === "0" || !empty($_GET["date"]) || !empty($_GET["heure"]))
                {
                    $where = [];
                    if(!empty($_GET["id_salle"]) || $_GET["id_salle"] === "0")
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
                    ORDER BY debut DESC");
                $queryListProg->execute(["id_salle" => $_GET["id_salle"]]);

                if (empty($data = $queryListProg->fetch()))
                {
                    echo "<tr><td colspan=6>Aucune programmation pour cette salle</td></tr>";
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

                    echo "<tr><td>" . $data["titre"]. "</td>";
                    echo "<td>" . $date . "</td>";
                    echo "<td>" . $heure . "</td>";
                    echo "<td>" . $data["salle"]. "</td>";
                    echo "<td>" . $data["places"]. "</td>";
                    echo "<td><a href=\"include/deprogFilm.php?id_salle=" . $data["id_salle"] . "&id_film=" . $data["id_film"] . "&debut=" . $data["debut"] . "\">Déprogrammer</a></td>";
                    echo "</tr>";
                }
                $queryListProg->closeCursor();
                ?>
            </table>
        </div>

        <div class="prog">
            <h2>Programmer des séances</h2>
            <form action="include/progFilm.php" method="POST" class="center">
                <ul>
                    <li>
                        <label for="id_salle">Salle : </label>
                        <select name="id_salle" id="id_salle">
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
                                echo '<option value="'. $data["id_film"] . '">' . ucfirst($data["titre"]) . '</option>' . "\n";
                            }
                            $querySelectFilms->closeCursor();
                            ?>
                        </select>
                    </li>
                    <li>
                        <label for="date">Jour : </label>
                        <input type="date" name="date" id="date" required />
                    </li>
                    <li>
                        <label for="heure">Heure : </label>
                        <input type="time" name="heure" id="heure" required />
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