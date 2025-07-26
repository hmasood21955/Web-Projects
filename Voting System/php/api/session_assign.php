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
        // List all students and their session assignments for admin
        $users = [];
        $res = $conn->query("SELECT id, username FROM users WHERE role='student' ORDER BY username");
        while ($row = $res->fetch_assoc()) {
            $row['sessions'] = [];
            $users[$row['id']] = $row;
        }
        $res2 = $conn->query('SELECT user_id, session_id FROM session_users');
        while ($row = $res2->fetch_assoc()) {
            if (isset($users[$row['user_id']])) {
                $users[$row['user_id']]['sessions'][] = $row['session_id'];
            }
        }
        echo json_encode(['success' => true, 'students' => array_values($users)]);
    } else {
        // Get sessions for current student
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
    $user_id = intval($_POST['user_id'] ?? 0);
    $session_id = intval($_POST['session_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if (!$user_id || !$session_id || !in_array($action, ['assign', 'unassign'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit();
    }
    if ($action === 'assign') {
        $stmt = $conn->prepare('INSERT IGNORE INTO session_users (session_id, user_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $session_id, $user_id);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Assigned.' : 'Failed to assign.']);
        exit();
    } else if ($action === 'unassign') {
        $stmt = $conn->prepare('DELETE FROM session_users WHERE session_id=? AND user_id=?');
        $stmt->bind_param('ii', $session_id, $user_id);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Unassigned.' : 'Failed to unassign.']);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?> 