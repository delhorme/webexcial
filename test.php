<?php
// Définir les paramètres de connexion
define('HOST', '127.0.0.1:3306'); // Nom du serveur (localhost avec port 3306)
define('DB_NAME', 'u789471193_site'); // Nom de la base de données
define('USER', 'u789471193_admin'); // Identifiant MySQL
define('PASS', '12756428Ld38!'); // Mot de passe MySQL


try {
    $db = new PDO('mysql:host=' . HOST . ';dbname=' . DB_NAME, USER, PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

?>
