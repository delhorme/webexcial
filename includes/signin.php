<?php
if (isset($_POST['formsend'])) {
    extract($_POST);

    // Vérification si tous les champs sont remplis
    if (!empty($password) && !empty($cpassword) && !empty($email)) {

        // Vérification si les mots de passe correspondent
        if ($password !== $cpassword) {
            echo "Les mots de passe ne correspondent pas.";
        } else {
            $options = [
                'cost' => 12,
            ];

            // Hashage du mot de passe
            $hashpass = password_hash($password, PASSWORD_BCRYPT, $options);

            // Inclure la connexion à la base de données
            include 'includes/database.php';

            // Vérifier si l'email est valide
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "L'email n'est pas valide.";
            } else {
                try {
                    // Vérifier si l'email existe déjà dans la base de données
                    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
                    $stmt->execute(['email' => $email]);
                    $user = $stmt->fetch();

                    if ($user !== false) {
                        echo "Un compte avec cet email existe déjà.";
                    } else {
                        // Insertion de l'utilisateur dans la base de données
                        $q = $db->prepare("INSERT INTO users (email, password) VALUES(:email, :password)");

                        // Exécution de la requête
                        $q->execute([
                            'email' => $email,
                            'password' => $hashpass
                        ]);
						// Effacer tout le contenu précédemment envoyé pour éviter "les en-têtes déjà envoyés"
						ob_clean(); 
						// Redirection vers page1.php
						header("Location: page1.php");
						exit();

                    }

                } catch (PDOException $e) {
                    // Affichage des erreurs en cas d'échec de la requête
                    echo "Erreur lors de l'enregistrement dans la base de données : " . $e->getMessage();
                }
            }
        }
    } else {
        echo "Les champs ne sont pas tous remplis !";
    }

}
?>