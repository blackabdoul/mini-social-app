<?php

// Load environment variables
require_once __DIR__ . '/../load-env.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection (reuse from parent)
require_once __DIR__ . "/../config.php";

// Get JWT_SECRET from environment variable
define('JWT_SECRET', $_ENV['JWT_SECRET']);

// Helper function to send JSON response
function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Helper function to get request body
function getRequestBody() {
    return json_decode(file_get_contents("php://input"), true);
}
?>