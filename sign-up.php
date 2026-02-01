<?php
session_start();
require_once "config/db.php";
require_once "classes/User.php";

$user = new User($conn);

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);

    // Validation
    if ($name === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $error = "All fields are required";
    } elseif (!$terms) {
        $error = "You must agree to the terms of service";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 10) {
        $error = "Password must be at least 10 characters";
    } elseif ($user->emailExists($email)) {
        $error = "Email already exists";
    }

    if ($error === '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($user->create($name, $lastName, $email, $hashedPassword)) {
            $newUser = $user->getByEmail($email);
            $userData = $newUser->fetch_assoc();

            $_SESSION['user_id'] = $userData['user_id'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['name'] = $userData['emri'];
            $_SESSION['admin'] = $userData['admin'];
            $_SESSION['last_activity'] = time();

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error creating account. Please try again.";
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
    <title>Vexor - Sign Up</title>
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
    
    <div class="sign-up-section">
        <form class="sign-up-container" action="sign-up.php" method="POST">
            <h1>Sign Up</h1>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="sign-up-top">
                <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>
                <div class="checkbox">
                    <input type="checkbox" name="terms" required>
                    <span>I agree to the terms of service.</span>
                </div>
            </div>
            <div class="sign-up-bottom">
                <button type="submit" class="sign-up">Sign Up</button>
            </div>
        </form>
    </div>
    <script src="js/script.js"></script>
</body>
</html>