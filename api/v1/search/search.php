<?php
header('Content-Type: application/json');
require '../../db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'status' => 400,
        'message' => 'Missing build ID parameter'
    ]);
    exit;
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'status' => 400,
        'message' => 'Invalid build ID format'
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    $stmt = $db->query(
        "SELECT * FROM minecraft_versions WHERE id = ?",
        [$id]
    );
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'status' => 404,
            'message' => 'Build not found'
        ]);
        exit;
    }
    
    $build = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'status' => 200,
        'build' => $build
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'status' => 500,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>