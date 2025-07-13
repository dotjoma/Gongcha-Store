<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$currentDir = dirname($_SERVER['SCRIPT_NAME']);
$inPages = (strpos($currentDir, '/pages') !== false);
$inAdmin = (strpos($currentDir, '/admin') !== false);
$homeLink = $inPages ? '../index.php' : ($inAdmin ? '../index.php' : 'index.php');
$menuLink = $inPages ? 'menu.php' : ($inAdmin ? '../pages/menu.php' : 'pages/menu.php');
$adminLink = $inPages ? '../admin/index.php' : ($inAdmin ? 'index.php' : 'admin/index.php');
$logoutLink = $inPages ? '../includes/process_auth.php' : ($inAdmin ? '../includes/process_auth.php' : 'includes/process_auth.php');
$menuHeaderClass = ($currentPage === 'menu.php') ? 'menu-header' : '';
?>
<nav class="main-navbar <?= $menuHeaderClass ?>">
    <div class="navbar-container">
        <div class="navbar-left">
            <span class="logo">Gong cha <span class="logo-sub">贡茶</span></span>
        </div>
        <ul class="navbar-center">
            <li><a href="<?= $homeLink ?>">Home</a></li>
            <li><a href="<?= $menuLink ?>">Our Menu</a></li>
        </ul>
        <div class="navbar-right">
        <?php if (!empty($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="<?= $adminLink ?>" class="nav-auth" style="margin-right:10px;">Admin Dashboard</a>
        <?php endif; ?>
        <?php if (empty($_SESSION['user_id'])): ?>
            <a href="#" class="nav-auth" onclick="handleAuthClick('login');return false;">Login</a>
            <a href="#" class="nav-auth" onclick="handleAuthClick('register');return false;">Register</a>
        <?php else: ?>
            <form id="logoutForm" method="post" action="<?= $logoutLink ?>" style="display:inline;">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="nav-auth" style="background:none;border:none;color:#fff;font-weight:700;font-size:1.05rem;cursor:pointer;padding:6px 12px;border-radius:4px;">Logout</button>
            </form>
        <?php endif; ?>
    </div>
    </div>
</nav>

<style>
    .main-navbar {
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 10;
        background: transparent;
        font-family: 'Georgia', serif;
    }
    .menu-header.main-navbar {
        background: #fff !important;
    }
    .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 48px 0 48px;
    }
    .navbar-left .logo {
        font-size: 2rem;
        font-weight: 600;
        color: #fff;
        letter-spacing: 1px;
    }
    .logo-sub {
        font-size: 1rem;
        font-weight: 400;
        margin-left: 4px;
        opacity: 0.7;
    }
    .navbar-center {
        display: flex;
        gap: 32px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .navbar-center li a {
        color: #fff;
        font-weight: 700;
        text-decoration: none;
        font-size: 1.05rem;
        transition: opacity 0.2s;
    }
    .navbar-center li a:hover {
        opacity: 0.7;
    }
    .navbar-right {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .icon span {
        font-size: 1.3rem;
        color: #fff;
        margin-right: 2px;
        vertical-align: middle;
    }
    .order-btn {
        background: #e50914;
        color: #fff;
        font-weight: 700;
        padding: 10px 24px;
        border-radius: 5px;
        text-decoration: none;
        margin-left: 10px;
        transition: background 0.2s;
    }
    .order-btn:hover {
        background: #b0060f;
    }
    .navbar-right .nav-auth {
        color: #fff;
        font-weight: 700;
        text-decoration: none;
        margin-right: 10px;
        font-size: 1rem;
        transition: opacity 0.2s;
        padding: 6px 12px;
        border-radius: 4px;
    }
    .navbar-right .nav-auth:hover {
        background: rgba(255,255,255,0.12);
        opacity: 0.7;
    }
    .menu-header .logo,
    .menu-header .logo-sub,
    .menu-header .navbar-center li a,
    .menu-header .navbar-right .nav-auth,
    .menu-header .order-btn {
        color: #be1635 !important;
    }
    .menu-header .order-btn {
        background: #fff;
        border: 2px solid #be1635;
    }
    .menu-header .order-btn:hover {
        background: #be1635;
        color: #fff !important;
    }
    @media (max-width: 900px) {
        .navbar-container {
            flex-direction: column;
            align-items: flex-start;
            padding: 16px 16px 0 16px;
        }
        .navbar-center {
            gap: 16px;
            font-size: 0.95rem;
        }
        .order-btn {
            padding: 8px 16px;
            font-size: 0.95rem;
        }
    }
</style>

<script>
    function handleAuthClick(type) {
        const currentPath = window.location.pathname;
        const isIndexPage = currentPath.endsWith('index.php') || currentPath.endsWith('/') || currentPath === '';
        
        if (isIndexPage) {
            // If already on index page, just open the modal
            if (type === 'login') {
                openModal('loginModal');
            } else if (type === 'register') {
                openModal('registerModal');
            }
        } else {
            // If not on index page, navigate to index with modal parameter
            const modalParam = type === 'login' ? 'login' : 'register';
            window.location.href = '<?= $homeLink ?>' + '?modal=' + modalParam;
        }
    }

    // Check for modal parameter on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const modalParam = urlParams.get('modal');
        
        if (modalParam === 'login') {
            // Small delay to ensure page is fully loaded
            setTimeout(() => {
                if (typeof openModal === 'function') {
                    openModal('loginModal');
                }
            }, 100);
        } else if (modalParam === 'register') {
            // Small delay to ensure page is fully loaded
            setTimeout(() => {
                if (typeof openModal === 'function') {
                    openModal('registerModal');
                }
            }, 100);
        }
    });
</script>