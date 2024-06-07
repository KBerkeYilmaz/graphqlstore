<?php

namespace App\Model;

use PDO;

class ProductModel {
    private $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // Fetch all products
    public function getAllProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a product by ID
    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insert a new product
    public function insertProduct($id, $name, $inStock, $description, $category_id, $brand) {
        $query = "INSERT INTO products (id, name, inStock, description, category_id, brand) 
                  VALUES (:id, :name, :inStock, :description, :category_id, :brand)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':inStock', $inStock);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':brand', $brand);
        return $stmt->execute();
    }
}
