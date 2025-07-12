<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
require_once '../../includes/connection.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

function respond($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($action === 'add') {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$fname || !$lname || !$email || !$role || !$password) {
        respond(false, 'All fields are required.');
    }
    // Check for duplicate email
    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        respond(false, 'Email already exists.');
    }
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $created_at = date('Y-m-d H:i:s');
    $stmt = $conn->prepare('INSERT INTO users (fname, lname, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([$fname, $lname, $email, $passwordHash, $role, $created_at])) {
        respond(true, 'User added successfully.');
    } else {
        respond(false, 'Failed to add user.');
    }
} elseif ($action === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$id || !$fname || !$lname || !$email || !$role) {
        respond(false, 'All fields are required.');
    }
    // Check for duplicate email (excluding current user)
    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id != ?');
    $stmt->execute([$email, $id]);
    if ($stmt->fetchColumn() > 0) {
        respond(false, 'Email already exists.');
    }
    if ($password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE users SET fname = ?, lname = ?, email = ?, password = ?, role = ? WHERE id = ?');
        $success = $stmt->execute([$fname, $lname, $email, $passwordHash, $role, $id]);
    } else {
        $stmt = $conn->prepare('UPDATE users SET fname = ?, lname = ?, email = ?, role = ? WHERE id = ?');
        $success = $stmt->execute([$fname, $lname, $email, $role, $id]);
    }
    if ($success) {
        respond(true, 'User updated successfully.');
    } else {
        respond(false, 'Failed to update user.');
    }
} elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        respond(false, 'Invalid user ID.');
    }
    $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
    if ($stmt->execute([$id])) {
        respond(true, 'User deleted successfully.');
    } else {
        respond(false, 'Failed to delete user.');
    }
} else {
    respond(false, 'Invalid action.');
} 