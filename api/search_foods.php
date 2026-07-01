<?php
/**
 * API Endpoint: Search Filipino Foods
 * Returns JSON results for autocomplete
 */

header('Content-Type: application/json');

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'BNS Staff') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/FilipinoFoodModel.php';

$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? null;

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = getDBConnection();
    $foodModel = new FilipinoFoodModel($db);
    
    $results = $foodModel->searchFoods($query, $category);
    
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
