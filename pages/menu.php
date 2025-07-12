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
        .menu-section { max-width: 1200px; margin: 0 auto; padding: 40px 0 60px 0; margin-top: 90px; flex: 1 0 auto; }
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
        @media (max-width: 700px) {
            .menu-section { padding: 18px 0 32px 0; margin-top: 70px; }
            .menu-title { font-size: 2rem; }
            .menu-grid { gap: 18px; }
        }
    </style>
</head>
<body>
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
        // Category filter logic
        document.querySelectorAll('.menu-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.menu-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const cat = this.getAttribute('data-cat');
                document.querySelectorAll('.menu-card').forEach(card => {
                    if (card.getAttribute('data-cat') === cat) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        
        // Set first category as active by default
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
        });
    </script>
<?php require_once '../components/footer.php'; ?>
</body>
</html> 