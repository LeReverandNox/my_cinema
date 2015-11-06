<?php include "include/database.php" ?>

<!DOCTYPE html>
<html>
<head>
    <title>Abonnements / Réductions</title>
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    <meta charset="UTF-8" />
</head>
<body>
    <header>
        <h1>Abonnements / Réductions</h1>
        <?php include "template/nav.html"; ?>
    </header>
    <div class="main_wrapper">

    <h2>Abonnements</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Prix</th>
            <th>Durée</th>
            <th>Résumé</th>
        </tr>
        <?php
                $querySelectAbonnement = $database->query("SELECT * FROM  tp_abonnement ORDER BY nom");
                while ($data = $querySelectAbonnement->fetch())
                {
                ?>
                    <tr>
                        <td><?php echo $data["nom"]; ?></td>
                        <td><?php echo $data["prix"]; ?>€</td>
                        <td><?php echo $data["duree_abo"]; ?> jours</td>
                        <td><?php echo $data["resum"]; ?></td>
                    </tr>
                <?php
                }
         ?>
    </table>

    <h2>Réductions</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Reduction</th>
        </tr>
        <?php
                $querySelectReductions = $database->query("SELECT * FROM  tp_reduction ORDER BY nom");
                while ($data = $querySelectReductions->fetch())
                {
                ?>
                    <tr>
                        <td><?php echo $data["nom"]?></td>
                        <td><?php echo $data["pourcentage_reduc"]?>%</td>
                    </tr>
                <?php
                }
         ?>
    </table>
    </div>
</body>
</html>