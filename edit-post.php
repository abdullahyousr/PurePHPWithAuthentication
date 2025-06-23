<?php 
    require_once 'Configuration/session.php';
    require_once 'Configuration/database.php';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $database = new Database();
    $pdo = $database->getConnection();

    // Get post ID from URL
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: dashboard.php');
        exit;
    }
    $post_id = $_GET['id'];
    // Fetch the post, ensure it belongs to the user
    $sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        // Post not found or not owned by user
        header('Location: dashboard.php');
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['title']) && !empty($_POST['content'])) {
                    $title = trim($_POST['title']);
                    $content = trim($_POST['content']);
            
                    $update_sql = "UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?";
                    $update_stmt = $pdo->prepare($update_sql);
                    $update_stmt->execute([$title, $content, $post_id, $_SESSION['user_id']]);
            
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = "Title and content are required.";
                }
            }
        } elseif (isset($_POST['delete'])) {
            $delete_sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
            $delete_stmt = $pdo->prepare($delete_sql);
            $delete_stmt->execute([$post_id, $_SESSION['user_id']]);
            header('Location: dashboard.php');
            exit;
        } elseif (isset($_POST['cancel'])) {
            header('Location: dashboard.php');
                    exit;
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - My Blog</title>
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
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <div class="page-header">
                <h2>Edit Post</h2>
                <p>Update your post content and settings</p>
            </div>

            <div class="form-container">
                <form class="post-form" method="post">
                    <div class="form-group">
                        <label for="post-title">Post Title *</label>
                        <input type="text" id="post-title" name="title" value="<?php echo $post['title']; ?>" required>
                    </div>

                    <!-- <div class="form-group">
                        <label for="post-image">Featured Image</label>
                         <div class="current-image">
                             <img src="1.png" alt="Current featured image">
                            <button type="button" class="btn btn-secondary btn-sm">Change Image</button>
                        </div> 
                        <input type="file" id="post-image" name="image" accept="image/*">
                        <small>Upload a new image to replace the current one</small>
                    </div> -->

                    <div class="form-group">
                        <label for="post-content">Post Content *</label>
                        <textarea id="post-content" name="content" rows="15" required><?php echo $post['content']?></textarea>
                    </div>

                    <div class="form-actions" method="post">
                        <button type="submit" name="cancel" class="btn btn-secondary">Cancel</button>
                        <button type="submit" name="update" class="btn btn-primary">Update Post</button>
                        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
                    </div>
                </form>
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