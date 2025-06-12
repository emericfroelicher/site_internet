<?php
session_start();        // ferme la session existante
session_destroy();       // vide et supprime les données

// Redirige vers la page d’accueil (index.php à la racine)
header('Location: /index.php');   // ou simplement '/' si index.php est la page par défaut
exit;
