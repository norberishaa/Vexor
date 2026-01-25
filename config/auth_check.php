<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    // User is not logged in  redirect to login page
    header("Location: log-in.php?error=unauthorized");
    exit();
}

// Optional: Session timeout (30 minutes of inactivity)
$timeout_duration = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    //  Session  expired
    session_unset();
    session_destroy();
    header("Location: log-in.php?error=session_expired");
    exit();
}

$_SESSION['last_activity'] = time();
?>