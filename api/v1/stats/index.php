<?php
header('Content-Type: application/json');
require '../../db.php';

try {
    $db = Database::getInstance();
    
    // Get storage stats
    $cacheStmt = $db->query("SELECT `value` FROM `cache` WHERE `id` IN (1, 2)");
    $cacheResults = $cacheStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $bucketSize = $cacheResults[0]['value'] ?? 0;
    $objectCount = $cacheResults[1]['value'] ?? 0;
    
    // Get build stats
    $buildStmt = $db->query("SELECT COUNT(*) as total_builds FROM minecraft_versions");
    $totalBuilds = $buildStmt->get_result()->fetch_assoc()['total_builds'];
    
    // Get forks
    $forkStmt = $db->query("SELECT fork, COUNT(*) as count FROM minecraft_versions GROUP BY fork");
    $forks = $forkStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'status' => 200,
        'stats' => [
            'storage' => [
                'bucket_size' => $bucketSize,
                'object_count' => $objectCount
            ],
            'builds' => [
                'total' => (int)$totalBuilds,
                'forks' => $forks
            ]
        ]
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