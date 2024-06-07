<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use App\Controller\GraphQL;

// Add CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle OPTIONS request method for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Set up routing
$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    $r->post('/graphql', [GraphQL::class, 'handle']);
    // Add a simple GET route for debugging purposes
    $r->get('/graphql', function() {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['message' => 'GraphQL endpoint is live. Please use POST requests to query.']);
    });
});

// Get request method and URI
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dispatch the route
$routeInfo = $dispatcher->dispatch($requestMethod, $requestUri);

// Route handling with debugging
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo call_user_func($handler, $vars); // Ensure the handler is called correctly
        break;
}
