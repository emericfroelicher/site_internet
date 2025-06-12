<?php
/* --------------- À personnaliser --------------- */
$host = 'localhost';          // laisse « localhost » sur Hostinger
$db   = '‹trinidad_db›';      // nom de la base créée dans le hPanel
$user = '‹trinidad_user›';    // utilisateur MySQL
$pass = '‹motDePasse›';       // mot de passe MySQL
/* ----------------------------------------------- */

$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];

$pdo = new PDO($dsn, $user, $pass, $options);
?>
