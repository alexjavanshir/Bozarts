<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);

    if (empty($email) || empty($mot_de_passe)) {
        $error = "Veuillez entrer votre email et mot de passe.";
    } else {
        $sql = "SELECT id, nom, prenom, mot_de_passe FROM artisans WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $nom, $prenom, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($mot_de_passe, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nom"] = $nom;
                            $_SESSION["prenom"] = $prenom;
                            header("location: ../profil.html");
                            exit();
                        } else {
                            $error = "Mot de passe incorrect.";
                        }
                    }
                } else {
                    $error = "Aucun compte trouvé avec cet email.";
                }
            } else {
                $error = "Une erreur est survenue. Veuillez réessayer.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>