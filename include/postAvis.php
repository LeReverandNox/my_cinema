<?php
include "database.php";

if (empty($_POST["id_film"]))
{
    header('Location: ../detailsMembre.php?id=' . $_POST["id"]);

}
elseif (empty($_POST["id"]))
{
    header('Location: ../detailsMembre.php');
}
else
{
    $queryAddAvis = $database->prepare("UPDATE tp_historique_membre
        SET avis = \"". htmlspecialchars($_POST["avis"]) . "\"
        WHERE id_membre = " . $_POST["id"] . " AND id_film = ". $_POST["id_film"]);
    $queryAddAvis->execute();
    header('Location: ../detailsMembre.php?id=' . $_POST["id"]);
}
?>