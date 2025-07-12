<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../includes/connection.php';
// Get stats
$userCount = $conn->query('SELECT COUNT(*) FROM users')->fetchColumn();
$productCount = $conn->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = $conn->query('SELECT COUNT(*) FROM categories')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gong cha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            background: #f6f6f6;
        }
        .admin-main {
            margin-left: 270px;
            padding-top: 60px;
            min-height: 100vh;
        }
        .admin-content {
            padding: 32px 40px;
        }
        .dashboard-cards {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
            margin-bottom: 36px;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(190,22,53,0.08);
            padding: 32px 38px 28px 38px;
            min-width: 220px;
            flex: 1 1 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: box-shadow 0.18s;
        }
        .dashboard-card:hover {
            box-shadow: 0 4px 24px rgba(190,22,53,0.16);
        }
        .dashboard-card .card-icon {
            font-size: 2.5rem;
            color: #be1635;
            margin-bottom: 18px;
        }
        .dashboard-card .card-label {
            font-size: 1.15rem;
            color: #444;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .dashboard-card .card-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #222;
        }
        @media (max-width: 900px) {
            .admin-main {
                margin-left: 0;
                padding-top: 60px;
            }
            .dashboard-cards {
                flex-direction: column;
                gap: 18px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-content">
            <h1>Welcome, Admin!</h1>
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="card-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="card-label">Total Users</div>
                    <div class="card-value"><?php echo $userCount; ?></div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <div class="card-label">Total Products</div>
                    <div class="card-value"><?php echo $productCount; ?></div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon"><i class="fa-solid fa-tags"></i></div>
                    <div class="card-label">Total Categories</div>
                    <div class="card-value"><?php echo $categoryCount; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 