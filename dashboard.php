<?php
session_start();
if(!isset($_SESSION["id_user"])){
    header("Location: index.php");
    exit();
}

require_once "config.php";

$userId = $_SESSION['id_user'];

// Fetch posts with like count, comment count, and current user's like status
$stmt = $pdo->prepare("
    SELECT p.id, p.content, p.image_path, p.created_at,
           u.id AS user_id, u.full_name, u.email,
           COUNT(DISTINCT l.id)                                       AS like_count,
           MAX(CASE WHEN l.user_id = :me THEN 1 ELSE 0 END)          AS liked_by_me,
           COUNT(DISTINCT c.id)                                       AS comment_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN likes    l ON l.post_id = p.id
    LEFT JOIN comments c ON c.post_id = p.id
    GROUP BY p.id, u.id
    ORDER BY p.created_at DESC
");
$stmt->bindParam(':me', $userId, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all comments for all posts in one query (avoids N+1)
$commentStmt = $pdo->query("
    SELECT c.id, c.post_id, c.content, c.created_at,
           u.id AS user_id, u.full_name, u.email
    FROM comments c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at ASC
");
$allComments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

// Group comments by post_id for easy lookup
$commentsByPost = [];
foreach ($allComments as $comment) {
    $commentsByPost[$comment['post_id']][] = $comment;
}

// Which post should have comments auto-opened after redirect
$openCommentsPostId = $_SESSION['open_comments'] ?? null;
unset($_SESSION['open_comments']);

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
    <link rel="stylesheet" href="dashboard.css">
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
                    <div class="post-actions-right">
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
                </div>
                <?php if($post['content']): ?>
                <div class="post-content"><?= htmlspecialchars($post['content']) ?></div>
                <?php endif; ?>
                <?php if($post['image_path']): ?>
                <img class="post-image" src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post image" loading="lazy">
                <?php endif; ?>
                <!-- LIKE + COMMENT BAR -->
                <div class="post-footer">
                    <button
                        class="like-btn <?= $post['liked_by_me'] ? 'liked' : '' ?>"
                        data-post-id="<?= $post['id'] ?>"
                        data-liked="<?= $post['liked_by_me'] ? '1' : '0' ?>"
                        data-count="<?= $post['like_count'] ?>"
                        onclick="toggleLike(this)"
                    >
                        <i data-lucide="heart"></i>
                        <span class="like-count"><?= $post['like_count'] ?></span>
                        <span class="like-word"><?= $post['like_count'] == 1 ? 'Like' : 'Likes' ?></span>
                    </button>
                    <button class="comment-btn" onclick="toggleComments(<?= $post['id'] ?>)">
                        <i data-lucide="message-circle"></i>
                        <span id="ccount-<?= $post['id'] ?>"><?= $post['comment_count'] ?></span>
                        <?= $post['comment_count'] == 1 ? 'Comment' : 'Comments' ?>
                    </button>
                </div>
                <!-- COMMENTS SECTION -->
                <div class="comments-section <?= $openCommentsPostId == $post['id'] ? 'open' : '' ?>" id="comments-<?= $post['id'] ?>">
                    <?php $postComments = $commentsByPost[$post['id']] ?? []; ?>
                    <?php if(empty($postComments)): ?>
                        <p class="no-comments">No comments yet. Be the first!</p>
                    <?php else: ?>
                        <?php foreach($postComments as $c): ?>
                        <div class="comment-item" id="comment-<?= $c['id'] ?>">
                            <div class="comment-avatar">
                                <?= strtoupper(substr($c['full_name'] ?: $c['email'], 0, 1)) ?>
                            </div>
                            <div class="comment-bubble">
                                <div class="comment-meta">
                                    <span class="comment-author">
                                        <?= htmlspecialchars($c['full_name'] ?: explode('@', $c['email'])[0]) ?>
                                    </span>
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <span class="comment-time">
                                            <?= date('M j · g:i a', strtotime($c['created_at'])) ?>
                                        </span>
                                        <?php if($userId == $c['user_id'] || ($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                        <form method="POST" action="commentback.php" onsubmit="return confirm('Delete comment?')">
                                            <input type="hidden" name="action"     value="delete_comment">
                                            <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="comment-delete" title="Delete">
                                                <i data-lucide="x"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="comment-text"><?= htmlspecialchars($c['content']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- ADD COMMENT FORM -->
                    <form class="comment-form" method="POST" action="commentback.php">
                        <input type="hidden" name="action"  value="create_comment">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input
                            type="text"
                            name="content"
                            class="comment-input"
                            placeholder="Write a comment..."
                            maxlength="500"
                            autocomplete="off"
                            required
                        >
                        <button type="submit" class="comment-submit">Send</button>
                    </form>
                </div>
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
                    <textarea class="compose-textarea" name="content" id="postContent" placeholder="What's on your mind?" maxlength="1000" oninput="updateCount(this)"></textarea>
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
        document.getElementById('themeIcon').setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
        lucide.createIcons({ attrs: { 'stroke-width': 2 } });
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

    // ── Optimistic like toggle ──────────────────────────────────
    async function toggleLike(btn) {
        const postId  = btn.dataset.postId;
        const liked   = btn.dataset.liked === '1';
        const count   = parseInt(btn.dataset.count);

        // Optimistic update — change UI immediately before server responds
        const newLiked = !liked;
        const newCount = newLiked ? count + 1 : count - 1;
        applyLikeState(btn, newLiked, newCount);

        try {
            const formData = new FormData();
            formData.append('post_id', postId);

            const res  = await fetch('likeback.php', { method: 'POST', body: formData });

            // likeback.php redirects — a redirect response means success
            // If fetch follows the redirect and gets back the dashboard HTML,
            // we just keep the optimistic state. Only roll back on network error.
            if (!res.ok && res.status !== 302) {
                // Server error — roll back
                applyLikeState(btn, liked, count);
            }
        } catch (err) {
            // Network error — roll back to original state
            applyLikeState(btn, liked, count);
        }
    }

    function applyLikeState(btn, liked, count) {
        btn.dataset.liked = liked ? '1' : '0';
        btn.dataset.count = count;
        btn.classList.toggle('liked', liked);
        btn.querySelector('.like-count').textContent = count;
        btn.querySelector('.like-word').textContent  = count === 1 ? 'Like' : 'Likes';
        // Re-render the heart icon so fill updates
        lucide.createIcons({ attrs: { 'stroke-width': 2 } });
    }

    // ── Toggle comments section ─────────────────────────────────
    function toggleComments(postId) {
        const section = document.getElementById('comments-' + postId);
        section.classList.toggle('open');
        if (section.classList.contains('open')) {
            // Focus the comment input when opening
            const input = section.querySelector('.comment-input');
            if (input) setTimeout(() => input.focus(), 50);
        }
    }
</script>
</body>
</html>