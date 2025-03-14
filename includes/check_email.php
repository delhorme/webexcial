<?php
include 'database.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    
    if (empty($email)) {
        echo 'Nouveau compte créé !';
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        echo 'exists';
    } else {
        echo 'not_exists';
    }
}
?>
