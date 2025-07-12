<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../includes/connection.php';

// Fetch users
$userStmt = $conn->query('SELECT id, fname, lname, email, role, created_at FROM users ORDER BY created_at DESC');
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html { margin: 0; padding: 0; font-family: 'Georgia', serif; background: #f6f6f6; }
        .logout-link {
            font-weight: 700;
        }
        .admin-main {
            margin-left: 270px;
            padding-top: 60px;
            min-height: 100vh;
        }
        .admin-content { padding: 32px 40px; }
        .user-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .user-table th, .user-table td { padding: 14px 16px; border-bottom: 1px solid #eee; text-align: left; }
        .user-table th { background: #be1635; color: #fff; }
        .user-table tr:last-child td { border-bottom: none; }
        .user-actions button { margin-right: 8px; padding: 6px 14px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .user-actions .edit-btn { background: #222; color: #fff; }
        .user-actions .delete-btn { background: #be1635; color: #fff; }
        .add-user-btn { background: #be1635; color: #fff; border: none; border-radius: 4px; padding: 10px 22px; font-size: 1rem; font-weight: 700; margin-bottom: 18px; cursor: pointer; }
        .add-user-btn:hover { background: #a0122b; }
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
        .user-modal-bg {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(24,23,28,0.7);
            align-items: center;
            justify-content: center;
        }
        .user-modal-bg.show { display: flex; }
        .user-modal-content {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(190,22,53,0.18), 0 2px 8px rgba(0,0,0,0.08);
            padding: 38px 32px 32px 32px;
            max-width: 420px;
            width: 96vw;
            min-width: 0;
            text-align: center;
            position: relative;
            animation: userModalFadeIn 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            border: 1.5px solid #f2d7dd;
        }
        @keyframes userModalFadeIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .user-modal-close {
            position: absolute;
            top: 18px; right: 22px;
            font-size: 1.7rem;
            color: #be1635;
            cursor: pointer;
            font-weight: 700;
            transition: color 0.18s;
        }
        .user-modal-close:hover { color: #a0122b; }
        .user-modal-content h2 {
            font-size: 2.1rem;
            font-family: 'Georgia', serif;
            font-weight: 800;
            margin-bottom: 28px;
            margin-top: 0;
            letter-spacing: 1px;
            color: #be1635;
            text-shadow: 0 2px 8px #f6cdd6;
        }
        #userAddForm, #userEditForm {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #userAddForm input, #userEditForm input, #userAddForm select, #userEditForm select {
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
        #userAddForm input:focus, #userEditForm input:focus, #userAddForm select:focus, #userEditForm select:focus {
            border: 1.5px solid #be1635;
            outline: none;
            background: #fff0f4;
        }
        #userAddForm .user-modal-msg, #userEditForm .user-modal-msg {
            min-height: 22px;
        }
        #userAddForm button, #userEditForm button {
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
        #userAddForm button:hover, #userEditForm button:hover {
            background: #a0122b;
            box-shadow: 0 4px 16px #f6cdd6;
        }
        .user-modal-msg {
            font-size: 1rem;
            font-weight: 600;
            margin-top: 6px;
            min-height: 22px;
        }
        .user-modal-content .delete-confirm-text {
            font-size: 1.13rem;
            color: #be1635;
            font-weight: 600;
            margin: 18px 0 24px 0;
            background: #fff0f4;
            border-radius: 7px;
            padding: 10px 0;
        }
        #userDeleteForm button[type="submit"] {
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
        #userDeleteForm button[type="submit"]:hover {
            background: #a0122b;
            box-shadow: 0 4px 16px #f6cdd6;
        }
        #userDeleteForm button[type="button"] {
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
        #userDeleteForm button[type="button"]:hover {
            background: #444;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-content">
            <h1>User Management</h1>
            <button class="add-user-btn" onclick="openUserModal()">+ Add User</button>
            <table class="user-table">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['fname']) ?><?php if ($_SESSION['user_id'] == $user['id']): ?><span style="color:#888; font-style:italic; font-size:0.98em;"> (You)</span><?php endif; ?></td>
                    <td><?= htmlspecialchars($user['lname']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td class="user-actions">
                        <button class="edit-btn" onclick="openUserEditModal(<?= $user['id'] ?>)">Edit</button>
                        <button class="delete-btn" onclick="openUserDeleteModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['fname'] . ' ' . $user['lname'])) ?>')">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <!-- Add User Modal -->
    <div id="userModal" class="user-modal-bg">
        <div class="user-modal-content">
            <span class="user-modal-close" onclick="closeUserModal()">&times;</span>
            <h2>Add User</h2>
            <form id="userAddForm" method="post" action="#">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <button type="submit">Add User</button>
                <div class="user-modal-msg"></div>
            </form>
        </div>
    </div>
    <!-- Edit User Modal -->
    <div id="userEditModal" class="user-modal-bg">
        <div class="user-modal-content">
            <span class="user-modal-close" onclick="closeUserEditModal()">&times;</span>
            <h2>Edit User</h2>
            <form id="userEditForm" method="post" action="#">
                <input type="hidden" name="id" id="editUserId">
                <input type="text" name="fname" id="editUserFName" placeholder="First Name" required>
                <input type="text" name="lname" id="editUserLName" placeholder="Last Name" required>
                <input type="email" name="email" id="editUserEmail" placeholder="Email" required>
                <input type="password" name="password" id="editUserPassword" placeholder="New Password (leave blank to keep current)">
                <select name="role" id="editUserRole" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <button type="submit">Save Changes</button>
                <div class="user-modal-msg"></div>
            </form>
        </div>
    </div>
    <!-- Delete User Modal -->
    <div id="userDeleteModal" class="user-modal-bg">
        <div class="user-modal-content">
            <span class="user-modal-close" onclick="closeUserDeleteModal()">&times;</span>
            <h2>Delete User</h2>
            <form id="userDeleteForm" method="post" action="#">
                <input type="hidden" name="id" id="deleteUserId">
                <div class="delete-confirm-text">Are you sure you want to delete <span id="deleteUserName"></span>?</div>
                <button type="submit">Delete</button>
                <button type="button" onclick="closeUserDeleteModal()">Cancel</button>
                <div class="user-modal-msg"></div>
            </form>
        </div>
    </div>
    <div id="toast" class="toast"></div>
    <script>
        window._users = <?php echo json_encode(isset($users) && is_array($users) ? $users : []); ?>;
    </script>
    <script>
        function openUserModal() {
            document.getElementById('userModal').classList.add('show');
        }
        function closeUserModal() {
            document.getElementById('userModal').classList.remove('show');
        }
        function openUserEditModal(id) {
            const users = window._users || [];
            const user = users.find(u => parseInt(u.id) === parseInt(id));
            if (!user) return showToast('User not found', 'error');
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editUserFName').value = user.fname;
            document.getElementById('editUserLName').value = user.lname;
            document.getElementById('editUserEmail').value = user.email;
            document.getElementById('editUserRole').value = user.role;
            document.getElementById('userEditModal').classList.add('show');
        }
        function closeUserEditModal() {
            document.getElementById('userEditModal').classList.remove('show');
        }
        function openUserDeleteModal(id, username) {
            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteUserName').textContent = username;
            document.getElementById('userDeleteModal').classList.add('show');
        }
        function closeUserDeleteModal() {
            document.getElementById('userDeleteModal').classList.remove('show');
        }
        // Toast
        function showToast(msg, type) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.className = 'toast show ' + (type || '');
            setTimeout(() => { toast.className = 'toast'; }, 2200);
        }
        // Form submissions
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('userAddForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'add');
                fetch('includes/process_user.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    const msg = form.querySelector('.user-modal-msg');
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) {
                        closeUserModal();
                        showToast(res.message, 'success');
                        setTimeout(() => { window.location.reload(); }, 900);
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(() => showToast('Network error.', 'error'));
            });
            document.getElementById('userEditForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'edit');
                fetch('includes/process_user.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    const msg = form.querySelector('.user-modal-msg');
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) {
                        closeUserEditModal();
                        showToast(res.message, 'success');
                        setTimeout(() => { window.location.reload(); }, 900);
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(() => showToast('Network error.', 'error'));
            });
            document.getElementById('userDeleteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'delete');
                fetch('includes/process_user.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    const msg = form.querySelector('.user-modal-msg');
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) {
                        closeUserDeleteModal();
                        showToast(res.message, 'success');
                        setTimeout(() => { window.location.reload(); }, 900);
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(() => showToast('Network error.', 'error'));
            });
            // Close modals on background click
            document.getElementById('userModal').addEventListener('click', function(e) { if (e.target === this) closeUserModal(); });
            document.getElementById('userEditModal').addEventListener('click', function(e) { if (e.target === this) closeUserEditModal(); });
            document.getElementById('userDeleteModal').addEventListener('click', function(e) { if (e.target === this) closeUserDeleteModal(); });
        });
    </script>
</body>
</html> 