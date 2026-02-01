<?php
session_start();
require_once "config/db.php";
require_once "classes/User.php";

$user = new User($conn);

$error = '';

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'unauthorized') {
        $error = "You must be logged in to access that page";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter all fields";
    } else {
        $result = $user->getByEmail($email);

        if ($result->num_rows === 1) {
            $userData = $result->fetch_assoc();

            if (password_verify($password, $userData['password'])) {
                $_SESSION['user_id'] = $userData['user_id'];
                $_SESSION['email'] = $userData['email'];
                $_SESSION['name'] = $userData['emri'];
                $_SESSION['admin'] = strtolower(trim($userData['admin']));

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="fonts.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/vexor_logo/vexor_black.ico" />
    <title>Vexor - Log In</title>
</head>
<body>
    <nav class="nav-bar">
        <div class="nav-logo-container">
            <a href="index.html"><img src="images/vexor_logo/vexor_black.svg" id="nav-logo" alt="Vexor Logo"></a>
        </div>
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-links" id="navLinks">
            <a href="index.html">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="news.html">News</a>
            <a href="contact.html">Contact</a>
            <button class="log-in" onclick="location.href='log-in.php'">Log In</button>
        </div>
    </nav>
    
    <div class="log-in-section">
        <form class="log-in-container" method="POST" action="log-in.php">
            <h1>Log In</h1>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="log-in-top">
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <a href="#">Forgot your password?</a>
            </div>
            <div class="log-in-bottom">
                <button type="submit" class="log-in">Log In</button>
                <span class="sign-up-link">No account yet? No problem - <a href="sign-up.php">Sign Up</a></span>
            </div>
        </form>
    </div>
    <script src="js/script.js"></script>
</body>
</html>