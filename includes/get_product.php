<?php
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $query = "SELECT * FROM produits WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($product = mysqli_fetch_assoc($result)) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Produit non trouvé']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'ID du produit non spécifié']);
}
?> 