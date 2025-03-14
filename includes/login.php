<?php

ini_set('display_errors', 0); // Désactive l'affichage des erreurs en production
ini_set('log_errors', 1); // Active l'enregistrement des erreurs dans le fichier de log
error_reporting(E_ALL);

// Configurez les paramètres de session pour plus de sécurité
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']), // Active uniquement en HTTPS
    'samesite' => 'Strict'
]);

// Démarrer la session si elle n'est pas encore active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si le formulaire a été soumis
if (isset($_POST['formsend'])) {
    // Récupérer et désinfecter les données de formulaire
    $email = filter_var(trim($_POST['lemail']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['lpassword']);

    // Vérifiez que les champs ne sont pas vides
    if (!empty($email) && !empty($password)) {
        // Valider le format de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Identifiants incorrects.";
            exit();
        }

        // Inclure la connexion à la base de données
        require 'includes/database.php';

        try {
            // Préparez une requête pour vérifier les informations d'identification de l'utilisateur
            $query = $db->prepare("SELECT * FROM users WHERE email = :email");
            $query->execute(['email' => $email]);
            $user = $query->fetch();

            // Vérifiez si l'utilisateur existe et vérifiez le mot de passe
            if ($user) {
                // Vérification du mot de passe
                if (password_verify($password, $user['password'])) {
                    // Debug: Afficher un message si le mot de passe est correct
                    error_log("Mot de passe correct pour l'utilisateur: " . $email);

                    // Authentification réussie
                    $_SESSION['user_id'] = $user['id']; // Stocker l'ID utilisateur en session

                    // Effacer tout le contenu précédemment envoyé pour éviter "les en-têtes déjà envoyés"
                    ob_clean(); 

                    // Redirection vers la page sécurisée
                    header("Location: page1.php");
                    exit();
                } else {
                    // Debug: Afficher un message si le mot de passe est incorrect
                    error_log("Mot de passe incorrect pour l'utilisateur: " . $email);

                    // Mot de passe incorrect
                    echo "Identifiants incorrects."; // Message générique pour éviter des informations spécifiques
                }
            } else {
                // Debug: Afficher un message si l'utilisateur n'existe pas
                error_log("Utilisateur non trouvé: " . $email);

                // L'utilisateur n'existe pas
                echo "Identifiants incorrects."; // Message générique pour éviter des informations spécifiques
            }
        } catch (PDOException $e) {
            // Enregistrez l'erreur plutôt que de l'afficher directement
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            echo "Une erreur est survenue. Veuillez réessayer plus tard.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>
