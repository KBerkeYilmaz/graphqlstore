<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

// Add CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle OPTIONS request method
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
$db = (new Database())->getConnection();

// Fetch product by id
$product_id = 'huarache-x-stussy-le'; // Change this to the id of the product you added

try {
    $query = "SELECT * FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($product);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
