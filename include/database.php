<?php
        try {
            $database = new PDO('mysql:host=localhost;dbname=epitech_tp;charset=utf8', 'pangolin', 'password');
        }
        catch (Exception $e) {
            Die ('Erreur : ' . $e->getMessage());
        }
 ?>