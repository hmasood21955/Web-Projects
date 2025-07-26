<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if ($_SESSION['role'] === 'admin') {
        // Admin can see all sessions
        $res = $conn->query('SELECT * FROM sessions ORDER BY created_at DESC');
        $sessions = [];
        while ($row = $res->fetch_assoc()) $sessions[] = $row;
        echo json_encode(['success' => true, 'sessions' => $sessions]);
    } else {
        // Students can only see their assigned sessions
        $user_id = $_SESSION['user_id'];
        $query = "SELECT s.* FROM sessions s 
                 INNER JOIN session_users su ON s.id = su.session_id 
                 WHERE su.user_id = ? 
                 ORDER BY s.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $sessions[] = $row;
        }
        echo json_encode(['success' => true, 'sessions' => $sessions]);
    }
    exit();
}

if ($method === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'Session name required.']);
        exit();
    }
    $stmt = $conn->prepare('INSERT INTO sessions (name) VALUES (?)');
    $stmt->bind_param('s', $name);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Session created.' : 'Failed to create session.']);
    exit();
}

if ($method === 'PATCH') {
    parse_str(file_get_contents('php://input'), $_PATCH);
    $id = intval($_PATCH['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Session ID required.']);
        exit();
    }
    $stmt = $conn->prepare('UPDATE sessions SET status="closed" WHERE id=?');
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Session closed.' : 'Failed to close session.']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?> 