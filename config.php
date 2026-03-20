<?php

define('HOTE_DB', 'localhost');
define('NOM_DB', 'actualites_db');
define('USER_DB', 'root');
define('PASS_DB', '');

function matos_connexion() {
    // Un petit singleton maison pour la DB
    static $base_donnees = null;
    
    if ($base_donnees === null) {
        $chaine = "mysql:host=".HOTE_DB.";dbname=".NOM_DB.";charset=utf8mb4";
        
        try {
            // On configure PDO pour qu'il cause un peu en cas de pépin
            $base_donnees = new PDO($chaine, USER_DB, PASS_DB, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            // Si ça crash, on arrete tout
            exit("La base de données fait la gueule : " . $e->getMessage());
        }
    }
    return $base_donnees;
}

// Pour la pagination, on garde 5 articles par défaut
define('NB_MAX_ARTICLES', 5);
