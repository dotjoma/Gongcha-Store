<?php
$current = basename($_SERVER['SCRIPT_NAME']);
function isActive($file) {
    global $current;
    return $current === $file ? 'active' : '';
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<aside class="admin-sidebar">
    <ul>
        <li class="<?= isActive('index.php') ?>"><a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li class="<?= isActive('category_management.php') ?>"><a href="category_management.php"><i class="fa-solid fa-tags"></i> Category Management</a></li>
        <li class="<?= isActive('product_management.php') ?>"><a href="product_management.php"><i class="fa-solid fa-boxes-stacked"></i> Product Management</a></li>
        <li class="<?= isActive('user_management.php') ?>"><a href="user_management.php"><i class="fa-solid fa-users"></i> User Management</a></li>
        <li><a href="../index.php"><i class="fa-solid fa-store"></i> View Store</a></li>
        <li><form method="post" action="../includes/process_auth.php" style="display:inline;"><input type="hidden" name="action" value="logout"><button type="submit" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</button></form></li>
    </ul>
</aside>
<style>
.admin-sidebar {
    width: 270px;
    background: #222;
    color: #fff;
    min-height: 100vh;
    position: fixed;
    left: 0; top: 0;
    padding-top: 60px;
    box-shadow: 2px 0 8px rgba(0,0,0,0.04);
    z-index: 100;
    overflow-x: hidden;
    max-width: 100vw;
    transition: width 0.2s;
}
.admin-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.admin-sidebar li {
    margin: 0;
}
.admin-sidebar a, .logout-link {
    display: flex;
    align-items: center;
    color: #fff;
    text-decoration: none;
    padding: 16px 28px;
    font-size: 1.08rem;
    transition: background 0.18s;
    border: none;
    background: none;
    text-align: left;
    width: 100%;
    cursor: pointer;
    gap: 12px;
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
}
.admin-sidebar a i, .logout-link i {
    min-width: 20px;
    text-align: center;
    font-size: 1.15em;
    margin-right: 8px;
}
.admin-sidebar li.active a, .admin-sidebar a:hover, .logout-link:hover {
    background: #be1635;
    color: #fff;
}
.logout-link {
    font-weight: 700;
}
.admin-main {
    margin-left: 270px;
    padding-top: 60px;
    min-height: 100vh;
}
@media (max-width: 900px) {
    .admin-sidebar {
        position: static;
        width: 100vw;
        min-height: auto;
        padding-top: 0;
        max-width: 100vw;
    }
    .admin-main {
        margin-left: 0;
    }
    .admin-sidebar a, .logout-link {
        font-size: 1rem;
        padding: 14px 16px;
    }
}
</style> 