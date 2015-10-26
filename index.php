<?php include "include/database.php" ?>
<!DOCTYPE html>
<html>
<head>
    <title>My Cinema Manager v1.0</title>
    <link rel="stylesheet" type="text/css" href="style/style.css" />
</head>
<body>
    <header>Mange mon interface 2.0</h1>
    </header>
    <section>
        <article>
                <form action="index.php" method="POST">
                <p>Recherche un film : </p>
                <label for "titre">Titre :</label>
                <input type="text" name="titre" id="titre" />
                <label for "genre">Genre :</label>
                <select name="genre" id="genre">
                    <option value="none">Tout</option>
                    <?php
                        $querySelectGenre = $database->query("SELECT * FROM  tp_genre ORDER BY nom");
                        while ($data = $querySelectGenre->fetch())
                        {
                            echo '<option value="'. $data["id_genre"] . '">' . ucfirst($data["nom"]) . '</option>' . "\n";
                        }
                    ?>
                </select>
                <label for "distributeur">Distributeur :</label>
                <select name="distributeur" id="distributeur">
                    <option value="none">Tout</option>
                    <?php
                        $querySelectDistributeur = $database->query("SELECT * FROM  tp_distrib ORDER BY nom");
                        while ($data = $querySelectDistributeur->fetch())
                        {
                            echo '<option value="'. $data["id_distrib"] . '">' . ucfirst($data["nom"]) . '</option>' . "\n";
                        }
                    ?>
                </select>
                <input type="submit" value="Envoyer" />
            </form>
        </article>
        <?php
        if (!empty($_POST))
        {
            if (!empty($_POST["titre"]))
            {
                if ($_POST["genre"] !== "none" && $_POST["distributeur"] !== "none")
                {
                    $querySelectFilm = $database->prepare("SELECT titre FROM  tp_film WHERE titre LIKE :POSTtitre AND id_genre = :POSTgenre AND id_distrib = :POSTdistributeur");
                    $querySelectFilm->execute(["POSTtitre" => '%' . htmlspecialchars($_POST["titre"]) . "%",
                                                                "POSTgenre" => htmlspecialchars((int)$_POST["genre"]),
                                                                "POSTdistributeur" => htmlspecialchars((int)$_POST["distributeur"])
                                                                ]);
                }
                elseif ($_POST["genre"] !== "none")
                {
                    $querySelectFilm = $database->prepare("SELECT titre FROM  tp_film WHERE titre LIKE :POSTtitre AND id_genre = :POSTgenre");
                    $querySelectFilm->execute(["POSTtitre" => '%' . htmlspecialchars($_POST["titre"]) . "%",
                                                                "POSTgenre" => htmlspecialchars((int)$_POST["genre"])
                                                                ]);
                }
                elseif ($_POST["distributeur"] !== "none")
                {
                    $querySelectFilm = $database->prepare("SELECT titre FROM  tp_film WHERE titre LIKE :POSTtitre AND id_distrib = :POSTdistributeur");
                    $querySelectFilm->execute(["POSTtitre" => '%' . htmlspecialchars($_POST["titre"]) . "%",
                                                                "POSTdistributeur" => htmlspecialchars((int)$_POST["distributeur"])
                                                                ]);
                }
                else
                {
                    $querySelectFilm = $database->prepare("SELECT titre FROM  tp_film WHERE titre LIKE :POSTtitre");
                    $querySelectFilm->execute(["POSTtitre" => '%' . htmlspecialchars($_POST["titre"]) . "%"]);
                }

                while ($data = $querySelectFilm->fetch())
                {
                    echo $data["titre"] . "<br />";
                }
            }
            else
            {
                echo "Veuillez entrer un nom de film à rechercher";
            }
        }
        ?>
    </section>
</body>
</html>