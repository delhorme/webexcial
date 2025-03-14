<!DOCTYPE html>
<html>

<head>
	<title>WebExcial</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="images/icons/favicon.ico" />
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<!--===============================================================================================-->
</head>

<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form p-l-55 p-r-55 p-t-178" method="POST">
									
					<span class="login100-form-title">
						Configurateur Garde corps<br>service Villa
						<br>
						<br>
						<img src="images/horizal.png"  style="width:200px;height:auto;">
					</span>
					<div style="height: 120px;"></div> 	<!--espace entre le titre et le formulaire-->
					<div class="wrap-input100 validate-input m-b-16" data-validate="Please enter username">
						<input class="input100" type="email" name="lemail" id="lemail" placeholder="Votre email" required>
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate="Please enter password">
						<input class="input100" type="password" name="lpassword" id="lpassword" placeholder="Votre mot de passe" required>
						<span class="focus-input100"></span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit" name="formsend">Se connecter</button>
					</div>

					<?php include 'includes/login.php'; ?>

					<div class="text-right p-t-13 p-b-23">
						<span class="txt1">
							Vous avez oublié : 
						</span>

						<a href="creationcompte.php" class="txt2">
							Nom d'utilisateur / Mot de passe ?
						</a>
					</div>

					<div class="flex-col-c p-t-170 p-b-40">
						<span class="txt1 p-b-9">
							Vous n'avez pas de compte ?
						</span>

						<a href="creationcompte.php" class="txt3">
							S'inscrire maintenant
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<!--===============================================================================================-->
	<script src="js/main.js"></script>

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


</body>

</html>