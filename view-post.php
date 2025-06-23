<?php
    require_once 'Configuration/database.php';
    require_once 'Configuration/session.php';


    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die('Invalid post ID.');
    }
    
    $post_id = (int)$_GET['id'];

    $post_sql = "SELECT posts.*, users.first_name, users.last_name
    FROM posts
    JOIN users ON posts.user_id = users.id
    WHERE posts.id = :id
    LIMIT 1";

    $database = new Database();
    $pdo = $database->getConnection();
    $stmt = $pdo->prepare($post_sql);
    $stmt->execute(['id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        die('Post not found.');
    }

    $title = htmlspecialchars($post['title']); 
    $auther_name = htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
    $created_date =  htmlspecialchars($post['created_at']);
    $post_slug =  htmlspecialchars($post['slug'] ?? '');
    $post_content = nl2br(htmlspecialchars($post['content']));

    $comment_sql = "SELECT comments.*, users.first_name, users.last_name
                    FROM comments
                    JOIN users ON comments.user_id = users.id
                    WHERE comments.post_id = :post_id
                    ORDER BY comments.created_at ASC";
    $stmt_comments = $database->getConnection()->prepare($comment_sql);
    $stmt_comments->execute(['post_id' => $post_id]);
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

    $recent_posts_sql = "SELECT posts.*, users.first_name, users.last_name
                        FROM posts
                        JOIN users ON posts.user_id = users.id
                        ORDER BY posts.created_at DESC
                        LIMIT 5";
    $stmt_recent = $database->getConnection()->query($recent_posts_sql);
    $recent_posts = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
        if (!empty($_POST['comment']) && isset($_SESSION['user_id'])) {
            $comment = trim($_POST['comment']);
            $user_id = $_SESSION['user_id'];
            $post_id = $post['id']; 
    
            $sql = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$post_id, $user_id, $comment]);
    
            // Optional: Redirect to avoid form resubmission
            header("Location: view-post.php?id=" . $post_id);
            exit;
        } else {
            $comment_error = "Comment cannot be empty.";
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?> - My Blog</title>
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

            <!-- Single Post -->
            <article class="single-post">
                <div class="post-header">
                    <div class="post-meta">
                        <!-- <span class="post-category technology">Technology</span> -->
                        <span class="post-date"><?php echo $created_date ?></span>
                        <span class="post-author"><?php echo $auther_name ?></span>
                        <!-- <span class="post-read-time">5 min read</span> -->
                    </div>
                    <h1 class="post-title"><?php echo $title ?></h1>
                    <div class="post-image">
                        <!-- <img src="1.png" alt="<?php echo $post_slug ?>"> -->
                    </div>
                </div>

                <div class="post-content">
                    <?php echo $post_content ?>
                </div>

                <div class="post-footer">
                    <!-- <div class="post-tags">
                        <span class="tag">Web Development</span>
                        <span class="tag">HTML</span>
                        <span class="tag">CSS</span>
                        <span class="tag">JavaScript</span>
                        <span class="tag">Programming</span>
                    </div> -->
                    <!-- <div class="post-actions">
                        <button class="btn btn-secondary">Share</button>
                        <button class="btn btn-primary">Like</button>
                    </div> -->
                </div>
            </article>

            <!-- Author Info -->
            <section class="author-section">
                <div class="author-card">
                    <!-- <div class="author-avatar">
                        <img src="https://via.placeholder.com/80x80/4a90e2/ffffff?text=JD" alt="John Doe">
                    </div> -->
                    <div class="author-info">
                        <h3><?php echo $auther_name ?></h3>
                        <!-- <p class="author-bio">Full-stack web developer with 5+ years of experience. Passionate about teaching and sharing knowledge with the community.</p> -->
                        <!-- <div class="author-social">
                            <a href="#" class="social-link">Twitter</a>
                            <a href="#" class="social-link">LinkedIn</a>
                            <a href="#" class="social-link">GitHub</a>
                        </div> -->
                    </div>
                </div>
            </section>

            <!-- Comments Section -->
            <section class="comments-section">
                <!-- <h3>Comments (5)</h3> -->
                <?php  if (isset($_SESSION['user_id'])): ?>
                    <div class="comment-form">
                        <h4>Leave a Comment</h4>
                        <?php if (isset($comment_error)) echo "<p style='color:red;'>$comment_error</p>"; ?>
                        <form method="post" class="comment-form">
                            <!-- <div class="form-group">
                                <label for="comment-name">Name</label>
                                <input type="text" id="comment-name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="comment-email">Email</label>
                                <input type="email" id="comment-email" name="email" required>
                            </div> -->
                            <div class="form-group">
                                <label for="comment-message">Comment</label>
                                <textarea id="comment" name="comment" rows="5" required></textarea>
                            </div>
                            <div style="text-align: center;">
                                <button type="submit" name="submit_comment" class="btn btn-primary">Post Comment</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                <div class="comments-list">
                <?php if ($comments): ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-avatar">
                            <img src="1.png" alt="John Doe">
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <h5 class="comment-author"><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></h5>
                                <span class="comment-date"><?php echo htmlspecialchars($comment['created_at']); ?></span>
                            </div>
                            <p class="comment-text"> <?php echo htmlspecialchars($comment['content']); ?></p>
                            <!-- <div class="comment-actions">
                                <button class="btn-link">Reply</button>
                                <button class="btn-link">Like</button>
                            </div> -->
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p> No Comments <p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Related Posts -->
            <section class="related-posts">
                <h3>Related Posts</h3>
                <div class="posts-grid">
                <?php foreach ($recent_posts as $post): ?>
                    <article class="post-card">
                        <div class="post-image tech"></div>
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="post-author"><?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></span>
                                <span class="post-date"><?php echo htmlspecialchars($post['created_at']); ?></span>
                            </div>
                            <h4 class="post-title">
                                <a  href="view-post.php?id=<?php echo $post['id']; ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h4>
                            <p class="post-excerpt">
                            <?php echo htmlspecialchars($post['excerpt'] ?? ''); ?>
                            </p>
                            <div class="post-footer">
                                <!-- <span class="post-category technology">Technology</span> -->
                                <!-- <span class="post-stats">3,245 views • 31 comments</span> -->
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
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