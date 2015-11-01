<?php
include "database.php";

if (empty($_GET["id_film"]))
{
    header('Location: ../detailsMembre.php?id=' . $_GET["id"]);

}
elseif (empty($_GET["id"]))
{
    header('Location: ../detailsMembre.php');
}
else
{
    $queryDeleteHistory = $database->prepare("DELETE FROM tp_historique_membre
            WHERE id_membre = " . $_GET["id"] . " AND id_film = " . $_GET["id_film"]);
    $queryDeleteHistory->execute();

    header('Location: ../detailsMembre.php?id=' . $_GET["id"]);
}
?>