<!DOCTYPE html>
<html>

<head>
	<title>Test - création de compte</title>
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
				<form method="post" class="login100-form validate-form p-l-55 p-r-55 p-t-178">
					<span class="login100-form-title">
					Webexcial
					</span>
					
					<div class="wrap-input100 validate-input m-b-16" data-validate="Veuillez entrer un nom d'utilisateur">
						<!-- <input class="input100" type="text" name="username" placeholder="Nom d'utilisateur"> -->
						<input class="input100" type="email" name="email" id="email" placeholder="Votre Email" required>
						<span class="focus-input100"></span>	
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate="Veuillez entrer un mot de passe">
						<!-- <input class="input100" type="password" name="pass" placeholder="Mot de passe"> -->
						<input class="input100" type="password" name="password" id="password"
							placeholder="Entrez votre mot de passe" required>
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-16" data-validate="Veuillez entrer un mot de passe">
						<!-- <input class="input100" type="password" name="pass" placeholder="Mot de passe"> -->
						<input class="input100" type="password" name="cpassword" id="cpassword"
							placeholder="Confirmez votre mot de passe" required>
						<span class="focus-input100"></span>
					</div>
					
					<div class="container-login100-form-btn">
						<input class="login100-form-btn m-b-16" type="submit" name="formsend" id="formsend" value="Ok">
					</div>

					<?php include 'includes/signin.php'; ?>

					<div class="flex-col-c p-t-125 p-b-40">
						<span class="txt1 p-b-9">
							Déjà un compte ?
						</span>

						<a href="index.php" class="txt3">
							Retour à la page de connexion
						</a>
					</div>
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
	<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<!--===============================================================================================-->
	<script src="js/main.js"></script>

	<script>
	document.querySelector('form').addEventListener('submit', function (e) {
	    var password = document.getElementById('password').value;
	    var cpassword = document.getElementById('cpassword').value;
	    var email = document.getElementById('email').value;

	    if (password !== cpassword) {
	        e.preventDefault();
	        alert("Les mots de passe ne correspondent pas.");
	    } else {
	        // Vérification si l'email existe déjà dans la base de données
	        var xhr = new XMLHttpRequest();
	        xhr.open('POST', 'includes/check_email.php', false); // Utilisation de la requête synchrone
	        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	        xhr.onreadystatechange = function () {
	            if (xhr.readyState === 4 && xhr.status === 200) {
	                if (xhr.responseText === 'exists') {
	                    e.preventDefault();
	                    alert("Un compte avec cet email existe déjà.");
	                }
	            }
	        };
	        xhr.send('email=' + encodeURIComponent(email));
	    }
	});
	</script>

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
</body>
</html>
