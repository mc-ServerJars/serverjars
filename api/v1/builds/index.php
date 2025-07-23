<?php
header('Content-Type: application/json');
require '../../db.php';

try {
    $db = Database::getInstance();
    $params = $_GET;

    // Build query based on parameters
    $query = "SELECT * FROM minecraft_versions";
    $conditions = [];
    $bindParams = [];
    $types = '';

    $validFilters = [
        'game_version' => 'game_version',
        'software_version' => 'software_version',
        'build_number' => 'build_number',
        'id' => 'id',
        'fork' => 'fork',
        'name' => 'name'
    ];

    foreach ($validFilters as $param => $column) {
        if (isset($params[$param])) {
            $conditions[] = "$column = ?";
            $bindParams[] = $params[$param];
            $types .= 's';
        }
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $db->query($query, $bindParams);
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (empty($results)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'status' => 404,
            'message' => 'No builds found matching your criteria'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'status' => 200,
        'count' => count($results),
        'builds' => $results
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