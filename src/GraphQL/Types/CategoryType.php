<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class CategoryType extends ObjectType {
    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new CategoryType();
        }
        return self::$instance;
    }

    public function __construct() {
        $config = [
            'name' => 'Category',
            'fields' => [
                'id' => Type::nonNull(Type::int()),
                'name' => Type::string(),
            ]
        ];
        parent::__construct($config);
    }
}
