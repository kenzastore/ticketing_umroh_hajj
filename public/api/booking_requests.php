<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';

check_auth(['admin']);

try {
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $totalItems = BookingRequest::countAll($startDate, $endDate);
    $requests = BookingRequest::readAll($limit, $offset, $startDate, $endDate);

    header('Content-Type: application/json');
    echo json_encode([
        'data' => $requests,
        'pagination' => [
            'totalItems' => $totalItems,
            'totalPages' => ceil($totalItems / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'offset' => $offset
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
