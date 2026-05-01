<?php
session_start();
require_once "config.php";

if(!isset($_SESSION["id_user"])){
    header("Location: index.php");
    exit();
}

$userId = $_SESSION["id_user"];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->bindParam(":id", $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="profile-styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
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
        .nav-links a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .nav-links a svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
        }
        .btn svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            vertical-align: middle;
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>My Profile</h1>
            <div class="nav-links">
                <a href="dashboard.php">
                    <i data-lucide="layout-dashboard"></i> Dashboard
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

        <div class="profile-grid">
            <!-- Left Sidebar -->
            <div class="profile-card">
                <div class="avatar">
                    <?= strtoupper(substr($user['email'], 0, 1)) ?>
                </div>
                <div class="user-name"><?= htmlspecialchars($user['full_name'] ?? 'User') ?></div>
                <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                <div class="profile-stats">
                    <div class="stat-box">
                        <div class="stat-number"><?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></div>
                        <div class="stat-label">Member Since</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?= date('M d', strtotime($user['updated_at'] ?? $user['created_at'] ?? 'now')) ?></div>
                        <div class="stat-label">Last Updated</div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="info-card">
                <!-- Personal Information -->
                <div class="section">
                    <h2>Personal Information</h2>
                    <form method="POST" action="profileback.php">
                        <input type="hidden" name="action" value="update_info">
                        <div class="info-row">
                            <div class="info-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Enter your full name">
                            </div>
                            <div class="info-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+1234567890">
                            </div>
                        </div>
                        <div class="info-group">
                            <label>Email Address</label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background: #f8f9fa;">
                        </div>
                        <div class="info-group">
                            <label>Bio</label>
                            <textarea name="bio" placeholder="Tell us about yourself..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        <div class="info-row">
                            <div class="info-group">
                                <label>Location</label>
                                <input type="text" name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>" placeholder="City, Country">
                            </div>
                            <div class="info-group">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i> Save Changes
                        </button>
                    </form>
                </div>

                <div class="divider"></div>

                <!-- Change Password -->
                <div class="section">
                    <h2>Change Password</h2>
                    <form method="POST" action="profileback.php">
                        <input type="hidden" name="action" value="change_password">
                        <div class="info-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="info-row">
                            <div class="info-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" required>
                            </div>
                            <div class="info-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="lock"></i> Update Password
                        </button>
                    </form>
                </div>

                <div class="divider"></div>

                <!-- Danger Zone -->
                <div class="section">
                    <h2 style="color: #dc3545;">Danger Zone</h2>
                    <p style="color: #666; margin-bottom: 15px;">Once you delete your account, there is no going back. Please be certain.</p>
                    <form method="POST" action="profileback.php" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                        <input type="hidden" name="action" value="delete_account">
                        <button type="submit" class="btn btn-danger">
                            <i data-lucide="trash-2"></i> Delete My Account
                        </button>
                    </form>
                </div>
            </div>
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
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
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