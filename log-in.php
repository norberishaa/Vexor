<?php
session_start();
require_once "config/db.php";

$error = '';
$success = '';

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'unauthorized') {
        $error = "You must be logged in to access that page";
    } elseif ($_GET['error'] == 'session_expired') {
        $error = "Your session has expired. Please log in again.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if ($email === '' || $password === '') {
        $error = "Please enter all fields";
    } else {
        $stmt = $conn->prepare("SELECT user_id, emri, email, password, admin FROM users WHERE email = ? LIMIT 1"); 
        $stmt->bind_param('s', $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // SUCCESS - Login user
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['emri'];
                $_SESSION['admin'] = $user['admin'];
                
                // Redirect to dashboard
                if ($user['admin'] === 'yes') {
                    header("Location: admin.php");
                } else {
                    header("Location: dashboard.php");
                }

                exit();
            } else {
                // FAIL - Wrong password
                $error = "Invalid email or password";
            }
        } else {
            // FAIL - Email not found
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
            <a href="news.php">News</a>
            <a href="contact.html">Contact</a>
            <button class="log-in" onclick="location.href='log-in.php'">Log In</button>
        </div>
    </nav>
    
    <div class="log-in-section">
        <form class="log-in-container" method="POST" action="log-in.php">
            <h1>Log In</h1>
            
            <?php if ($error): ?>
                <div class="error-message" style="color:#ff9b9b"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <div class="log-in-top">  <!-- ← FIXED: Changed from <form> to <div> -->
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <a href="#">Forgot your password?</a>
            </div>  <!-- ← FIXED: Changed from </form> to </div> -->
            
            <div class="log-in-bottom">
                <button type="submit" class="log-in">Log In</button>
                <span class="sign-up-link">No account yet? No problem - <a href="sign-up.php">Sign Up</a></span>
            </div>
        </form>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>