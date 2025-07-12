<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../includes/connection.php';

// Handle fetch categories
$stmt = $conn->query('SELECT * FROM categories ORDER BY created_at DESC');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin Dashboard</title>
    <style>
        body, html { margin: 0; padding: 0; font-family: 'Georgia', serif; background: #f6f6f6; }
        .admin-main { margin-left: 220px; padding-top: 60px; min-height: 100vh; }
        .admin-content { padding: 32px 40px; }
        .cat-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .cat-table th, .cat-table td { padding: 14px 16px; border-bottom: 1px solid #eee; text-align: left; }
        .cat-table th { background: #be1635; color: #fff; }
        .cat-table tr:last-child td { border-bottom: none; }
        .cat-actions button { margin-right: 8px; padding: 6px 14px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .cat-actions .edit-btn { background: #222; color: #fff; }
        .cat-actions .delete-btn { background: #be1635; color: #fff; }
        .add-cat-btn { background: #be1635; color: #fff; border: none; border-radius: 4px; padding: 10px 22px; font-size: 1rem; font-weight: 700; margin-bottom: 18px; cursor: pointer; }
        .add-cat-btn:hover { background: #a0122b; }
    </style>
    <style>
        .cat-modal-bg {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(24,23,28,0.7);
            align-items: center;
            justify-content: center;
        }
        .cat-modal-bg.show { display: flex; }
        .cat-modal-content {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(190,22,53,0.18);
            padding: 38px 32px 32px 32px;
            min-width: 320px;
            max-width: 95vw;
            width: 400px;
            text-align: center;
            position: relative;
            animation: catModalFadeIn 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        @keyframes catModalFadeIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .cat-modal-content h2 {
            font-size: 2rem;
            font-family: 'Georgia', serif;
            font-weight: 700;
            margin-bottom: 28px;
            margin-top: 0;
            letter-spacing: 1px;
            color: #be1635;
        }
        .cat-modal-content p {
            font-size: 1.1rem;
            color: #222;
            margin-bottom: 28px;
            margin-top: 0;
        }
        .cat-modal-close {
            position: absolute;
            top: 18px; right: 22px;
            font-size: 1.7rem;
            color: #be1635;
            cursor: pointer;
            font-weight: 700;
            transition: color 0.18s;
        }
        .cat-modal-close:hover { color: #a0122b; }
        #catAddForm, #catEditForm, #catDeleteForm {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #catAddForm input, #catEditForm input {
            width: 100%;
            margin-bottom: 22px;
            padding: 12px;
            border-radius: 7px;
            border: 1px solid #ccc;
            font-size: 1.08rem;
            font-family: 'Georgia', serif;
            box-sizing: border-box;
        }
        #catAddForm button, #catEditForm button {
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
            transition: background 0.18s;
        }
        #catAddForm button:hover, #catEditForm button:hover { background: #a0122b; }
        #catDeleteForm button[type="submit"] {
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
            transition: background 0.18s;
            margin-top: 10px;
        }
        #catDeleteForm button[type="submit"]:hover { background: #a0122b; }
        #catDeleteForm button[type="button"] {
            width: 100%;
            background: #eee;
            color: #222;
            border: none;
            border-radius: 7px;
            padding: 13px 0;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 0;
            margin-top: 0;
            transition: background 0.18s;
        }
        #catDeleteForm button[type="button"]:hover { background: #ddd; }
        .cat-modal-msg {
            font-size: 1rem;
            font-weight: 600;
            margin-top: 6px;
            min-height: 22px;
        }
        #deleteCatName {
            color: #be1635;
            font-weight: 700;
            font-size: 1.1em;
        }
        </style>
        <style>
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
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-content">
            <h1>Manage Categories</h1>
            <input type="text" id="catFilterInput" placeholder="Filter categories..." style="margin-bottom:18px;padding:10px 14px;border-radius:6px;border:1px solid #ccc;font-size:1.05rem;width:320px;max-width:100%;font-family:'Georgia',serif;">
            <button class="add-cat-btn" onclick="openCatModal()">+ Add Category</button>
            <!-- Add Category Modal -->
            <div id="catModal" class="cat-modal-bg">
                <div class="cat-modal-content">
                    <span class="cat-modal-close" onclick="closeCatModal()">&times;</span>
                    <h2>Add Category</h2>
                    <form id="catAddForm" method="post" action="#">
                        <input type="text" name="name" placeholder="Category Name" required>
                        <button type="submit">Add Category</button>
                        <div class="cat-modal-msg"></div>
                    </form>
                </div>
            </div>
            <!-- Edit Category Modal -->
            <div id="catEditModal" class="cat-modal-bg">
                <div class="cat-modal-content">
                    <span class="cat-modal-close" onclick="closeCatEditModal()">&times;</span>
                    <h2>Edit Category</h2>
                    <form id="catEditForm" method="post" action="#">
                        <input type="hidden" name="id" id="editCatId">
                        <input type="text" name="name" id="editCatName" placeholder="Category Name" required>
                        <button type="submit">Save Changes</button>
                        <div class="cat-modal-msg"></div>
                    </form>
                </div>
            </div>
            <!-- Delete Category Modal -->
            <div id="catDeleteModal" class="cat-modal-bg">
                <div class="cat-modal-content">
                    <span class="cat-modal-close" onclick="closeCatDeleteModal()">&times;</span>
                    <h2>Delete Category</h2>
                    <form id="catDeleteForm" method="post" action="#">
                        <input type="hidden" name="id" id="deleteCatId">
                        <p>Are you sure you want to delete <span id="deleteCatName"></span>?</p>
                        <button type="submit" style="background:#be1635;">Delete</button>
                        <button type="button" onclick="closeCatDeleteModal()" style="background:#222; color: #fff">Cancel</button>
                        <div class="cat-modal-msg"></div>
                    </form>
                </div>
            </div>
            <!-- Toast Notification -->
            <div id="toast" class="toast"></div>
            <table class="cat-table" id="catTable">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['id']) ?></td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= htmlspecialchars($cat['created_at']) ?></td>
                    <td class="cat-actions">
                        <button class="edit-btn" onclick="openCatEditModal(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>')">Edit</button>
                        <button class="delete-btn" onclick="openCatDeleteModal(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>')">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div id="catPagination" style="margin-top:18px;display:flex;justify-content:center;gap:8px;"></div>
        </div>
    </div>
    <script>
    // Toast
    function showToast(msg, type) {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.className = 'toast show ' + (type || '');
        setTimeout(() => { toast.className = 'toast'; }, 2200);
    }
    // Add Modal
    function openCatModal() {
        document.getElementById('catModal').classList.add('show');
    }
    function closeCatModal() {
        document.getElementById('catModal').classList.remove('show');
    }
    // Edit Modal
    function openCatEditModal(id, name) {
        document.getElementById('editCatId').value = id;
        document.getElementById('editCatName').value = name;
        document.getElementById('catEditModal').classList.add('show');
    }
    function closeCatEditModal() {
        document.getElementById('catEditModal').classList.remove('show');
    }
    // Delete Modal
    function openCatDeleteModal(id, name) {
        document.getElementById('deleteCatId').value = id;
        document.getElementById('deleteCatName').textContent = name;
        document.getElementById('catDeleteModal').classList.add('show');
    }
    function closeCatDeleteModal() {
        document.getElementById('catDeleteModal').classList.remove('show');
    }
    document.addEventListener('DOMContentLoaded', function() {
        // Add
        document.getElementById('catAddForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const data = new FormData(form);
            data.append('action', 'add');
            fetch('includes/process_category.php', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                const msg = form.querySelector('.cat-modal-msg');
                msg.textContent = res.message;
                msg.style.color = res.success ? '#27ae60' : '#be1635';
                if (res.success) {
                    closeCatModal();
                    showToast(res.message, 'success');
                    setTimeout(() => { window.location.reload(); }, 900);
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Network error.', 'error'));
        });
        // Edit
        document.getElementById('catEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const data = new FormData(form);
            data.append('action', 'edit');
            fetch('includes/process_category.php', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                const msg = form.querySelector('.cat-modal-msg');
                msg.textContent = res.message;
                msg.style.color = res.success ? '#27ae60' : '#be1635';
                if (res.success) {
                    closeCatEditModal();
                    showToast(res.message, 'success');
                    setTimeout(() => { window.location.reload(); }, 900);
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Network error.', 'error'));
        });
        // Delete
        document.getElementById('catDeleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const data = new FormData(form);
            data.append('action', 'delete');
            fetch('includes/process_category.php', {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                const msg = form.querySelector('.cat-modal-msg');
                msg.textContent = res.message;
                msg.style.color = res.success ? '#27ae60' : '#be1635';
                if (res.success) {
                    closeCatDeleteModal();
                    showToast(res.message, 'success');
                    setTimeout(() => { window.location.reload(); }, 900);
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Network error.', 'error'));
        });
        // Close modals on background click
        document.getElementById('catModal').addEventListener('click', function(e) { if (e.target === this) closeCatModal(); });
        document.getElementById('catEditModal').addEventListener('click', function(e) { if (e.target === this) closeCatEditModal(); });
        document.getElementById('catDeleteModal').addEventListener('click', function(e) { if (e.target === this) closeCatDeleteModal(); });
    });
    // Category filter logic
    document.getElementById('catFilterInput').addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.cat-table tr').forEach((row, i) => {
            if (i === 0) return; // skip header
            const name = row.children[1]?.textContent.toLowerCase() || '';
            if (name.includes(val)) {
                row.removeAttribute('data-filtered');
            } else {
                row.setAttribute('data-filtered', '1');
            }
        });
        resetPagination(); // Re-apply pagination after filtering
    });
    // Pagination logic
    const rowsPerPage = 10;
    const table = document.getElementById('catTable');
    const pagination = document.getElementById('catPagination');
    function getVisibleRows() {
        return Array.from(table.rows).slice(1).filter(row => !row.hasAttribute('data-filtered'));
    }
    function showPage(page) {
        const rows = getVisibleRows();
        const total = rows.length;
        const totalPages = Math.ceil(total / rowsPerPage);
        page = Math.max(1, Math.min(page, totalPages));
        let shown = 0;
        Array.from(table.rows).slice(1).forEach(row => {
            if (row.hasAttribute('data-filtered')) {
                row.style.display = 'none';
            } else {
                if (shown >= (page-1)*rowsPerPage && shown < page*rowsPerPage) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
                shown++;
            }
        });
        // Pagination bar
        pagination.innerHTML = '';
        if (totalPages > 1) {
            const prev = document.createElement('button');
            prev.textContent = 'Previous';
            prev.disabled = page === 1;
            prev.onclick = () => showPage(page-1);
            prev.style.cssText = 'padding:6px 14px;border-radius:5px;border:1px solid #ccc;background:#fff;color:#be1635;font-weight:600;cursor:pointer;margin-right:4px;';
            pagination.appendChild(prev);
            for (let p=1; p<=totalPages; p++) {
                const btn = document.createElement('button');
                btn.textContent = p;
                btn.disabled = p === page;
                btn.onclick = () => showPage(p);
                btn.style.cssText = 'padding:6px 12px;border-radius:5px;border:1px solid #ccc;background:'+(p===page?'#be1635':'#fff')+';color:'+(p===page?'#fff':'#be1635')+';font-weight:600;cursor:pointer;margin-right:2px;';
                pagination.appendChild(btn);
            }
            const next = document.createElement('button');
            next.textContent = 'Next';
            next.disabled = page === totalPages;
            next.onclick = () => showPage(page+1);
            next.style.cssText = 'padding:6px 14px;border-radius:5px;border:1px solid #ccc;background:#fff;color:#be1635;font-weight:600;cursor:pointer;margin-left:4px;';
            pagination.appendChild(next);
        }
    }
    function resetPagination() {
        showPage(1);
    }
    // Re-apply pagination after filtering
    // Initial pagination
    resetPagination();
    </script>
</body>
</html> 