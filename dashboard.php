<?php 
    require_once 'Configuration/session.php';
    require_once 'Configuration/database.php';
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $database = new Database();
    $pdo = $database->getConnection();

    // Get user information
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $pdo->prepare($user_sql);
    $user_stmt->execute([$_SESSION['user_id']]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
    $user_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
    $user_email = htmlspecialchars($user['email']); 
    $user_register_date =  date('F Y', strtotime($user['created_at']));
    // Get user posts
    $posts_sql = "SELECT *
                    FROM posts
                    WHERE user_id = ?
                    ORDER BY posts.created_at DESC";
    $posts_stmt = $pdo->prepare($posts_sql);
    $posts_stmt->execute([$_SESSION['user_id']]);
    $posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - My Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="main-content">
        <div class="container">
            <div class="dashboard-container">
                <!-- Dashboard Header -->
                <div class="dashboard-header">
                    <h2>Dashboard</h2>
                    <p>Welcome back, <?php echo $user_name; ?></p>
                </div>

                <!-- Quick Stats -->
                <!-- <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">12</div>
                        <div class="stat-label">Total Posts</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">156</div>
                        <div class="stat-label">Total Views</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Comments</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">4.8</div>
                        <div class="stat-label">Avg Rating</div>
                    </div>
                </div> -->

                <!-- Quick Actions -->
                <div class="quick-actions" >
                    <!-- <h3>Quick Actions</h3> -->
                    <div class="action-buttons">
                        <a href="create-post.php" class="btn btn-primary">Create New Post</a>
                        <a href="viewposts.php" class="btn btn-secondary">Show All Posts</a>
                        <!-- <a href="edit-post.html" class="btn btn-secondary">Edit Post</a>
                        <a href="#" class="btn btn-secondary">View Profile</a>
                        <a href="index.html" class="btn btn-secondary">View Blog</a> -->
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="recent-posts">
                    <h3>My Posts</h3>
                    <div class="posts-list">
                        <?php if ($posts): ?>
                            <?php foreach ($posts as $post): ?>
                                <div class="post-item">
                                    <div class="post-info">
                                        <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                                        <p class="post-meta"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></p>
                                    </div>
                                    <div class="post-actions">
                                        <a href="view-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <a href="delete-post.php?id=<?php echo $post['id']; ?>" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this post?');">
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <p>No posts found.</p>
                            <?php endif; ?>
                    </div>
                </div>
                <!-- User Info -->
                <div class="user-info">
                    <h3>Account Information</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value"><?php echo $user_name ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo $user_email; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Member Since:</span>
                            <span class="info-value"><?php echo $user_register_date; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <div class="logout-section">
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 