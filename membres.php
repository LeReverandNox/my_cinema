<?php include "include/database.php" ?>
<!DOCTYPE html>
<html>
<head>
    <title>Membres</title>
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <meta charset="UTF-8" />
</head>
<body>
    <header>
        <h1>Membres</h1>
        <?php include "template/nav.html"; ?>
    </header>

    <div class="main_wrapper">
        <div id="content">
            <div id="recherche">
                <h2>Recherche</h2>
                <form action="membres.php" method="GET">

                    <?php
                    if (empty($_GET["page"]))
                    {
                        $_GET["page"] = 1;
                    }
                    if (empty($_GET["limit"]))
                    {
                        $_GET["limit"] = 20;
                    }
                    if (empty($_GET["nom"]))
                    {
                        $_GET["nom"] = "";
                    }
                    if (empty($_GET["prenom"]))
                    {
                        $_GET["prenom"] = "";
                    }
                    if (empty($_GET["email"]))
                    {
                        $_GET["email"] = "";
                    }
                    if (empty($_GET["cpostal"]))
                    {
                        $_GET["cpostal"] = "";
                    }
                    if (empty($_GET["ville"]))
                    {
                        $_GET["ville"] = "";
                    }

                    $start = ($_GET["page"] * $_GET["limit"]) - $_GET["limit"];
                    ?>

                    <ul>
                        <li>
                            <label for "nom">Nom :</label>
                            <input type="text" name="nom" id="nom" value="<?php echo $_GET["nom"]; ?>" />
                        </li>
                        <li>
                            <label for "prenom">Prenom :</label>
                            <input type="text" name="prenom" id="prenom" value="<?php echo $_GET["prenom"]; ?>"/>
                        </li>
                        <li>
                            <label for "email">Email :</label>
                            <input type="email" name="email" id="email" value="<?php echo $_GET["email"]; ?>"/>
                        </li>
                        <li>
                            <label for "cpostal">Code Postal :</label>
                            <input type="text" name="cpostal" id="cpostal" value="<?php echo $_GET["cpostal"]; ?>"/>
                        </li>
                        <li>
                            <label for "ville">Ville :</label>
                            <input type="text" name="ville" id="ville" value="<?php echo $_GET["ville"]; ?>"/>
                        </li>
                        <li>
                            <label for="limit">Membre par page</label>
                            <input type="number" name="limit" id="limit" value="<?php echo $_GET["limit"]; ?>"/>
                        </li>
                        <li>
                            <input type="submit" id="submit" value="Envoyer" />
                        </li>
                    </ul>
                </form>
            </div>
            <div id="resultats">
                <h2>Résulats</h2>
                <table>
                    <tr>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Email</th>
                        <th>Code Postal</th>
                        <th>Ville</th>
                        <th>Pays</th>
                        <th>Gérer</th>
                    </tr>
                    <?php
                    if (!empty($_GET["nom"]) || !empty($_GET["prenom"]) || !empty($_GET["email"]) || !empty($_GET["cpostal"]) || !empty($_GET["ville"]))
                    {
                        $where = [];
                        if(!empty($_GET["nom"]))
                        {
                            array_push($where, "tfp.nom LIKE \"%" . $_GET["nom"] . "%\"");
                        }
                        if(!empty($_GET["prenom"]))
                        {
                            array_push($where, "tfp.prenom LIKE \"%" . $_GET["prenom"] . "%\"");
                        }
                        if(!empty($_GET["email"]))
                        {
                            array_push($where, "tfp.email LIKE \"%" . $_GET["email"] . "%\"");
                        }
                        if(!empty($_GET["cpostal"]))
                        {
                            array_push($where, "tfp.cpostal = " . $_GET["cpostal"]);
                        }
                        if(!empty($_GET["ville"]))
                        {
                            array_push($where, "tfp.ville LIKE \"%" . $_GET["ville"] . "%\"");
                        }
                        $where =  "WHERE " . implode($where, " AND ");
                    }
                    else
                    {
                        $where = "";
                    }

                    $querySelectMembres = $database->prepare("SELECT tfp.nom AS nom, tfp.prenom AS prenom, tfp.email AS email, tfp.cpostal AS cpostal, tfp.ville AS ville, tfp.pays AS pays, tpm.id_membre AS id_membre
                        FROM tp_fiche_personne AS tfp
                        LEFT JOIN tp_membre AS tpm
                        ON tfp.id_perso = tpm.id_fiche_perso
                        $where
                        ORDER BY tfp.nom
                        LIMIT " . $start . ", " . $_GET["limit"] ."");
                    $querySelectMembres->execute();

                    if (empty($data = $querySelectMembres->fetch()))
                    {
                        echo "<tr><td colspan=7>Aucun résultat !</td></tr>";
                    }
                    else
                    {
                        $querySelectMembres->closeCursor();
                        $querySelectMembres->execute();
                    }

                    while ($data = $querySelectMembres->fetch())
                    {
                        echo "<tr><td>". $data["nom"] ."</td>";
                        echo "<td>" . $data["prenom"]. "</td>";
                        echo "<td>" . $data["email"]. "</td>";
                        echo "<td>" . $data["cpostal"]. "</td>";
                        echo "<td>" . $data["ville"]. "</td>";
                        echo "<td>" . $data["pays"]. "</td>";

                        echo "<td><a href=detailsMembre.php?id=" . $data["id_membre"] . ">Détails</a></td></tr>";
                    }
                    $querySelectMembres->closeCursor();

                    $queryCountMembers = $database->prepare("SELECT  COUNT(tfp.nom) AS nb_membres
                        FROM tp_fiche_personne AS tfp
                        LEFT JOIN tp_membre AS tpm
                        ON tfp.id_perso = tpm.id_fiche_perso
                        $where  ");
                    $queryCountMembers->execute();
                    $nb_membres =$queryCountMembers->fetch();
                    $queryCountMembers->closeCursor();
                    ?>
                    <div id="liens">
                        <?php
                        if ($start > 0)
                        {
                            ?>
                            <a href="membres.php?nom=<?php echo $_GET["nom"]; ?>&prenom=<?php echo $_GET["prenom"]; ?>&email=<?php echo $_GET["email"]; ?>&cpostal=<?php echo $_GET["cpostal"]; ?>&ville=<?php echo $_GET["ville"]; ?>&limit=<?php echo $_GET["limit"]; ?>&page=<?php echo $_GET["page"] - 1; ?>" id="precedent">Précédent</a>
                            <?php
                        }
                        if (($_GET["page"] * $_GET["limit"]) < $nb_membres["nb_membres"])
                        {
                            ?>
                            <a href="membres.php?nom=<?php echo $_GET["nom"]; ?>&prenom=<?php echo $_GET["prenom"]; ?>&email=<?php echo $_GET["email"]; ?>&cpostal=<?php echo $_GET["cpostal"]; ?>&ville=<?php echo $_GET["ville"]; ?>&limit=<?php echo $_GET["limit"]; ?>&page=<?php echo $_GET["page"] + 1; ?>" id="suivant">Suivant</a>
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