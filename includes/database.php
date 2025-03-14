<?php

define('HOST', '127.0.0.1:3306');
define('DB_NAME', 'u789471193_site');
define('USER', 'u789471193_admin');
define('PASS', '12756428Ld38!');

try {
    // Connexion avec gestion des erreurs en mode exception
    $db = new PDO('mysql:host=' . HOST . ';dbname=' . DB_NAME, USER, PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activer le mode exception
    $db->exec("SET NAMES 'utf8mb4'");
    //echo "Connexion réussie à la base de données.<br>";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
