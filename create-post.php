<?php 
    require_once 'Configuration/session.php';
    require_once 'Configuration/database.php';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    
    $database = new Database();
    $pdo = $database->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Basic validation
        if (!empty($_POST['title']) && !empty($_POST['content'])) {
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $user_id = $_SESSION['user_id'];

            $sql = "INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $user_id]);

            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Title and content are required.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post - My Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-logo">
                    <h1>My Blog</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.html" class="nav-link">Home</a></li>
                    <li><a href="viewposts.php" class="nav-link">Posts</a></li>
                    <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>

                    <li class="auth-buttons">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="logout.php" class = "btn btn-danger">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class = "btn btn-login">Login</a>
                            <a href="register.php" class = "btn btn-register">Register</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">

            <div class="page-header">
                <h2>Create New Post</h2>
                <p>Share your thoughts and ideas with the world</p>
            </div>

            <div class="form-container">
                <form class="post-form" method="post">
                    <div class="form-group">
                        <label for="post-title">Post Title *</label>
                        <input type="text" id="post-title" name="title" placeholder="Enter your post title" required>
                    </div>

                    <!-- <div class="form-group">
                        <label for="post-image">Featured Image</label>
                        <input type="file" id="post-image" name="image" accept="image/*">
                        <small>Upload an image to make your post more engaging (optional)</small>
                    </div> -->

                    <div class="form-group">
                        <label for="post-content">Post Content *</label>
                        <textarea id="post-content" name="content" rows="15" placeholder="Write your post content here..." required></textarea>
                    </div>

                    <div class="form-actions">
                        <!-- <button type="button" class="btn btn-secondary">Save Draft</button> -->
                        <button type="submit" class="btn btn-primary">Publish Post</button>
                    </div>
                </form>
            </div>

            <div class="writing-tips">
                <h3>Writing Tips</h3>
                <ul>
                    <li>Write a compelling headline that grabs attention</li>
                    <li>Start with a strong introduction that hooks the reader</li>
                    <li>Use clear, concise language</li>
                    <li>Break up text with headings and bullet points</li>
                    <li>Include relevant images to enhance your content</li>
                    <li>End with a call-to-action or conclusion</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>My Blog</h4>
                    <p>Share your stories and connect with readers from around the world.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Categories</h4>
                    <ul>
                        <li><a href="#">Technology</a></li>
                        <li><a href="#">Health</a></li>
                        <li><a href="#">Travel</a></li>
                        <li><a href="#">Art</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>Email: info@myblog.com</p>
                    <p>Phone: +1 234 567 890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 My Blog. All rights reserved.</p>
            </div>
        </div>
    </footer> -->
</body>
</html> 