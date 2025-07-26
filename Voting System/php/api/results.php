<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit();
}

$session_id = intval($_GET['session_id'] ?? 0);
if (!$session_id) {
    echo json_encode(['success' => false, 'message' => 'Session ID required.']);
    exit();
}

// Check if user has access to this session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'student') {
    // Students can only view results of sessions they're assigned to
    $check = $conn->prepare('SELECT 1 FROM session_users WHERE session_id = ? AND user_id = ?');
    $check->bind_param('ii', $session_id, $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Access denied.']);
        exit();
    }
    $check->close();
}

// Get session status
$status = $conn->prepare('SELECT status FROM sessions WHERE id = ?');
$status->bind_param('i', $session_id);
$status->execute();
$status->bind_result($session_status);
$status->fetch();
$status->close();

if ($session_status !== 'closed') {
    echo json_encode(['success' => false, 'message' => 'Results are only available for closed sessions.']);
    exit();
}

// Get candidates and their vote counts
$results = [];
$query = "
    SELECT 
        c.id,
        c.name,
        COUNT(v.id) as vote_count
    FROM candidates c
    LEFT JOIN votes v ON c.id = v.candidate_id
    WHERE c.session_id = ?
    GROUP BY c.id, c.name
    ORDER BY vote_count DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $session_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

// Get total votes
$total = 0;
foreach ($results as $r) {
    $total += $r['vote_count'];
}

echo json_encode([
    'success' => true,
    'results' => $results,
    'total_votes' => $total
]);
?>
