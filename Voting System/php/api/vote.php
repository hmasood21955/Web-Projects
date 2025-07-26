<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Students only.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List all active sessions assigned to this student
    $sessions = [];
    // Get all sessions assigned to the student that are active
    $res = $conn->prepare('
        SELECT DISTINCT 
            s.id, 
            s.name, 
            s.status,
            s.created_at
        FROM sessions s 
        INNER JOIN session_users su ON s.id = su.session_id 
        WHERE su.user_id = ? 
        AND s.status = "active" 
        ORDER BY s.created_at DESC
    ');
    $res->bind_param('i', $user_id);
    $res->execute();
    $result = $res->get_result();
    while ($row = $result->fetch_assoc()) {
        $row['candidates'] = [];
        $row['voted'] = false;
        $sessions[$row['id']] = $row;
    }
    $res->close();
    if (count($sessions) === 0) {
        echo json_encode(['success' => true, 'sessions' => []]);
        exit();
    }
    // Get candidates for these sessions
    $ids = implode(',', array_map('intval', array_keys($sessions)));
    $candRes = $conn->query("SELECT * FROM candidates WHERE session_id IN ($ids)");
    while ($row = $candRes->fetch_assoc()) {
        $sessions[$row['session_id']]['candidates'][] = $row;
    }
    // Check voting status
    $voteRes = $conn->query("SELECT session_id FROM votes WHERE user_id=$user_id AND session_id IN ($ids)");
    while ($row = $voteRes->fetch_assoc()) {
        $sessions[$row['session_id']]['voted'] = true;
    }
    echo json_encode(['success' => true, 'sessions' => array_values($sessions)]);
    exit();
}

if ($method === 'POST') {
    $session_id = intval($_POST['session_id'] ?? 0);
    $candidate_id = intval($_POST['candidate'] ?? 0);
    
    if (!$session_id || !$candidate_id) {
        echo json_encode(['success' => false, 'message' => 'Session and candidate required.']);
        exit();
    }

    // Check if student is assigned to this session
    $checkAssign = $conn->prepare('SELECT 1 FROM session_users WHERE session_id = ? AND user_id = ?');
    $checkAssign->bind_param('ii', $session_id, $user_id);
    $checkAssign->execute();
    $checkAssign->store_result();
    if ($checkAssign->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'You are not assigned to this session.']);
        exit();
    }
    $checkAssign->close();
    
    // Check if session is active
    $checkSession = $conn->prepare('SELECT status FROM sessions WHERE id = ? AND status = "active"');
    $checkSession->bind_param('i', $session_id);
    $checkSession->execute();
    $checkSession->store_result();
    if ($checkSession->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'This session is not active.']);
        exit();
    }
    $checkSession->close();
    // Check if already voted
    $check = $conn->prepare('SELECT id FROM votes WHERE user_id=? AND session_id=?');
    $check->bind_param('ii', $user_id, $session_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already voted in this session.']);
        exit();
    }
    $check->close();
    // Insert vote
    $stmt = $conn->prepare('INSERT INTO votes (user_id, candidate_id, session_id) VALUES (?, ?, ?)');
    $stmt->bind_param('iii', $user_id, $candidate_id, $session_id);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Vote submitted!' : 'Failed to vote.']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?> 