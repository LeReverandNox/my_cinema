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
                    echo "<tr><td>" . $data["nom"] . "</td>";
                    echo "<td>" . $data["prix"]. "</td>";
                    echo "<td>" . $data["duree_abo"]. "</td>";
                    echo "<td>" . $data["resum"]. "</td>";
                    echo "</tr>";
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
                    echo "<tr><td>" . $data["nom"] . "</td>";
                    echo "<td>-" . $data["pourcentage_reduc"]. "%</td>";
                    echo "</tr>";
                }
         ?>
    </table>
    </div>
</body>
</html>