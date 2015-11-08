<?php
include "include/database.php";
if (empty($_GET["id"]))
{
    header('Location: membres.php');
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
        <h1>Détails du membre</h1>
        <?php include "template/nav.html"; ?>
    </header>

    <div class="main_wrapper">
        <div class="historique">
            <div class="center">
                <?php
                if (!isset($_GET["page"]) || $_GET["page"] <= 0)
                {
                    $_GET["page"] = 1;
                }
                if (!isset($_GET["limit"]) || $_GET["limit"] <= 0)
                {
                    $_GET["limit"] = 10;
                }
                $start = ($_GET["page"] * $_GET["limit"]) - $_GET["limit"];

                $queryDisplayHistory = $database->prepare("SELECT tpf.titre AS titre, tphm.date AS date, tphm.avis AS avis, tphm.id_film AS id_film
                    FROM tp_historique_membre AS tphm
                    LEFT JOIN tp_film AS tpf
                    ON tpf.id_film = tphm.id_film
                    WHERE tphm.id_membre = " . $_GET["id"] . "
                    ORDER BY date DESC
                    LIMIT " . $start . ", " . $_GET["limit"] ."");

                $queryDisplayHistory->execute();
                ?>
                <h2>Historique</h2>
                <table>
                    <tr>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Avis</th>
                        <th>Supprimer</th>
                    </tr>

                    <?php
                    while ($data = $queryDisplayHistory->fetch())
                    {
                        ?>
                        <tr>
                            <td><?php echo $data["titre"]; ?></td>
                            <td><?php echo $data["date"]; ?></td>
                            <td><?php echo $data["avis"]; ?><a href="include/modifierAvis.php?id=<?php echo $_GET["id"]; ?>&amp;id_film=<?php echo $data["id_film"]; ?>">Editer</a></td>
                            <td><a href="include/supprimerHistorique.php?id=<?php echo $_GET["id"]; ?>&amp;id_film=<?php echo $data["id_film"]; ?>">Supprimer</a></td>
                        </tr>
                        <?php
                    }
                    $queryDisplayHistory->closeCursor();

                    $queryCountHistory = $database->prepare("SELECT COUNT(tpf.titre) AS nb_films
                        FROM tp_historique_membre AS tphm
                        LEFT JOIN tp_film AS tpf
                        ON tpf.id_film = tphm.id_film
                        WHERE tphm.id_membre = " . $_GET["id"]);
                    $queryCountHistory->execute();
                    $nb_films = $queryCountHistory->fetch();
                    $queryCountHistory->closeCursor();

                    $nb_pages = ceil($nb_films["nb_films"] / $_GET["limit"]);
                    ?>

                </table>


                <form id="limit_history" action="detailsMembre.php" method="GET">
                    <label for="limit">Films par page</label>
                    <input type="number" name="limit" id="limit" value="<?php echo $_GET["limit"]; ?>" />
                    <input type="hidden" name="id" value="<?php  echo $_GET["id"]; ?>">
                    <input type="submit" value="Envoyer" />
                </form>


                <div id="liens">
                    <?php
                    if ($nb_pages > 0)
                    {
                        ?>
                        <form action="detailsMembre.php" method="GET" class="center">
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
                                    <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
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
                        <a href="detailsMembre.php?id=<?php echo $_GET["id"]; ?>&amp;limit=<?php echo $_GET["limit"]; ?>&amp;page=<?php echo $_GET["page"] - 1; ?>" id="precedent">Précédent</a>
                        <?php
                    }
                    if (($_GET["page"] * $_GET["limit"]) < $nb_films["nb_films"])
                    {
                        ?>
                        <a href="detailsMembre.php?id=<?php echo $_GET["id"]; ?>&amp;limit=<?php echo $_GET["limit"]; ?>&amp;page=<?php echo $_GET["page"] + 1; ?>" id="suivant">Suivant</a>
                        <?php
                    }
                    $queryCountHistory->closeCursor();
                    ?>
                </div>

                <h2>Ajouter un film</h2>
                <form id="ajouterHistorique" action="include/ajouterHistorique.php" method="POST">
                    <ul>
                        <li>
                            <label for="id_film">Film :</label>
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
                            <label for="date">Date</label>
                            <input type="date" name="date" id="date" required />
                            <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>" />
                            <input type="submit" class="submit" value="Ajouter" />
                        </li>
                    </ul>
                </form>
            </div>
        </div>

        <div class="abonnements">
            <h2>Gestion des abonnements</h2>
            <form class="center" action="include/modifierAbo.php" method="POST">
                <ul>
                    <li>
                        <select name="id_abonnement">
                            <option value="null">Aucun</option>
                            <?php
                            $querySelectAbo = $database->query("SELECT nom, id_abo FROM tp_abonnement");
                            $queryCurAbo = $database->query("SELECT id_abo AS cur_abo FROM tp_membre WHERE id_membre = " . $_GET["id"]);
                            $dataCurAbo = $queryCurAbo->fetch();

                            while ($data = $querySelectAbo->fetch())
                            {
                                ?>
                                <option value="<?php echo $data["id_abo"]; ?>" <?php if($data["id_abo"] == $dataCurAbo["cur_abo"]) {echo "selected";} ?>><?php echo $data["nom"]?></option>
                                <?php
                            }
                            $querySelectAbo->closeCursor();
                            $queryCurAbo->closeCursor();
                            ?>
                        </select>
                    </li>
                    <li>
                        <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
                        <input type="submit" class="submit" value="Envoyer" />
                    </li>
                </ul>
            </form>
        </div>

        <div class="infos_perso">
            <h2>Informations personelles</h2>
            <form class="center" action="include/editInfos.php" method="POST">
                <?php
                $queryInfosPerso = $database->query("SELECT tpfp.nom AS nom, tpfp.prenom AS prenom, tpfp.email AS email, tpfp.adresse AS adresse, tpfp.cpostal AS cpostal, tpfp.ville AS ville, tpfp.pays AS pays, tpm.id_fiche_perso AS id_perso
                    FROM tp_fiche_personne AS tpfp
                    LEFT JOIN tp_membre AS tpm
                    ON tpfp.id_perso = tpm.id_fiche_perso
                    WHERE tpm.id_membre = " . $_GET["id"]);
                $data = $queryInfosPerso->fetch();
                ?>
                <ul>
                    <li>
                        <label for="nom">Nom :</label>
                        <input type="text" name="nom" id="nom" value="<?php echo $data["nom"]; ?>" />
                    </li>
                    <li>
                        <label for="prenom">Prénom : </label>
                        <input type="text" name="prenom" id="prenom" value="<?php echo $data["prenom"]; ?>" />
                    </li>
                    <li>
                        <label for="email">Email : </label>
                        <input type="text" name="email" id="email" value="<?php echo $data["email"]; ?>" />
                    </li>
                    <li>
                        <label for="adresse">Adresse : </label>
                        <input type="text" name="adresse" id="adresse" value="<?php echo $data["adresse"]; ?>" />
                    </li>
                    <li>
                        <label for="cpostal">Code Postal : </label>
                        <input type="text" pattern="[0-9]{5}" name="cpostal" id="cpostal" value="<?php echo $data["cpostal"]; ?>" />
                    </li>
                    <li>
                        <label for="ville">Ville : </label>
                        <input type="text" name="ville" id="ville" value="<?php echo $data["ville"]; ?>" />
                    </li>
                    <li>
                        <label for="pays">Pays : </label>
                        <input type="text" name="pays" id="pays" value="<?php echo $data["pays"]; ?>" />
                    </li>
                    <li>
                        <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>" />
                        <input type="hidden" name="id_perso" value="<?php echo $data["id_perso"]; ?>" />
                        <input type="submit" class="submit" value="Envoyer" />
                    </li>
                </ul>
                <?php
                $queryInfosPerso->closeCursor();
                ?>

                <a href="membres.php">Retour à la recherche</a>
            </form>
        </div>
    </div>
</body>
</html>