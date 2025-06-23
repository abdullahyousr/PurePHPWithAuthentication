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
        if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$_POST['email']]);
            if ($check->fetch()) {
                $error = "Email already registered.";
            } else {
                $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $passwordHash]);
                header('Location: login.php');
                exit;
            }
        }else{
            $error = "All fields are required.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="main-content">
        <div class="container">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <h2>Create Account</h2>
                        <p>Join our community and start sharing your stories</p>
                    </div>

                    <form class="auth-form" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name *</label>
                                <input type="text" id="first-name" name="first_name" placeholder="Enter your first name" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name *</label>
                                <input type="text" id="last-name" name="last_name" placeholder="Enter your last name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" placeholder="Create a password" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Create Account</button>
                    </form>
                    <?php if (isset($error)) echo "<p>$error</p>"; ?>

                    <div class="auth-footer">
                        <p>Already have an account? <a href="login.php">Sign in here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 