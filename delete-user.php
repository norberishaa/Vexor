<?php
require_once "config/auth_admin.php";
require_once "config/db.php";

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Prevent admin from deleting themselves
    if ($user_id === $_SESSION['user_id']) {
        $return_url = $_GET['return'] ?? 'admin.php?tab=users';
        header("Location: $return_url&error=cannot_delete_self");
        exit();
    }
    
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    
    if ($stmt->execute()) {
        $return_url = $_GET['return'] ?? 'admin.php?tab=users';
        header("Location: $return_url&success=user_deleted");
    } else {
        $return_url = $_GET['return'] ?? 'admin.php?tab=users';
        header("Location: $return_url&error=delete_failed");
    }
    exit();
}

header("Location: admin.php?tab=users");
exit();
?>