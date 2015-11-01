<?php
include "database.php";

if (!isset($_POST["id_abonnement"]))
{
    header('Location: ../detailsMembre.php?id=' . $_POST["id"]);
}
elseif (empty($_POST["id"]))
{
    header('Location: ../detailsMembre.php');
}
else
{
    $queryModAbo = $database->prepare("UPDATE tp_membre
        SET id_abo = " . $_POST["id_abonnement"] . "
        WHERE id_membre = " . $_POST["id"]);
    $queryModAbo->execute();

    header('Location: ../detailsMembre.php?id=' . $_POST["id"]);
}
?>