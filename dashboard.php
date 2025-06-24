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
    $user_id = $_SESSION['user_id'];
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

    // Get products
    $products_sql = "SELECT *
                 FROM products
                 ORDER BY products.created_at DESC
                 LIMIT 3";
    $products = $pdo->query($products_sql);

    // Get cart items count
    $cart_count = 0;
    $cart_count_sql = "SELECT COUNT(*) as count FROM cart_items ci 
                    JOIN carts c ON ci.cart_id = c.id 
                    WHERE c.user_id = ?";
    $cart_count_stmt = $pdo->prepare($cart_count_sql);
    $cart_count_stmt->execute([$_SESSION['user_id']]);
    $cart_count_result = $cart_count_stmt->fetch(PDO::FETCH_ASSOC);
    $cart_count = $cart_count_result['count'];
    
    $cart_stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = ?");
    $cart_stmt->execute([$user_id]);
    $existing_cart = $cart_stmt->fetch(PDO::FETCH_ASSOC);        
    
    // add products to carts
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_product_id'])) {
        $cart_id = null;

        // Find or create cart for user
        if (!$existing_cart) {
            $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)")->execute([$user_id]);
            $cart_id = $pdo->lastInsertId();
        } else {
            $cart_id = $existing_cart['id'];
        }        

        $product_id = (int)$_POST['cart_product_id'];
        // Check if product already in cart
        $item_stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
        $item_stmt->execute([$cart_id, $product_id]);
        $item = $item_stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Update quantity
            $pdo->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?")->execute([$item['id']]);
        } else {
            // Add new item
            $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, 1)")->execute([$cart_id, $product_id]);
        }
    }
    // Order Products
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_all_cart'])) {
        if ($existing_cart) {
            $cart_id = $existing_cart['id'];
             // Get all cart items
            $items_stmt = $pdo->prepare("SELECT ci.*, p.price, p.stock 
                                        FROM cart_items ci 
                                        JOIN products p ON ci.product_id = p.id 
                                        WHERE ci.cart_id = ?");
            $items_stmt->execute([$cart_id]);
            $cart_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($cart_items) {
                $total = 0;
                foreach ($cart_items as $item) {
                    if ($item['stock'] < $item['quantity']) {
                        // Not enough stock for this item
                        header('Location: dashboard.php?order=outofstock');
                        exit;
                    }
                    $total += $item['price'] * $item['quantity'];
                }

            // Create order
            $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)")->execute([$user_id, $total]);
            $order_id = $pdo->lastInsertId();


            // Add each cart item to order_items and update product stock
            foreach ($cart_items as $item) {
                $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)")
                    ->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")
                    ->execute([$item['quantity'], $item['product_id']]);
                }
                $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cart_id]);
                header('Location: dashboard.php?order=success');
                exit;                          
            }   
        }  
    }

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
          

                <!-- Quick Actions -->
                <div class="quick-actions" >
                    <div class="action-buttons">
                        <a href="create-post.php" class="btn btn-primary">Create New Post</a>
                        <a href="viewposts.php" class="btn btn-secondary">Show All Posts</a>
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

                    <!-- Products Dashboard Section with Action Buttons -->
                <section class="dashboard-products">
                <div class="products-header">
                    <h2>Products</h2>
                    <div class="products-icon-orders">
                        <form method="post">
                            <input type="hidden" name="order_all_cart" value="1">
                            <button type="submit" class="btn btn-secondary btn-orders">Order Products</button>
                        </form>
                        <div class="cart-icon">
                            <a href="view_cart.php" class="cart-link">
                                <span class="cart-icon-symbol">🛒</span>
                                <span class="cart-count"><?php echo $cart_count; ?></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="products-grid">
                <?php if ($products && $products->rowCount() > 0): ?>
                    <?php while($row = $products->fetch(PDO::FETCH_ASSOC)): ?>   
                    <div class="product-card">
                        <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="product-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="product-info">
                            <span class="product-price">$<?php echo htmlspecialchars($row['price']); ?></span>
                            <span class="product-stock">In Stock: <?php echo htmlspecialchars($row['stock']); ?></span>
                        </div>
                        <div class="product-actions">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="cart_product_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                   
                </div>
                </section>


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