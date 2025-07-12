<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../includes/connection.php';

if (!isset($products)) $products = [];
if (!isset($productSizes)) $productSizes = [];

// Fetch categories
$catStmt = $conn->query('SELECT * FROM categories ORDER BY name ASC');
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch products with sizes and category
$prodStmt = $conn->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC');
$products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
error_log('Database query result: ' . print_r($products, true));
error_log('Number of products found: ' . count($products));
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
    <title>Manage Products - Admin Dashboard</title>
    <style>
        body, html { margin: 0; padding: 0; font-family: 'Georgia', serif; background: #f6f6f6; }
        .admin-main { margin-left: 220px; padding-top: 60px; min-height: 100vh; }
        .admin-content { padding: 32px 40px; }
        .prod-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .prod-table th, .prod-table td { padding: 14px 16px; border-bottom: 1px solid #eee; text-align: left; }
        .prod-table th { background: #be1635; color: #fff; }
        .prod-table tr:last-child td { border-bottom: none; }
        .prod-img { width: 60px; height: 60px; object-fit: cover; border-radius: 7px; border: 1px solid #eee; }
        .prod-actions button { margin-right: 8px; padding: 6px 14px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .prod-actions .edit-btn { background: #222; color: #fff; }
        .prod-actions .delete-btn { background: #be1635; color: #fff; }
        .add-prod-btn { background: #be1635; color: #fff; border: none; border-radius: 4px; padding: 10px 22px; font-size: 1rem; font-weight: 700; margin-bottom: 18px; cursor: pointer; }
        .add-prod-btn:hover { background: #a0122b; }
        .size-price { font-size: 0.98rem; margin-right: 8px; }
        .toast {
            visibility: hidden;
            min-width: 220px;
            background-color: #222;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 16px 24px;
            position: fixed;
            z-index: 3000;
            right: 32px;
            top: 32px;
            font-size: 1.1rem;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.4s, top 0.4s;
        }
        .toast.show {
            visibility: visible;
            opacity: 1;
            top: 48px;
        }
        .toast.success { background: #27ae60; }
        .toast.error { background: #be1635; }
        .prod-modal-bg {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(24,23,28,0.7);
            align-items: center;
            justify-content: center;
        }
        .prod-modal-bg.show { display: flex; }
        .prod-modal-content {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(190,22,53,0.18), 0 2px 8px rgba(0,0,0,0.08);
            padding: 38px 32px 32px 32px;
            max-width: 420px;
            width: 96vw;
            min-width: 0;
            text-align: center;
            position: relative;
            animation: prodModalFadeIn 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            border: 1.5px solid #f2d7dd;
        }
        .prod-modal-content h2 {
            font-size: 2.1rem;
            font-family: 'Georgia', serif;
            font-weight: 800;
            margin-bottom: 28px;
            margin-top: 0;
            letter-spacing: 1px;
            color: #be1635;
            text-shadow: 0 2px 8px #f6cdd6;
        }
        .prod-modal-content label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #be1635;
            display: block;
            text-align: left;
        }
        .prod-modal-content .delete-confirm-text {
            font-size: 1.13rem;
            color: #be1635;
            font-weight: 600;
            margin: 18px 0 24px 0;
            background: #fff0f4;
            border-radius: 7px;
            padding: 10px 0;
        }
        .prod-modal-content input[type="file"] {
            margin-bottom: 18px;
            border: none;
            background: #f6f6f6;
            padding: 8px 0;
            border-radius: 7px;
            width: 100%;
        }
        .prod-modal-content img#editProdImgPreview {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #eee;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px #f6cdd6;
            display: block;
        }
        #prodEditForm,
        #prodAddForm {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #prodEditForm input[type="text"], #prodEditForm input[type="number"], #prodEditForm select,
        #prodAddForm input[type="text"], #prodAddForm input[type="number"], #prodAddForm select {
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 7px;
            border: 1px solid #ccc;
            font-size: 1.08rem;
            font-family: 'Georgia', serif;
            background: #f9f9f9;
            transition: border 0.2s;
        }
        #prodEditForm input[type="text"]:focus, #prodEditForm input[type="number"]:focus, #prodEditForm select:focus,
        #prodAddForm input[type="text"]:focus, #prodAddForm input[type="number"]:focus, #prodAddForm select:focus {
            border: 1.5px solid #be1635;
            outline: none;
            background: #fff0f4;
        }
        #prodEditForm .prod-modal-msg,
        #prodAddForm .prod-modal-msg {
            min-height: 22px;
        }
        #prodEditForm .price-row,
        #prodAddForm .price-row {
            display: flex;
            gap: 10px;
            width: 100%;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        #prodEditForm .price-row input,
        #prodAddForm .price-row input {
            flex: 1 1 0;
            min-width: 0;
            background: #fff;
            border: 1px solid #f2d7dd;
            font-weight: 600;
        }
        #prodEditForm button, #prodAddForm button {
            width: 100%;
            background: #be1635;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 13px 0;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-bottom: 10px;
            transition: background 0.18s, box-shadow 0.18s;
            box-shadow: 0 2px 8px #f6cdd6;
        }
        #prodEditForm button:hover, #prodAddForm button:hover {
            background: #a0122b;
            box-shadow: 0 4px 16px #f6cdd6;
        }
        #prodDeleteForm button[type="submit"] {
            background: #be1635;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 13px 0;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-bottom: 10px;
            width: 100%;
            transition: background 0.18s, box-shadow 0.18s;
            box-shadow: 0 2px 8px #f6cdd6;
        }
        #prodDeleteForm button[type="submit"]:hover {
            background: #a0122b;
            box-shadow: 0 4px 16px #f6cdd6;
        }
        #prodDeleteForm button[type="button"] {
            background: #222;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 13px 0;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-bottom: 0;
            transition: background 0.18s, box-shadow 0.18s;
        }
        #prodDeleteForm button[type="button"]:hover {
            background: #444;
        }
        .prod-modal-close {
            position: absolute;
            top: 18px; right: 22px;
            font-size: 1.7rem;
            color: #be1635;
            cursor: pointer;
            font-weight: 700;
            transition: color 0.18s;
        }
        .prod-modal-close:hover { color: #a0122b; }
        .prod-modal-msg {
            font-size: 1rem;
            font-weight: 600;
            margin-top: 6px;
            min-height: 22px;
        }
        </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-content">
            <h1>Manage Products</h1>
            <div style="display:flex;gap:12px;margin-bottom:18px;flex-wrap:wrap;align-items:center;">
                <input type="text" id="prodFilterInput" placeholder="Filter by product name..." style="padding:10px 14px;border-radius:6px;border:1px solid #ccc;font-size:1.05rem;width:220px;max-width:100%;font-family:'Georgia',serif;">
                <select id="prodCategoryFilter" style="padding:10px 14px;border-radius:6px;border:1px solid #ccc;font-size:1.05rem;width:200px;max-width:100%;font-family:'Georgia',serif;">
                    <option value="all">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="add-prod-btn" onclick="openProdModal()">+ Add Product</button>
            <table class="prod-table" id="prodTable">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Prices</th>
                    <th>Actions</th>
                </tr>
                <?php 
                error_log('About to loop through ' . count($products) . ' products');
                foreach ($products as $prod): 
                    error_log('Processing product: ' . print_r($prod, true));
                ?>
                <tr data-cat-id="<?= $prod['category_id'] ?>">
                    <td>
                        <?php if ($prod['image']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($prod['image']) ?>" class="prod-img" alt="Product Image">
                        <?php else: ?>
                            <span style="color:#aaa;">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($prod['name']) ?></td>
                    <td><?= htmlspecialchars($prod['category_name']) ?></td>
                    <td>
                        <?php foreach (["small","medium","large"] as $sz): ?>
                            <?php if (isset($productSizes[$prod['id']][$sz])): ?>
                                <span class="size-price"><b><?= ucfirst($sz) ?>:</b> ₱<?= number_format($productSizes[$prod['id']][$sz],2) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                    <td class="prod-actions">
                        <button class="edit-btn" onclick="openProdEditModal(<?= $prod['id'] ?>)" data-product-id="<?= $prod['id'] ?>" data-product-name="<?= htmlspecialchars($prod['name']) ?>">Edit</button>
                        <button class="delete-btn" onclick="openProdDeleteModal(<?= $prod['id'] ?>, '<?= htmlspecialchars(addslashes($prod['name'])) ?>')">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div id="prodPagination" style="margin-top:18px;display:flex;justify-content:center;gap:8px;"></div>
        </div>
    </div>
    <!-- Add Product Modal -->
    <div id="prodModal" class="prod-modal-bg">
        <div class="prod-modal-content">
            <span class="prod-modal-close" onclick="closeProdModal()">&times;</span>
            <h2>Add Product</h2>
            <form id="prodAddForm" method="post" enctype="multipart/form-data" action="#">
                <input type="file" name="image" accept="image/*" style="margin-bottom:18px;">
                <input type="text" name="name" placeholder="Product Name" required>
                <select name="category_id" required style="margin-bottom:18px; padding:10px; border-radius:7px; border:1px solid #ccc; font-size:1.08rem; width:100%; font-family:'Georgia',serif;">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div style="width:100%;margin-bottom:18px;">
                    <label style="display:block;text-align:left;font-weight:600;margin-bottom:6px;">Prices:</label>
                    <div class="price-row">
                        <input type="number" name="sizes[small]" placeholder="Small" min="0" step="0.01" required>
                        <input type="number" name="sizes[medium]" placeholder="Medium" min="0" step="0.01" required>
                        <input type="number" name="sizes[large]" placeholder="Large" min="0" step="0.01" required>
                    </div>
                </div>
                <button type="submit">Add Product</button>
                <div class="prod-modal-msg"></div>
            </form>
        </div>
    </div>
    <!-- Edit Product Modal -->
    <div id="prodEditModal" class="prod-modal-bg">
        <div class="prod-modal-content">
            <span class="prod-modal-close" onclick="closeProdEditModal()">&times;</span>
            <h2>Edit Product</h2>
            <form id="prodEditForm" method="post" enctype="multipart/form-data" action="#">
                <input type="hidden" name="id" id="editProdId">
                <img id="editProdImgPreview" src="" style="width:60px;height:60px;object-fit:cover;border-radius:7px;border:1px solid #eee;margin-bottom:12px;display:none;">
                <input type="file" name="image" accept="image/*" style="margin-bottom:18px;">
                <input type="text" name="name" id="editProdName" placeholder="Product Name" required>
                <select name="category_id" id="editProdCategory" required style="margin-bottom:18px; padding:10px; border-radius:7px; border:1px solid #ccc; font-size:1.08rem; width:100%; font-family:'Georgia',serif;">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div style="width:100%;margin-bottom:18px;">
                    <label style="display:block;text-align:left;font-weight:600;margin-bottom:6px;">Prices:</label>
                    <div class="price-row">
                        <input type="number" name="sizes[small]" id="editProdSmall" placeholder="Small" min="0" step="0.01" required>
                        <input type="number" name="sizes[medium]" id="editProdMedium" placeholder="Medium" min="0" step="0.01" required>
                        <input type="number" name="sizes[large]" id="editProdLarge" placeholder="Large" min="0" step="0.01" required>
                    </div>
                </div>
                <button type="submit">Save Changes</button>
                <div class="prod-modal-msg"></div>
            </form>
        </div>
    </div>
    <!-- Delete Product Modal -->
    <div id="prodDeleteModal" class="prod-modal-bg">
        <div class="prod-modal-content">
            <span class="prod-modal-close" onclick="closeProdDeleteModal()">&times;</span>
            <h2>Delete Product</h2>
            <form id="prodDeleteForm" method="post" action="#">
                <input type="hidden" name="id" id="deleteProdId">
                <div class="delete-confirm-text">Are you sure you want to delete <span id="deleteProdName"></span>?</div>
                <button type="submit" style="background:#be1635;">Delete</button>
                <button type="button" onclick="closeProdDeleteModal()" style="background:#222; color: #fff">Cancel</button>
                <div class="prod-modal-msg"></div>
            </form>
        </div>
    </div>
    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>
    <script>
        <?php 
        error_log(print_r($products, true)); 
        error_log('Products count: ' . count($products));
        error_log('ProductSizes count: ' . count($productSizes));
        ?>
        <?php 
        // Create a copy of products without the image data for JSON encoding
        $productsForJson = [];
        if (isset($products) && is_array($products)) {
            foreach ($products as $product) {
                $productCopy = $product;
                // Remove the image data to avoid JSON encoding issues
                unset($productCopy['image']);
                $productsForJson[] = $productCopy;
            }
        }
        
        $productsJson = json_encode($productsForJson);
        $productSizesJson = json_encode(isset($productSizes) && is_array($productSizes) ? $productSizes : []);
        error_log('Products JSON: ' . $productsJson);
        error_log('ProductSizes JSON: ' . $productSizesJson);
        error_log('JSON last error: ' . json_last_error_msg());
        ?>
        window._products = <?php echo $productsJson; ?>;
        window._productSizes = <?php echo $productSizesJson; ?>;
    </script>
    <script>
        function openProdModal() {
            document.getElementById('prodModal').classList.add('show');
        }
        function closeProdModal() {
            document.getElementById('prodModal').classList.remove('show');
        }
        // Toast
        function showToast(msg, type) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.className = 'toast show ' + (type || '');
            setTimeout(() => { toast.className = 'toast'; }, 2200);
        }
        // Edit Modal
        function openProdEditModal(id) {
            // Use global variables set above
            const products = window._products || [];
            const sizes = window._productSizes || {};
            
            // Safety check to ensure products is an array
            if (!Array.isArray(products)) {
                console.error('Products is not an array:', products);
                showToast('Error loading product data', 'error');
                return;
            }
            
            // Convert id to number for comparison
            const searchId = parseInt(id);
            const prod = products.find(p => parseInt(p.id) === searchId);
            
            if (!prod) {
                console.error('Product not found for ID:', id);
                showToast('Product not found', 'error');
                return;
            }
            document.getElementById('editProdId').value = prod.id;
            document.getElementById('editProdName').value = prod.name;
            document.getElementById('editProdCategory').value = prod.category_id;
            document.getElementById('editProdSmall').value = sizes[prod.id]?.small || '';
            document.getElementById('editProdMedium').value = sizes[prod.id]?.medium || '';
            document.getElementById('editProdLarge').value = sizes[prod.id]?.large || '';
            // Try to get the image from the table row
            const rowImg = document.querySelector(`button[onclick="openProdEditModal(${prod.id})"]`)
                ?.closest('tr')
                ?.querySelector('img.prod-img');
            const imgPrev = document.getElementById('editProdImgPreview');
            if (rowImg && rowImg.src) {
                imgPrev.src = rowImg.src;
                imgPrev.style.display = 'block';
            } else {
                imgPrev.style.display = 'none';
            }
            document.getElementById('prodEditModal').classList.add('show');
        }
        function closeProdEditModal() {
            document.getElementById('prodEditModal').classList.remove('show');
        }
        // Delete Modal
        function openProdDeleteModal(id, name) {
            document.getElementById('deleteProdId').value = id;
            document.getElementById('deleteProdName').textContent = name;
            document.getElementById('prodDeleteModal').classList.add('show');
        }
        function closeProdDeleteModal() {
            document.getElementById('prodDeleteModal').classList.remove('show');
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('prodAddForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'add');
                fetch('includes/process_product.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    const msg = form.querySelector('.prod-modal-msg');
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) {
                        closeProdModal();
                        showToast(res.message, 'success');
                        setTimeout(() => { window.location.reload(); }, 900);
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(() => showToast('Network error.', 'error'));
            });
            // Edit
            document.getElementById('prodEditForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'edit');
                fetch('includes/process_product.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    const msg = form.querySelector('.prod-modal-msg');
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) {
                        closeProdEditModal();
                        showToast(res.message, 'success');
                        setTimeout(() => { window.location.reload(); }, 900);
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(() => showToast('Network error.', 'error'));
            });
            // Delete
            document.getElementById('prodDeleteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'delete');
                fetch('includes/process_product.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    const msg = form.querySelector('.prod-modal-msg');
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) {
                        closeProdDeleteModal();
                        showToast(res.message, 'success');
                        setTimeout(() => { window.location.reload(); }, 900);
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(() => showToast('Network error.', 'error'));
            });
            // Close modals on background click
            document.getElementById('prodModal').addEventListener('click', function(e) { if (e.target === this) closeProdModal(); });
            document.getElementById('prodEditModal').addEventListener('click', function(e) { if (e.target === this) closeProdEditModal(); });
            document.getElementById('prodDeleteModal').addEventListener('click', function(e) { if (e.target === this) closeProdDeleteModal(); });
        });
    </script>
    <script>
        // Product filter and pagination logic
        const prodRowsPerPage = 10;
        const prodTable = document.getElementById('prodTable');
        const prodPagination = document.getElementById('prodPagination');
        const prodFilterInput = document.getElementById('prodFilterInput');
        const prodCategoryFilter = document.getElementById('prodCategoryFilter');
        function prodApplyFilters() {
            const nameVal = prodFilterInput.value.toLowerCase();
            const catVal = prodCategoryFilter.value;
            Array.from(prodTable.rows).slice(1).forEach(row => {
                const prodName = row.children[1]?.textContent.toLowerCase() || '';
                const prodCat = row.children[2]?.textContent || '';
                const catId = row.getAttribute('data-cat-id');
                let show = true;
                if (nameVal && !prodName.includes(nameVal)) show = false;
                if (catVal !== 'all' && catId !== catVal) show = false;
                if (show) {
                    row.removeAttribute('data-filtered');
                } else {
                    row.setAttribute('data-filtered', '1');
                }
            });
        }
        function prodGetVisibleRows() {
            return Array.from(prodTable.rows).slice(1).filter(row => !row.hasAttribute('data-filtered'));
        }
        function prodShowPage(page) {
            const rows = prodGetVisibleRows();
            const total = rows.length;
            const totalPages = Math.ceil(total / prodRowsPerPage);
            page = Math.max(1, Math.min(page, totalPages));
            let shown = 0;
            Array.from(prodTable.rows).slice(1).forEach(row => {
                if (row.hasAttribute('data-filtered')) {
                    row.style.display = 'none';
                } else {
                    if (shown >= (page-1)*prodRowsPerPage && shown < page*prodRowsPerPage) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                    shown++;
                }
            });
            // Pagination bar
            prodPagination.innerHTML = '';
            if (totalPages > 1) {
                const prev = document.createElement('button');
                prev.textContent = 'Previous';
                prev.disabled = page === 1;
                prev.onclick = () => prodShowPage(page-1);
                prev.style.cssText = 'padding:6px 14px;border-radius:5px;border:1px solid #ccc;background:#fff;color:#be1635;font-weight:600;cursor:pointer;margin-right:4px;';
                prodPagination.appendChild(prev);
                for (let p=1; p<=totalPages; p++) {
                    const btn = document.createElement('button');
                    btn.textContent = p;
                    btn.disabled = p === page;
                    btn.onclick = () => prodShowPage(p);
                    btn.style.cssText = 'padding:6px 12px;border-radius:5px;border:1px solid #ccc;background:'+(p===page?'#be1635':'#fff')+';color:'+(p===page?'#fff':'#be1635')+';font-weight:600;cursor:pointer;margin-right:2px;';
                    prodPagination.appendChild(btn);
                }
                const next = document.createElement('button');
                next.textContent = 'Next';
                next.disabled = page === totalPages;
                next.onclick = () => prodShowPage(page+1);
                next.style.cssText = 'padding:6px 14px;border-radius:5px;border:1px solid #ccc;background:#fff;color:#be1635;font-weight:600;cursor:pointer;margin-left:4px;';
                prodPagination.appendChild(next);
            }
        }
        function prodResetPagination() {
            prodShowPage(1);
        }
        prodFilterInput.addEventListener('input', () => { prodApplyFilters(); prodResetPagination(); });
        prodCategoryFilter.addEventListener('change', () => { prodApplyFilters(); prodResetPagination(); });
        // Add data-cat-id to each row for filtering
        Array.from(prodTable.rows).slice(1).forEach(row => {
            row.setAttribute('data-cat-id', row.children[2]?.getAttribute('data-cat-id') || '');
        });
        // Initial filter and pagination
        prodApplyFilters();
        prodResetPagination();
    </script>
</body>
</html> 