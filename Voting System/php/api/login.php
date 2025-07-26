<?php
require_once '../includes/db.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Username and password required.']);
    exit();
}

$stmt = $conn->prepare('SELECT id, password, role FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit();
}
$stmt->bind_result($id, $hash, $role);
$stmt->fetch();
if (!password_verify($password, $hash)) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit();
}
$_SESSION['user_id'] = $id;
$_SESSION['username'] = $username;
$_SESSION['role'] = $role;
echo json_encode(['success' => true, 'message' => 'Login successful.', 'user' => [
    'id' => $id,
    'username' => $username,
    'role' => $role
]]);
$stmt->close();
?> 