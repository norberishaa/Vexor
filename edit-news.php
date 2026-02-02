<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
require_once "classes/News.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php?tab=news');
    exit();
}

// Get form data
$news_id = $_POST['news_id'] ?? '';
$news_title = $_POST['news_title'] ?? '';
$news_description = $_POST['news_description'] ?? '';
$author = $_POST['author'] ?? '';
$article_url = $_POST['article_url'] ?? '';

// Validate required fields
if (empty($news_id) || empty($news_title) || empty($news_description) || empty($author) || empty($article_url)) {
    $_SESSION['error'] = "All fields are required.";
    header('Location: admin.php?tab=news');
    exit();
}

// Validate URL format
if (!filter_var($article_url, FILTER_VALIDATE_URL)) {
    $_SESSION['error'] = "Invalid URL format.";
    header('Location: admin.php?tab=news');
    exit();
}

// Sanitize inputs
$news_title = htmlspecialchars(trim($news_title), ENT_QUOTES, 'UTF-8');
$news_description = htmlspecialchars(trim($news_description), ENT_QUOTES, 'UTF-8');
$author = htmlspecialchars(trim($author), ENT_QUOTES, 'UTF-8');
$article_url = filter_var($article_url, FILTER_SANITIZE_URL);

try {
    $news = new News($conn);
    $result = $news->update($news_id, $news_title, $news_description, $author, $article_url);
    
    if ($result) {
        $_SESSION['success'] = "News article updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update news article.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header('Location: admin.php?tab=news');
exit();
?>