<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class ProductType extends ObjectType
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ProductType();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $config = [
            'name' => 'Product',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
                'inStock' => Type::boolean(),
                'description' => Type::string(),
                'category' => Type::string(),
                'brand' => Type::string(),
            ]
        ];
        parent::__construct($config);
    }
}
