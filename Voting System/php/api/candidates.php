<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Admin only.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $session_id = intval($_GET['session_id'] ?? 0);
    if (!$session_id) {
        echo json_encode(['success' => false, 'message' => 'Session ID required.']);
        exit();
    }
    $res = $conn->prepare('SELECT * FROM candidates WHERE session_id=?');
    $res->bind_param('i', $session_id);
    $res->execute();
    $result = $res->get_result();
    $candidates = [];
    while ($row = $result->fetch_assoc()) $candidates[] = $row;
    $res->close();
    echo json_encode(['success' => true, 'candidates' => $candidates]);
    exit();
}

if ($method === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $session_id = intval($_POST['session_id'] ?? 0);
    if ($name === '' || !$session_id) {
        echo json_encode(['success' => false, 'message' => 'Name and session required.']);
        exit();
    }
    $stmt = $conn->prepare('INSERT INTO candidates (name, session_id) VALUES (?, ?)');
    $stmt->bind_param('si', $name, $session_id);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Candidate added.' : 'Failed to add candidate.']);
    exit();
}

if ($method === 'DELETE') {
    parse_str(file_get_contents('php://input'), $_DELETE);
    $id = intval($_DELETE['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Candidate ID required.']);
        exit();
    }
    $stmt = $conn->prepare('DELETE FROM candidates WHERE id=?');
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Candidate removed.' : 'Failed to remove candidate.']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?> 