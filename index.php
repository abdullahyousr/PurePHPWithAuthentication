<?php
    require_once 'Configuration/database.php';
    require_once 'Configuration/session.php';

    $sql = "SELECT posts.*, users.first_name, users.last_name 
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC";
    $database = new Database();
    $result = $database->getConnection()->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog - Home</title>
    <link rel="stylesheet" href = "css/style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-logo">
                    <h1>My Blog</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="viewposts.php" class="nav-link">Posts</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <?php endif; ?>
                    
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
            <section class="hero-section">
                <h2>Welcome to My Blog</h2>
                <p>Discover amazing stories and insights from around the world</p>
                <div class="hero-buttons">
                    <a href="viewposts.php" class="btn btn-primary btn-hero">Start Reading</a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-secondary btn-hero">Join Our Community</a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="posts-section">
                <div class="section-header">
                    <h3>Latest Posts</h3> 
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="create-post.php" class="btn btn-primary">Create New Post</a>
                   <?php endif; ?>               
                </div>

                <div class="posts-grid">
                <?php if ($result && $result->rowCount() > 0): ?>
                    <?php while($row = $result->fetch(PDO::FETCH_ASSOC)): ?>      
                    <!-- Post 1 -->
                    <article class="post-card">
                        <div class="post-image tech"></div>
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="post-author">By <?php echo htmlspecialchars($row['first_name']) ." " .  htmlspecialchars($row['last_name']); ?></span>
                                <span class="post-date"><?php echo date('F j, Y', strtotime($row['created_at'])); ?></span>
                            </div>
                            <h4 class="post-title">
                                <a href="view-post.php?id=<?php echo $row['id'];?>"> 
                                        <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                            </h4>
                            <p class="post-excerpt">
                            <?php echo htmlspecialchars($row['excerpt'] ?? ''); ?>
                            </p>
                            <div class="post-footer">
                                <!-- <span class="post-category technology">Technology</span> -->
                                <!-- <span class="post-stats">1,234 views • 5 comments</span> -->
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php endif; ?>
                </div>
<!--                 
                <div class="pagination">
                    <a href="#" class="page-link active">1</a>
                    <a href="#" class="page-link">2</a>
                    <a href="#" class="page-link">3</a>
                    <a href="#" class="page-link">4</a>
                    <a href="#" class="page-link">Next</a>
                </div> -->
            </section>
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
                        <li><a href="#">Home</a></li>
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
