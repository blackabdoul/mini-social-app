<?php
require_once "admin-check.php";

$statsQuery = $pdo->query("
    SELECT
        COUNT(*) as total_users,
        SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified_users,
        SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as unverified_users,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users
    FROM users
");
$stats = $statsQuery->fetch(PDO::FETCH_ASSOC);

$usersQuery = $pdo->query("
    SELECT id, email, full_name, role, is_verified, created_at, updated_at
    FROM users
    ORDER BY created_at DESC
");
$users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-dashboard-styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .header h1 {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header h1 svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
        }
        .nav-links a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .nav-links a svg {
            width: 15px;
            height: 15px;
            stroke: currentColor;
        }
        .btn svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            vertical-align: middle;
            margin-right: 4px;
        }
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .theme-toggle svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i data-lucide="shield-check"></i> Admin Dashboard
            </h1>
            <div class="nav-links">
                <a href="dashboard.php">
                    <i data-lucide="layout-dashboard"></i> Dashboard
                </a>
                <a href="profile.php">
                    <i data-lucide="user"></i> Profile
                </a>
                <a href="logout.php">
                    <i data-lucide="log-out"></i> Logout
                </a>
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
                            <?php if ($u['id'] !== $_SESSION["id_user"]): ?>
                                <form method="POST" action="admin-actions.php" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                    <input type="hidden" name="action"  value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i data-lucide="trash-2"></i> Delete
                                    </button>
                                </form>
                                <form method="POST" action="admin-actions.php" style="display:inline;">
                                    <input type="hidden" name="action"  value="toggle_role">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i data-lucide="<?= $u['role'] === 'admin' ? 'user' : 'shield' ?>"></i>
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

    <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn">
        <i data-lucide="moon" id="themeIcon"></i>
        <span id="themeLabel">Dark Mode</span>
    </button>

    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        function toggleTheme() {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        }
        function updateThemeIcon(theme) {
            document.getElementById('themeIcon').setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
            document.getElementById('themeLabel').textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
            lucide.createIcons({ attrs: { 'stroke-width': 2 } });
        }

        lucide.createIcons({ attrs: { 'stroke-width': 2 } });
        updateThemeIcon(savedTheme);
    </script>
</body>
</html>