<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: log-in.php?error=unauthorized");
    exit();
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'yes') {
    header("Location: index.html");
    exit();
}
?>