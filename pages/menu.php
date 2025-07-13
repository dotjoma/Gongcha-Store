<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once '../components/header.php';
    require_once '../includes/connection.php';
    // Fetch categories
    $catStmt = $conn->query('SELECT * FROM categories ORDER BY name ASC');
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch products with category name
    $prodStmt = $conn->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC');
    $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch all sizes for all products
    $sizeStmt = $conn->query('SELECT * FROM product_sizes');
    $sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);
    $productSizes = [];
    foreach ($sizes as $s) {
        $productSizes[$s['product_id']][$s['size']] = $s['price'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Gong cha</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f3f0ec;
            background-image: url('data:image/svg+xml;utf8,<svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><circle fill="%23e9e4d9" fill-opacity="0.18" cx="10" cy="10" r="1.5"/><circle fill="%23e9e4d9" fill-opacity="0.13" cx="60" cy="40" r="1.2"/><circle fill="%23e9e4d9" fill-opacity="0.12" cx="80" cy="80" r="1.7"/><circle fill="%23e9e4d9" fill-opacity="0.10" cx="30" cy="70" r="1.1"/><circle fill="%23e9e4d9" fill-opacity="0.09" cx="90" cy="20" r="1.3"/></svg>');
            background-repeat: repeat;
        }
        .menu-section { max-width: 1200px; margin: 0 auto; padding: 40px 0 60px 0; margin-top: 5px; flex: 1 0 auto; }
        .menu-title { text-align: center; font-size: 2.5rem; color: #be1635; font-weight: 800; margin-bottom: 32px; letter-spacing: 1px; }
        .menu-filters { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-bottom: 36px; }
        .menu-filter-btn { background: #fff; color: #be1635; border: 2px solid #be1635; border-radius: 7px; padding: 10px 22px; font-size: 1.08rem; font-weight: 700; cursor: pointer; transition: background 0.18s, color 0.18s; }
        .menu-filter-btn.active, .menu-filter-btn:hover { background: #be1635; color: #fff; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; justify-items: center; }
        .menu-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(190,22,53,0.08); padding: 24px 18px 18px 18px; display: flex; flex-direction: column; align-items: center; transition: box-shadow 0.18s; width: 100%; max-width: 260px; }
        .menu-card:hover { box-shadow: 0 4px 24px rgba(190,22,53,0.16); }
        .menu-card img { width: 120px; height: 120px; object-fit: cover; border-radius: 10px; border: 1.5px solid #eee; margin-bottom: 16px; }
        .menu-card-name { font-size: 1.13rem; font-weight: 700; color: #be1635; text-align: center; margin-bottom: 10px; }
        .menu-card-category { font-size: 0.98rem; color: #888; margin-bottom: 8px; }
        .menu-card-prices { font-size: 1.01rem; color: #222; margin-bottom: 2px; }
        .menu-card-prices span { margin-right: 8px; }
        
        /* Featured Products Styles */
        .featured-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px 60px 20px;
            margin-top: 90px;
        }
        .featured-title {
            text-align: center;
            font-size: 2.5rem;
            color: #be1635;
            font-weight: 800;
            margin-bottom: 40px;
            letter-spacing: 1px;
        }
        .featured-grid {
            display: flex;
            gap: 25px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: thin;
            scrollbar-color: #be1635 #f0f0f0;
            scroll-behavior: smooth;
        }
        .featured-grid::-webkit-scrollbar {
            height: 8px;
        }
        .featured-grid::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 4px;
        }
        .featured-grid::-webkit-scrollbar-thumb {
            background: #be1635;
            border-radius: 4px;
        }
        .featured-grid::-webkit-scrollbar-thumb:hover {
            background: #a0122b;
        }
        .featured-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(190,22,53,0.12);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            min-width: 280px;
            flex-shrink: 0;
        }
        .featured-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(190,22,53,0.2);
        }
        .featured-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid #f0f0f0;
        }
        .featured-card-content {
            padding: 20px;
        }
        .featured-card-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #be1635;
            margin-bottom: 8px;
            text-align: center;
        }
        .featured-card-category {
            font-size: 1rem;
            color: #888;
            text-align: center;
            font-style: italic;
        }
        .featured-card-prices {
            text-align: center;
        }
        .featured-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: #222;
            background: linear-gradient(135deg, #be1635, #d41e3f);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        @keyframes shimmer {
            0% {
                background-position: -200px 0;
            }
            100% {
                background-position: calc(200px + 100%) 0;
            }
        }
        
        /* Apply animations to elements */
        .featured-title {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .featured-card {
            animation: slideInLeft 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .featured-card:nth-child(1) { animation-delay: 0.1s; }
        .featured-card:nth-child(2) { animation-delay: 0.2s; }
        .featured-card:nth-child(3) { animation-delay: 0.3s; }
        .featured-card:nth-child(4) { animation-delay: 0.4s; }
        .featured-card:nth-child(5) { animation-delay: 0.5s; }
        .featured-card:nth-child(6) { animation-delay: 0.6s; }
        
        .menu-title {
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }
        
        .menu-filters {
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }
        
        .menu-card {
            animation: slideInRight 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .menu-card:nth-child(1) { animation-delay: 0.1s; }
        .menu-card:nth-child(2) { animation-delay: 0.2s; }
        .menu-card:nth-child(3) { animation-delay: 0.3s; }
        .menu-card:nth-child(4) { animation-delay: 0.4s; }
        .menu-card:nth-child(5) { animation-delay: 0.5s; }
        .menu-card:nth-child(6) { animation-delay: 0.6s; }
        .menu-card:nth-child(7) { animation-delay: 0.7s; }
        .menu-card:nth-child(8) { animation-delay: 0.8s; }
        
        /* Hover animations */
        .menu-filter-btn:hover {
            animation: pulse 0.3s ease-in-out;
        }
        
        .menu-card:hover {
            animation: bounce 0.6s ease-in-out;
        }
        
        .featured-card:hover {
            animation: pulse 0.4s ease-in-out;
        }
        
        /* Loading animation for images */
        .menu-card img, .featured-card img {
            transition: all 0.3s ease;
        }
        
        .menu-card img:hover, .featured-card img:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(190,22,53,0.25);
        }
        
        /* Smooth transitions for all interactive elements */
        .menu-filter-btn, .menu-card, .featured-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Filter button active state animation */
        .menu-filter-btn.active {
            animation: pulse 0.3s ease-in-out;
        }
        
        /* Price animation */
        .featured-price {
            animation: fadeInUp 0.8s ease-out 0.8s both;
        }
        
        @media (max-width: 700px) {
            .menu-section { padding: 18px 0 32px 0; margin-top: 70px; }
            .menu-title { font-size: 2rem; }
            .menu-grid { gap: 18px; }
            .featured-section { padding: 20px 15px 40px 15px; margin-top: 70px; }
            .featured-title { font-size: 2rem; margin-bottom: 30px; }
            .featured-grid { gap: 15px; }
            .featured-card { min-width: 240px; }
            .featured-card img { height: 140px; }
            .featured-card-name { font-size: 1.1rem; }
            .featured-price { font-size: 1.2rem; padding: 6px 12px; }
        }
    </style>
</head>
<body>
    <!-- Featured Products Section -->
    <div class="featured-section">
        <div class="featured-title">FEATURED PRODUCTS</div>
        <div class="featured-grid">
            <?php 
            // Get featured products from database
            $featuredStmt = $conn->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 ORDER BY p.created_at DESC');
            $featuredProducts = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($featuredProducts as $prod): 
            ?>
                <div class="featured-card">
                    <?php if ($prod['image']): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                    <?php else: ?>
                        <img src="../assets/placeholder.png" alt="No Image">
                    <?php endif; ?>
                    <div class="featured-card-content">
                        <div class="featured-card-name"><?= htmlspecialchars($prod['name']) ?></div>
                        <div class="featured-card-category"><?= htmlspecialchars($prod['category_name']) ?></div>
                        <div class="featured-card-prices">
                            <?php 
                            $priceDisplayed = false;
                            foreach (["small","medium","large"] as $sz): 
                                if (isset($productSizes[$prod['id']][$sz]) && !$priceDisplayed): 
                                    $priceDisplayed = true;
                            ?>
                                <span class="featured-price">₱<?= number_format($productSizes[$prod['id']][$sz],2) ?></span>
                            <?php 
                                break;
                                endif; 
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="menu-section">
        <div class="menu-title">OUR MENU</div>
        <div class="menu-filters">
            <?php foreach ($categories as $cat): ?>
                <button class="menu-filter-btn" data-cat="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="menu-grid" id="menuGrid">
            <?php foreach ($products as $prod): ?>
                <div class="menu-card" data-cat="<?= htmlspecialchars($prod['category_id']) ?>">
                    <?php if ($prod['image']): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                    <?php else: ?>
                        <img src="../assets/placeholder.png" alt="No Image">
                    <?php endif; ?>
                    <div class="menu-card-name"><?= htmlspecialchars($prod['name']) ?></div>
                    <div class="menu-card-category"><?= htmlspecialchars($prod['category_name']) ?></div>
                    <div class="menu-card-prices">
                        <?php foreach (["small","medium","large"] as $sz): ?>
                            <?php if (isset($productSizes[$prod['id']][$sz])): ?>
                                <span><b><?= ucfirst($sz) ?>:</b> ₱<?= number_format($productSizes[$prod['id']][$sz],2) ?></span><br>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        // Auto-scroll functionality for featured products
        function autoScroll() {
            const featuredGrid = document.querySelector('.featured-grid');
            if (!featuredGrid) return;
            
            const scrollSpeed = 1; // pixels per frame
            let animationId;
            
            function animate() {
                const currentScroll = featuredGrid.scrollLeft;
                const maxScroll = featuredGrid.scrollWidth - featuredGrid.clientWidth;
                
                // If we've reached the end, reset to the beginning for seamless loop
                if (currentScroll >= maxScroll) {
                    featuredGrid.scrollLeft = 0;
                } else {
                    // Continue scrolling to the right
                    featuredGrid.scrollLeft += scrollSpeed;
                }
                
                // Continue the animation loop
                animationId = requestAnimationFrame(animate);
            }
            
            // Start the animation
            animationId = requestAnimationFrame(animate);
        }
        
        // Category filter logic with animations
        document.querySelectorAll('.menu-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.menu-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const cat = this.getAttribute('data-cat');
                
                // Animate cards out
                document.querySelectorAll('.menu-card').forEach((card, index) => {
                    if (card.getAttribute('data-cat') === cat) {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = '';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 50);
                        }, 200);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(-20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 200);
                    }
                });
            });
        });
        
        // Set first category as active by default and start auto-scroll
        document.addEventListener('DOMContentLoaded', function() {
            const firstBtn = document.querySelector('.menu-filter-btn');
            if (firstBtn) {
                firstBtn.classList.add('active');
                const cat = firstBtn.getAttribute('data-cat');
                document.querySelectorAll('.menu-card').forEach(card => {
                    if (card.getAttribute('data-cat') === cat) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            // Start auto-scroll after a short delay
            setTimeout(autoScroll, 200);
            
            // Add scroll-triggered animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe menu cards for scroll animations
            document.querySelectorAll('.menu-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
<?php require_once '../components/footer.php'; ?>
</body>
</html> 