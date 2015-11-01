<?php
include "database.php";

$queryLengthFilm = $database->query("SELECT duree_min AS duree FROM tp_film WHERE id_film = " . $_POST["id_film"]);
$data = $queryLengthFilm->fetch();
$queryLengthFilm->closeCursor();

$debut_sceance = $_POST["date"] . " " . $_POST["heure"];
// $fin_seance = $

$queryProgFilm = $database->prepare("INSERT INTO tp_grille_programme (id_film, id_salle, debut_sceance, fin_sceance)
    VALUES (:id_film, :id_salle, :debut_sceance, :fin_sceance)");
$queryProgFilm->execute(["id_film" => $_POST["id_film"],
    "id_salle" => $_POST["id_salle"],
    "debut_sceance" => $debut_sceance,
    "fin_sceance" => "21-11-1993 00:00"
    ]);
header('Location: ../programmation.php');
?>
