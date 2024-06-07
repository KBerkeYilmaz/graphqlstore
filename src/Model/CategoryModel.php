<?php

namespace App\Model;

use PDO;

class CategoryModel {
    private $conn;
    private $table = 'categories';

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getAllCategories() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
