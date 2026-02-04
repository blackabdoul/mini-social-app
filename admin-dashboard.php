<?php
require_once "admin-check.php"; // Restricts access to admins only

// Get statistics
$statsQuery = $pdo->query("
    SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified_users,
        SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as unverified_users,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users
    FROM users
");
$stats = $statsQuery->fetch(PDO::FETCH_ASSOC);

// Get all users
$usersQuery = $pdo->query("
    SELECT id, email, full_name, role, is_verified, created_at, updated_at 
    FROM users 
    ORDER BY created_at DESC
");
$users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);

// Get success/error messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-dashboard-styles.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>⚙️ Admin Dashboard</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_users'] ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['verified_users'] ?></div>
                <div class="stat-label">Verified Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['unverified_users'] ?></div>
                <div class="stat-label">Unverified Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['admin_users'] ?></div>
                <div class="stat-label">Admin Users</div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-card">
            <h2>All Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['full_name'] ?? 'N/A') ?></td>
                        <td>
                            <span class="badge badge-<?= $u['role'] ?>">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $u['is_verified'] ? 'verified' : 'unverified' ?>">
                                <?= $u['is_verified'] ? 'VERIFIED' : 'UNVERIFIED' ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td>
                             <?php  if ($u['id'] !== $_SESSION["id_user"]): // Can't delete yourself ?> 
                                <form method="POST" action="admin-actions.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                                
                                <form method="POST" action="admin-actions.php" style="display: inline;">
                                    <input type="hidden" name="action" value="toggle_role">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <?= $u['role'] === 'admin' ? 'Make User' : 'Make Admin' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: var(--text-secondary); font-size: 12px;">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Theme Toggle Button -->
    <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn">
        🌙 Dark Mode
    </button>

    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateButton(savedTheme);

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateButton(newTheme);
        }

        function updateButton(theme) {
            const btn = document.getElementById('themeBtn');
            btn.textContent = theme === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
        }
    </script>
</body>
</html>