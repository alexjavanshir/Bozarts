<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialisation de la session
session_start();

require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : null;
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : null;

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else{
        echo "Email: $email <br>";
        echo "Mot de passe: $password <br>";
    }

}
?>
