<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/connection.php';
require_once '../../includes/logger.php';

function respond($success, $message, $extra = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $extra));
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        $logger->error('Invalid request method or missing action', ['_POST' => $_POST]);
        respond(false, 'Invalid request.');
    }

    $action = $_POST['action'];

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            $logger->error('Category name is required for add', ['_POST' => $_POST]);
            respond(false, 'Category name is required.');
        }
        $stmt = $conn->prepare('INSERT INTO categories (name) VALUES (?)');
        $ok = $stmt->execute([$name]);
        if ($ok) {
            respond(true, 'Category added successfully.', ['id' => $conn->lastInsertId()]);
        } else {
            $logger->error('Failed to add category', ['_POST' => $_POST, 'errorInfo' => $stmt->errorInfo()]);
            respond(false, 'Failed to add category.');
        }
    } elseif ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if (!$id || !$name) {
            $logger->error('ID and name required for edit', ['_POST' => $_POST]);
            respond(false, 'ID and name required.');
        }
        $stmt = $conn->prepare('UPDATE categories SET name=? WHERE id=?');
        $ok = $stmt->execute([$name, $id]);
        if ($ok) {
            respond(true, 'Category updated successfully.');
        } else {
            $logger->error('Failed to update category', ['_POST' => $_POST, 'errorInfo' => $stmt->errorInfo()]);
            respond(false, 'Failed to update category.');
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            $logger->error('ID required for delete', ['_POST' => $_POST]);
            respond(false, 'ID required.');
        }
        $stmt = $conn->prepare('DELETE FROM categories WHERE id=?');
        $ok = $stmt->execute([$id]);
        if ($ok) {
            respond(true, 'Category deleted successfully.');
        } else {
            $logger->error('Failed to delete category', ['_POST' => $_POST, 'errorInfo' => $stmt->errorInfo()]);
            respond(false, 'Failed to delete category.');
        }
    } else {
        $logger->error('Unknown action', ['_POST' => $_POST]);
        respond(false, 'Unknown action.');
    }
} catch (Exception $e) {
    $logger->error('Exception in process_category.php', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    respond(false, 'A server error occurred. Please check the logs.');
} 