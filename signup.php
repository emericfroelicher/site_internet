<?php
require 'config.php';                 // on récupère $pdo

/* 1. Collecte + validation basique */
$username = trim($_POST['username'] ?? '');
$phoneRaw = $_POST['phone'] ?? '';
$phone    = preg_replace('/\D+/', '', $phoneRaw); // ne garde que les chiffres
$email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$pass     = $_POST['password'] ?? '';

if (!$username || !$phone || !$email || strlen($pass) < 6) {
  exit('Merci de remplir tous les champs correctement.');
}

/* 2. Hachage du mot de passe */
$hash = password_hash($pass, PASSWORD_BCRYPT);

/* 3. Insertion sécurisée */
$sql  = "INSERT INTO users (username, phone, email, password_hash)
         VALUES (:u, :p, :e, :h)";
$stmt = $pdo->prepare($sql);

try {
  $stmt->execute([
    ':u' => $username,
    ':p' => $phone,
    ':e' => $email,
    ':h' => $hash
  ]);
  header('Location: login.html?signup=ok');   // redirige vers la page connexion
  exit;
} catch (PDOException $e) {
  // 1062 = doublon UNIQUE (email ou téléphone)
  if ($e->errorInfo[1] == 1062) {
    exit('E-mail ou téléphone déjà utilisé.');
  }
  exit('Erreur : '.$e->getMessage());
}
?>
