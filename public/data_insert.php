<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $db = (new Database())->getConnection();
    
    $categories = [
        ["name" => "all"],
        ["name" => "clothes"],
        ["name" => "tech"]
    ];
    
    $products = [
        [
            "id" => "huarache-x-stussy-le",
            "name" => "Nike Air Huarache Le",
            "inStock" => true,
            "description" => "<p>Great sneakers for everyday use!</p>",
            "category" => "clothes",
            "brand" => "Nike x Stussy"
        ],
        // Add more products as needed...
    ];
    
    // Insert categories
    $categoryStmt = $db->prepare("INSERT INTO categories (name) VALUES (:name)");
    foreach ($categories as $category) {
        $categoryStmt->execute([':name' => $category['name']]);
    }
    
    // Get the category IDs
    $categoryStmt = $db->prepare("SELECT id, name FROM categories");
    $categoryStmt->execute();
    $categoryMap = $categoryStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Insert products
    $productStmt = $db->prepare("INSERT INTO products (id, name, inStock, description, category_id, brand) VALUES (:id, :name, :inStock, :description, :category_id, :brand)");
    foreach ($products as $product) {
        $product['category_id'] = $categoryMap[$product['category']];
        $productStmt->execute($product);
    }
    
    echo "Data inserted successfully!";
} catch (Exception $e) {
    echo "Data insertion failed: " . $e->getMessage();
}
