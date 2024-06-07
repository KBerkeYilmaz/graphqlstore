<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
use App\Database;
use PDO;

class GraphQL
{
    static public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Return a simple message for GET requests
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['message' => 'GraphQL endpoint is live. Please use POST requests to query.']);
            return;
        }

        error_log("GraphQL handle method called"); // Debug: Entry point

        try {
            // Get database connection
            $db = new Database();
            $conn = $db->getConnection();

            if (!$conn) {
                throw new RuntimeException('Failed to connect to database');
            }

            // Define the Query type
            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => Type::string(),
                    'name' => Type::string(),
                    'inStock' => Type::boolean(),
                    'description' => Type::string(),
                    'category' => Type::string(),
                    'brand' => Type::string(),
                ]
            ]);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'echo' => [
                        'type' => Type::string(),
                        'args' => [
                            'message' => ['type' => Type::string()],
                        ],
                        'resolve' => static fn($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => function () use ($conn) {
                            $stmt = $conn->query("SELECT * FROM products");
                            return $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }
                    ]
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        'resolve' => static fn($calc, array $args): int => $args['x'] + $args['y'],
                    ],
                ],
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            error_log("Input received: " . json_encode($input)); // Debug: Input data

            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
            error_log("Query result: " . json_encode($output)); // Debug: Query result

        } catch (Throwable $e) {
            error_log("GraphQL error: " . $e->getMessage()); // Debug: Error
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output);
    }
}
