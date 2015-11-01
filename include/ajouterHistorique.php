<?php
include "database.php";

$date = $_POST["date"] . " 00:00:00";

$queryAddHistory = $database->prepare("INSERT INTO tp_historique_membre (id_membre, id_film, date)
    VALUES (" . $_POST["id"]. ", " . $_POST["id_film"]. " , \"" . $date. "\")");
$queryAddHistory->execute();

header('Location: ../detailsMembre.php?id=' . $_POST["id"]);
?>
