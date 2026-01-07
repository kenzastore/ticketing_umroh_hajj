<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Agent.php';

check_auth(['admin']);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

$totalItems = Agent::countAll();
$data = Agent::readAll($limit, $offset);

header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'pagination' => [
        'totalItems' => $totalItems,
        'totalPages' => ceil($totalItems / $limit),
        'currentPage' => $page,
        'limit' => $limit,
        'offset' => $offset
    ]
]);
