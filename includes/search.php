<?php
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
    $specialite = isset($_GET["specialite"]) ? trim($_GET["specialite"]) : "";
    $min_price = isset($_GET["min_price"]) ? floatval($_GET["min_price"]) : 0;
    $max_price = isset($_GET["max_price"]) ? floatval($_GET["max_price"]) : PHP_FLOAT_MAX;

    $sql = "SELECT p.*, a.nom as artisan_nom, a.prenom as artisan_prenom, a.specialite 
            FROM produits p 
            JOIN artisans a ON p.artisan_id = a.id 
            WHERE 1=1";

    $params = array();
    $types = "";

    if (!empty($search)) {
        $sql .= " AND (p.nom LIKE ? OR p.description LIKE ?)";
        $search_param = "%" . $search . "%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ss";
    }

    if (!empty($specialite)) {
        $sql .= " AND a.specialite = ?";
        $params[] = $specialite;
        $types .= "s";
    }

    $sql .= " AND p.prix BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;
    $types .= "dd";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $products = array();
            
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
            
            echo json_encode($products);
        } else {
            echo json_encode(array("error" => "Une erreur est survenue lors de la recherche."));
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>