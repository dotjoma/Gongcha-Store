<nav class="main-navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <span class="logo">Gong cha <span class="logo-sub">贡茶</span></span>
        </div>
        <ul class="navbar-center">
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Our Menu</a></li>
            <li><a href="#">Perks</a></li>
        </ul>
        <div class="navbar-right">
            <a href="#" class="nav-auth" onclick="openModal('loginModal');return false;">Login</a>
            <a href="#" class="nav-auth" onclick="openModal('registerModal');return false;">Register</a>
            <a href="#" class="order-btn">ORDER NOW</a>
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
    font-size: 1.05rem;
    transition: opacity 0.2s;
    padding: 6px 12px;
    border-radius: 4px;
}
.navbar-right .nav-auth:hover {
    background: rgba(255,255,255,0.12);
    opacity: 0.7;
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