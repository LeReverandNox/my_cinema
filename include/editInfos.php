<?php
include "database.php";

if (empty($_POST["id_perso"]))
{
    header('Location: ../detailsMembre.php?id=' . $_POST["id"]);

}
elseif (empty($_POST["id"]))
{
    header('Location: ../detailsMembre.php');
}
else
{
    $queryModInfos = $database->prepare("UPDATE tp_fiche_personne
        SET nom = \"" . $_POST["nom"] . "\", prenom = \"" . $_POST["prenom"] . "\", email = \"" . $_POST["email"]. "\", adresse = \"" . $_POST["adresse"] . "\", cpostal = \"" . $_POST["cpostal"]. "\", ville = \"" . $_POST["ville"] . "\", pays = \"" . $_POST["pays"] . "\"
        WHERE id_perso = " . $_POST["id_perso"]);
    $queryModInfos->execute();

    header('Location: ../detailsMembre.php?id=' . $_POST["id"]);
}
?>