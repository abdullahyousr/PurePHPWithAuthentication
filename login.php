<?php
    require_once 'Configuration/database.php';
    require_once 'Configuration/session.php';

    if (isset($_SESSION['user_id'])) {
        header('Location: dashboard.php');
        exit;
    }
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($_POST['password'], $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Please fill in all fields";
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="main-content">
        <div class="container">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2>Welcome Back</h2>
                        <p>Sign in to your account to continue</p>
                    </div>

                    <form class="auth-form" method="post">
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Sign In</button>
                    </form>
                    <?php if (isset($error)) echo "<p>$error</p>"; ?>

                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 