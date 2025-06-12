<?php
session_start();
require 'config.php';

/* 1. Récupération des champs */
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$pass  = $_POST['password'] ?? '';

if (!$email || !$pass) {
  exit('Merci de remplir tous les champs.');
}

/* 2. Cherche l’utilisateur par e-mail */
$sql  = "SELECT id, password_hash FROM users WHERE email = :mail";
$stmt = $pdo->prepare($sql);
$stmt->execute([':mail' => $email]);
$user = $stmt->fetch();

/* 3. Vérification du mot de passe */
if ($user && password_verify($pass, $user['password_hash'])) {
  $_SESSION['user_id'] = $user['id'];      // on stocke l’ID en session
  header('Location: tableau_de_bord.php'); // page protégée à créer
  exit;
}

exit('E-mail ou mot de passe incorrect.');
?>
