<?php
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT p.*, a.nom as artisan_nom, a.prenom as artisan_prenom, a.specialite 
            FROM produits p 
            JOIN artisans a ON p.artisan_id = a.id 
            ORDER BY p.date_creation DESC";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $products = array();
            
            while ($row = mysqli_fetch_assoc($result)) {
                // Ajout des informations supplémentaires pour la galerie
                $row['image_url'] = !empty($row['image_url']) ? $row['image_url'] : 'assets/images/default-product.jpg';
                $row['artisan_name'] = $row['artisan_prenom'] . ' ' . $row['artisan_nom'];
                $row['formatted_price'] = number_format($row['prix'], 2, ',', ' ') . ' €';
                
                $products[] = $row;
            }
            
            echo json_encode($products);
        } else {
            echo json_encode(array("error" => "Une erreur est survenue lors de la récupération des produits."));
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>