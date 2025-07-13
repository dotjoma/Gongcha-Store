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
        $category_id = intval($_POST['category_id'] ?? 0);
        $sizes = $_POST['sizes'] ?? [];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
            $image = file_get_contents($_FILES['image']['tmp_name']);
        }
        if (!$name || !$category_id || empty($sizes)) {
            $logger->error('Missing required fields for add', ['_POST' => $_POST]);
            respond(false, 'All fields are required.');
        }
        $stmt = $conn->prepare('INSERT INTO products (name, category_id, image, is_featured) VALUES (?, ?, ?, ?)');
        $ok = $stmt->execute([$name, $category_id, $image, $is_featured]);
        if ($ok) {
            $product_id = $conn->lastInsertId();
            // Insert sizes
            $sizeStmt = $conn->prepare('INSERT INTO product_sizes (product_id, size, price) VALUES (?, ?, ?)');
            foreach ($sizes as $size => $price) {
                $sizeStmt->execute([$product_id, $size, $price]);
            }
            respond(true, 'Product added successfully.');
        } else {
            $logger->error('Failed to add product', ['_POST' => $_POST, 'errorInfo' => $stmt->errorInfo()]);
            respond(false, 'Failed to add product.');
        }
    } elseif ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $sizes = $_POST['sizes'] ?? [];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $image = null;
        $updateImage = false;
        if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
            $image = file_get_contents($_FILES['image']['tmp_name']);
            $updateImage = true;
        }
        if (!$id || !$name || !$category_id || empty($sizes)) {
            $logger->error('Missing required fields for edit', ['_POST' => $_POST]);
            respond(false, 'All fields are required.');
        }
        if ($updateImage) {
            $stmt = $conn->prepare('UPDATE products SET name=?, category_id=?, image=?, is_featured=? WHERE id=?');
            $ok = $stmt->execute([$name, $category_id, $image, $is_featured, $id]);
        } else {
            $stmt = $conn->prepare('UPDATE products SET name=?, category_id=?, is_featured=? WHERE id=?');
            $ok = $stmt->execute([$name, $category_id, $is_featured, $id]);
        }
        if ($ok) {
            // Update sizes: delete old, insert new
            $conn->prepare('DELETE FROM product_sizes WHERE product_id=?')->execute([$id]);
            $sizeStmt = $conn->prepare('INSERT INTO product_sizes (product_id, size, price) VALUES (?, ?, ?)');
            foreach ($sizes as $size => $price) {
                $sizeStmt->execute([$id, $size, $price]);
            }
            respond(true, 'Product updated successfully.');
        } else {
            $logger->error('Failed to update product', ['_POST' => $_POST, 'errorInfo' => $stmt->errorInfo()]);
            respond(false, 'Failed to update product.');
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            $logger->error('ID required for delete', ['_POST' => $_POST]);
            respond(false, 'ID required.');
        }
        $conn->prepare('DELETE FROM product_sizes WHERE product_id=?')->execute([$id]);
        $stmt = $conn->prepare('DELETE FROM products WHERE id=?');
        $ok = $stmt->execute([$id]);
        if ($ok) {
            respond(true, 'Product deleted successfully.');
        } else {
            $logger->error('Failed to delete product', ['_POST' => $_POST, 'errorInfo' => $stmt->errorInfo()]);
            respond(false, 'Failed to delete product.');
        }
    } else {
        $logger->error('Unknown action', ['_POST' => $_POST]);
        respond(false, 'Unknown action.');
    }
} catch (Exception $e) {
    $logger->error('Exception in process_product.php', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    respond(false, 'A server error occurred. Please check the logs.');
} 