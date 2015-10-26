<?php
        try {
            $database = new PDO('mysql:host=localhost;dbname=epitech_tp;charset=utf8', 'root', '#W0u1d@Y0u&K1nd1y$', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }
        catch (Exception $e) {
            Die ('Erreur : ' . $e->getMessage());
        }
 ?>