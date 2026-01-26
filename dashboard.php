<?php
session_start();
if(!isset($_SESSION["id_user"])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        :root {
            /* Light mode colors */
            --bg-gradient: linear-gradient(90deg, rgba(131, 58, 180, 1) 0%, rgba(253, 29, 29, 1) 50%, rgba(252, 176, 69, 1) 100%);
            --card-bg: rgba(255, 255, 255, 0.1);
            --text-color: white;
            --card-hover: rgba(255, 255, 255, 0.3); 
        }

        [data-theme="dark"] {
            /* Dark mode colors */
            --bg-gradient: linear-gradient(90deg, rgba(20, 20, 40, 1) 0%, rgba(40, 20, 60, 1) 50%, rgba(20, 40, 60, 1) 100%);
            --card-bg: rgba(30, 30, 50, 0.8);
            --text-color: #e0e0e0;
            --card-hover: rgba(50, 50, 70, 0.8);
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: var(--text-color);
            transition: background 0.3s ease;
        }
        .welcome-box {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: background 0.3s ease;       
        }
        h1 {
            margin-bottom: 30px;
        }
        .nav-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            justify-content: center;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 25px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: var(--card-hover);
            transform: translateY(-2px);
        }
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="welcome-box">
        <h1>Welcome, <?= htmlspecialchars($_SESSION["user_email"]) ?></h1>
        <p>You have successfully logged in!</p>
        
        <div class="nav-links">
            <a href="profile.php">👤 My Profile</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>
    <!-- Theme Toggle Button -->
    <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn">
        🌙 Dark Mode
    </button>

    <script>
        // Load saved theme on page load
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateButton(savedTheme);

        function toggleTheme() {
            // Get current theme
            const currentTheme = document.documentElement.getAttribute('data-theme');
            
            // Switch theme
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Apply new theme
            document.documentElement.setAttribute('data-theme', newTheme);
            
            // Save to localStorage
            localStorage.setItem('theme', newTheme);
            
            // Update button text
            updateButton(newTheme);
        }

        function updateButton(theme) {
            const btn = document.getElementById('themeBtn');
            btn.textContent = theme === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
        }
    </script>
</body>
</html>
