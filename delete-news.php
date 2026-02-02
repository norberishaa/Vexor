<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
require_once "classes/News.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php?tab=news');
    exit();
}

// Get News ID from POST data
$news_id = $_POST['news_id'] ?? '';

// Validate News ID
if (empty($news_id)) {
    $_SESSION['error'] = "News ID is required.";
    header('Location: admin.php?tab=news');
    exit();
}

try {
    $news = new News($conn);
    $result = $news->delete($news_id);
    
    if ($result) {
        $_SESSION['success'] = "News article deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete news article. It may not exist.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header('Location: admin.php?tab=news');
exit();
?>