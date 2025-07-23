<?php
header('Content-Type: application/json');

echo json_encode(array(
    'success' => true,
    'status' => 200,
    'message' => 'Connection successful.',
    'endpoints' => array('/stats','/list','/search')
));