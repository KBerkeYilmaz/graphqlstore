<?php

use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\ProductType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'categories' => [
            'type' => Type::listOf(CategoryType::getInstance()),
            'resolve' => function ($rootValue, $args, $context, $info) {
                $categoryModel = $context['categoryModel'];
                return $categoryModel->getAllCategories();
            },
        ],
        'products' => [
            'type' => Type::listOf(ProductType::getInstance()),
            'resolve' => function ($rootValue, $args, $context, $info) {
                $productModel = $context['productModel'];
                return $productModel->getAllProducts();
            },
        ],
        'product' => [
            'type' => ProductType::getInstance(),
            'args' => [
                'id' => ['type' => Type::string()],
            ],
            'resolve' => function ($rootValue, array $args, $context, $info) {
                $productModel = $context['productModel'];
                return $productModel->getProductById($args['id']);
            },
        ],
    ],
]);

$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        'echo' => [
            'type' => Type::string(),
            'args' => [
                'message' => ['type' => Type::string()],
            ],
            'resolve' => function ($rootValue, array $args): string {
                return $rootValue['prefix'] . $args['message'];
            },
        ],
        'sum' => [
            'type' => Type::int(),
            'args' => [
                'x' => ['type' => Type::int()],
                'y' => ['type' => Type::int()],
            ],
            'resolve' => function ($calc, array $args): int {
                return $args['x'] + $args['y'];
            },
        ],
    ],
]);

$schema = new Schema(
    (new SchemaConfig())
    ->setQuery($queryType)
    ->setMutation($mutationType)
);

return $schema;
