<?php
session_start();
if(!isset($_SESSION["id_user"])){
    header("Location: index.php");
    exit();
}

require_once "config.php";

$stmt = $pdo->query("
    SELECT p.id, p.content, p.image_path, p.created_at,
           u.id AS user_id, u.full_name, u.email
    FROM posts p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniSocial — Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --accent:       #667eea;
            --accent-dark:  #5568d3;
            --bg:           #f0f2f8;
            --surface:      #ffffff;
            --surface2:     #f7f8fc;
            --border:       #e4e6ef;
            --text:         #1a1d2e;
            --muted:        #6b7280;
            --danger:       #ef4444;
            --success-bg:   #dcfce7;
            --success-text: #166534;
            --error-bg:     #fee2e2;
            --error-text:   #991b1b;
            --shadow:       0 1px 3px rgba(0,0,0,0.08), 0 4px 16px rgba(102,126,234,0.06);
            --radius:       12px;
        }
        [data-theme="dark"] {
            --bg:      #0f1117;
            --surface: #1a1d2e;
            --surface2:#232640;
            --border:  #2e3152;
            --text:    #e8eaf6;
            --muted:   #8b90b8;
            --shadow:  0 1px 3px rgba(0,0,0,0.3), 0 4px 16px rgba(0,0,0,0.2);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }
        /* NAVBAR */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            transition: background 0.3s, border-color 0.3s;
        }
        .nav-inner {
            max-width: 680px; margin: 0 auto;
            padding: 0 20px; height: 58px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .nav-brand {
            font-family: 'DM Serif Display', serif;
            font-size: 22px; color: var(--accent);
            letter-spacing: -0.5px; text-decoration: none;
        }
        .nav-actions { display:flex; align-items:center; gap:8px; }
        .nav-btn {
            background:none; border:none; cursor:pointer;
            color:var(--muted); padding:8px; border-radius:8px;
            transition: background 0.2s, color 0.2s;
            text-decoration:none; display:flex; align-items:center;
        }
        .nav-btn:hover { background:var(--surface2); color:var(--text); }
        .nav-btn svg { width:20px; height:20px; stroke:currentColor; }
        .nav-avatar {
            width:34px; height:34px; border-radius:50%;
            background: linear-gradient(135deg, var(--accent), #764ba2);
            display:flex; align-items:center; justify-content:center;
            color:white; font-size:13px; font-weight:600;
            cursor:pointer; text-decoration:none;
        }
        /* TABS */
        .tabs-bar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            transition: background 0.3s, border-color 0.3s;
        }
        .tabs-inner {
            max-width:680px; margin:0 auto;
            padding:0 20px; display:flex; gap:4px;
        }
        .tab {
            padding:12px 18px; font-size:14px; font-weight:500;
            color:var(--muted); cursor:pointer; border:none;
            background:none; border-bottom:2px solid transparent;
            margin-bottom:-1px; transition: color 0.2s, border-color 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .tab:hover { color:var(--text); }
        .tab.active { color:var(--accent); border-bottom-color:var(--accent); }
        /* LAYOUT */
        .main { max-width:680px; margin:0 auto; padding:24px 20px 60px; }
        .tab-panel { display:none; }
        .tab-panel.active { display:block; }
        /* ALERTS */
        .alert { padding:12px 16px; border-radius:var(--radius); font-size:14px; margin-bottom:16px; }
        .alert-success { background:var(--success-bg); color:var(--success-text); }
        .alert-error   { background:var(--error-bg);   color:var(--error-text); }
        /* COMPOSE */
        .compose-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:var(--radius); padding:20px; margin-bottom:20px;
            box-shadow:var(--shadow); transition: background 0.3s, border-color 0.3s;
        }
        .compose-top { display:flex; gap:12px; align-items:flex-start; margin-bottom:14px; }
        .compose-avatar {
            width:40px; height:40px; border-radius:50%;
            background:linear-gradient(135deg, var(--accent), #764ba2);
            display:flex; align-items:center; justify-content:center;
            color:white; font-size:14px; font-weight:600; flex-shrink:0;
        }
        .compose-textarea {
            flex:1; border:none; background:none; resize:none;
            font-family:'DM Sans',sans-serif; font-size:15px;
            color:var(--text); outline:none; min-height:70px; line-height:1.5;
        }
        .compose-textarea::placeholder { color:var(--muted); }
        .compose-footer {
            display:flex; align-items:center; justify-content:space-between;
            padding-top:12px; border-top:1px solid var(--border);
        }
        .compose-actions { display:flex; align-items:center; gap:8px; }
        .img-label {
            display:flex; align-items:center; gap:6px;
            padding:7px 14px; border-radius:8px; cursor:pointer;
            font-size:13px; font-weight:500; color:var(--muted);
            border:1px solid var(--border); transition:all 0.2s;
        }
        .img-label svg { width:16px; height:16px; stroke:currentColor; }
        .img-label:hover { color:var(--accent); border-color:var(--accent); }
        .img-label input { display:none; }
        #img-name { font-size:12px; color:var(--muted); max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .btn-post {
            background:var(--accent); color:white; border:none;
            padding:9px 22px; border-radius:8px; font-size:14px;
            font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-post:hover { background:var(--accent-dark); }
        .btn-post:active { transform:scale(0.98); }
        /* POST CARD */
        .post-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:var(--radius); margin-bottom:16px;
            box-shadow:var(--shadow); overflow:hidden;
            transition: background 0.3s, border-color 0.3s;
            animation: fadeUp 0.3s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(8px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .post-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:16px 18px 10px;
        }
        .post-author { display:flex; align-items:center; gap:10px; }
        .post-avatar {
            width:38px; height:38px; border-radius:50%;
            background:linear-gradient(135deg, var(--accent), #764ba2);
            display:flex; align-items:center; justify-content:center;
            color:white; font-size:13px; font-weight:600; flex-shrink:0;
        }
        .post-name  { font-size:14px; font-weight:600; color:var(--text); }
        .post-time  { font-size:12px; color:var(--muted); }
        .post-delete {
            background:none; border:none; cursor:pointer;
            color:var(--muted); padding:6px; display:flex; align-items:center;
            border-radius:6px; transition: background 0.2s, color 0.2s;
        }
        .post-delete svg { width:17px; height:17px; stroke:currentColor; }
        .post-delete:hover { background:var(--error-bg); color:var(--danger); }
        .post-content {
            padding:2px 18px 14px; font-size:15px; line-height:1.6;
            color:var(--text); white-space:pre-wrap; word-break:break-word;
        }
        .post-image {
            width:100%; max-height:480px; object-fit:cover;
            display:block; border-top:1px solid var(--border);
        }
        /* EMPTY STATE */
        .empty-state { text-align:center; padding:60px 20px; color:var(--muted); }
        .empty-state svg { width:48px; height:48px; stroke:var(--muted); margin-bottom:12px; }
        .empty-state p { font-size:15px; }
        /* ME TAB */
        .profile-summary {
            background:var(--surface); border:1px solid var(--border);
            border-radius:var(--radius); padding:30px;
            text-align:center; box-shadow:var(--shadow);
        }
        .ps-avatar {
            width:80px; height:80px; border-radius:50%;
            background:linear-gradient(135deg, var(--accent), #764ba2);
            display:flex; align-items:center; justify-content:center;
            color:white; font-size:28px; font-weight:600; margin:0 auto 16px;
        }
        .ps-name { font-family:'DM Serif Display',serif; font-size:22px; color:var(--text); margin-bottom:4px; }
        .ps-email { font-size:14px; color:var(--muted); margin-bottom:20px; }
        .ps-links { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
        .ps-link { padding:9px 20px; border-radius:8px; font-size:14px; font-weight:500; text-decoration:none; transition:all 0.2s; }
        .ps-link-primary { background:var(--accent); color:white; }
        .ps-link-primary:hover { background:var(--accent-dark); }
        .ps-link-danger { background:var(--error-bg); color:var(--danger); }
        .ps-link-danger:hover { background:var(--danger); color:white; }
        /* CHAR COUNTER */
        .char-count { font-size:12px; color:var(--muted); }
        .char-count.over { color:var(--danger); }
        @media (max-width:600px) {
            .nav-inner,.tabs-inner,.main { padding-left:14px; padding-right:14px; }
            .tab { padding:12px 12px; font-size:13px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-inner">
        <a class="nav-brand" href="dashboard.php">MiniSocial</a>
        <div class="nav-actions">
            <button class="nav-btn" onclick="toggleTheme()" id="themeBtn" title="Toggle theme">
                <i data-lucide="moon" id="themeIcon"></i>
            </button>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a class="nav-btn" href="admin-dashboard.php" title="Admin panel">
                <i data-lucide="settings"></i>
            </a>
            <?php endif; ?>
            <a class="nav-avatar" href="profile.php" title="My profile">
                <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['user_email'], 0, 1)) ?>
            </a>
        </div>
    </div>
</nav>

<div class="tabs-bar">
    <div class="tabs-inner">
        <button class="tab active" onclick="switchTab('feed', this)">Feed</button>
        <button class="tab" onclick="switchTab('create', this)">New Post</button>
        <button class="tab" onclick="switchTab('me', this)">Me</button>
    </div>
</div>

<main class="main">

    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- FEED -->
    <div class="tab-panel active" id="tab-feed">
        <?php if(empty($posts)): ?>
            <div class="empty-state">
                <i data-lucide="pen-line"></i>
                <p>No posts yet. Be the first to share something.</p>
            </div>
        <?php else: ?>
            <?php foreach($posts as $i => $post): ?>
            <article class="post-card" style="animation-delay:<?= $i * 0.05 ?>s">
                <div class="post-header">
                    <div class="post-author">
                        <div class="post-avatar">
                            <?= strtoupper(substr($post['full_name'] ?: $post['email'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="post-name">
                                <?= htmlspecialchars($post['full_name'] ?: explode('@', $post['email'])[0]) ?>
                            </div>
                            <div class="post-time">
                                <?= date('M j, Y · g:i a', strtotime($post['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php if($_SESSION['id_user'] == $post['user_id'] || ($_SESSION['user_role'] ?? '') === 'admin'): ?>
                    <form method="POST" action="postback.php" onsubmit="return confirm('Delete this post?')">
                        <input type="hidden" name="action"  value="delete_post">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" class="post-delete" title="Delete">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php if($post['content']): ?>
                <div class="post-content"><?= htmlspecialchars($post['content']) ?></div>
                <?php endif; ?>
                <?php if($post['image_path']): ?>
                <img class="post-image" src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post image" loading="lazy">
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- CREATE POST -->
    <div class="tab-panel" id="tab-create">
        <div class="compose-card">
            <form method="POST" action="postback.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_post">
                <div class="compose-top">
                    <div class="compose-avatar">
                        <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['user_email'], 0, 1)) ?>
                    </div>
                    <textarea
                        class="compose-textarea"
                        name="content"
                        id="postContent"
                        placeholder="What's on your mind?"
                        maxlength="1000"
                        oninput="updateCount(this)"
                    ></textarea>
                </div>
                <div class="compose-footer">
                    <div class="compose-actions">
                        <label class="img-label">
                            <i data-lucide="image"></i> Photo
                            <input type="file" name="image" accept="image/*" onchange="showFileName(this)">
                        </label>
                        <span id="img-name"></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span class="char-count" id="charCount">0 / 1000</span>
                        <button type="submit" class="btn-post">Post</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ME -->
    <div class="tab-panel" id="tab-me">
        <div class="profile-summary">
            <div class="ps-avatar">
                <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['user_email'], 0, 1)) ?>
            </div>
            <div class="ps-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'No name set') ?></div>
            <div class="ps-email"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
            <div class="ps-links">
                <a href="profile.php" class="ps-link ps-link-primary">Edit Profile</a>
                <a href="logout.php"  class="ps-link ps-link-danger">Log out</a>
            </div>
        </div>
    </div>

</main>

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
        const icon = document.getElementById('themeIcon');
        icon.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
        lucide.createIcons();
    }
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }
    function updateCount(el) {
        const n = el.value.length;
        const c = document.getElementById('charCount');
        c.textContent = n + ' / 1000';
        c.classList.toggle('over', n >= 950);
    }
    function showFileName(input) {
        document.getElementById('img-name').textContent = input.files[0]?.name || '';
    }
    <?php if($success): ?>
    switchTab('feed', document.querySelectorAll('.tab')[0]);
    <?php endif; ?>

    lucide.createIcons({ attrs: { 'stroke-width': 2 } });
    updateThemeIcon(savedTheme);
</script>
</body>
</html>