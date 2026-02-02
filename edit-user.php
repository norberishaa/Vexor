<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
require_once "classes/User.php";

$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $admin = $_POST['admin'];

    if ($userId === $_SESSION['user_id'] && $admin === 'no') {
        header("Location: admin.php?tab=users&error=cannot_demote_self");
        exit();
    }

    if ($user->updateUser($userId, $name, $email, $admin)) {
        header("Location: admin.php?tab=users&success=user_updated");
    } else {
        header("Location: admin.php?tab=users&error=update_failed");
    }
    exit();
}

header("Location: admin.php?tab=users");
exit();
?>