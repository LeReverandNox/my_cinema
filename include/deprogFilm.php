<?php
include "database.php";

if (!isset($_GET["id_film"]) || !isset($_GET["id_salle"]) || empty($_GET["debut"]))
{
    header('Location: ../programmation.php');
}
else
{
    $queryDeprogFilm = $database->prepare("DELETE FROM tp_grille_programme
            WHERE id_film = " . $_GET["id_film"] . " AND id_salle = " . $_GET["id_salle"] . " AND debut_sceance = \"" . $_GET["debut"] . "\"");
    $queryDeprogFilm->execute();

    header('Location: ../programmation.php');
}
?>