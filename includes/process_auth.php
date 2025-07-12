<?php
session_start();
require_once __DIR__ . '/connection.php';
header('Content-Type: application/json');

function respond($success, $message, $extra = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    respond(false, 'Invalid request.');
}

$action = $_POST['action'];

if ($action === 'register') {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$fname || !$lname || !$email || !$password) {
        respond(false, 'All fields are required.');
    }
    // Check if email exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        respond(false, 'Email already registered.');
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (fname, lname, email, password, role) VALUES (?, ?, ?, ?, ?)');
    $ok = $stmt->execute([$fname, $lname, $email, $hashed, 'user']);
    if ($ok) {
        respond(true, 'Registration successful!');
    } else {
        respond(false, 'Registration failed.');
    }
} elseif ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        respond(false, 'Email and password required.');
    }
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fname'] = $user['fname'];
        $_SESSION['lname'] = $user['lname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        respond(true, 'Login successful!', ['user' => [
            'id' => $user['id'],
            'fname' => $user['fname'],
            'lname' => $user['lname'],
            'email' => $user['email'],
            'role' => $user['role']
        ]]);
    } else {
        respond(false, 'Invalid email or password.');
    }
} elseif ($action === 'logout') {
    session_unset();
    session_destroy();
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        respond(true, 'Logged out successfully.');
    } else {
        header('Location: ../index.php');
        exit;
    }
} else {
    respond(false, 'Unknown action.');
} 