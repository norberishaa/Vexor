<?php
require_once "config/db.php";

$sql = "SELECT news_title, news_description, author, date_posted, article_url FROM news ORDER BY date_posted DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="fonts.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/vexor_logo/vexor_black.ico" />
    <title>News</title>
</head>
<body>
    <nav class="nav-bar">
            <div class="nav-logo-container">
                <a href="index.html"><img src="images/vexor_logo/vexor_black.svg" id="nav-logo" alt="Vexor Logo"></a>
            </div>
            
            <!-- Hamburger Menu -->
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="nav-links" id="navLinks">
                <a href="index.html">Home</a>
                <a href="dashboard.html">Dashboard</a>
                <a href="news.html">News</a>
                <a href="contact.html">Contact</a>
                <button class="log-in" onclick="location.href='log-in.html'">Log In</button>
            </div>
        </nav>

        <div class="news">
            <div class="news-container">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="news-item-container">
                            <a href="<?php echo htmlspecialchars($row['article_url'], ENT_QUOTES, 'UTF-8'); ?>" class="titulli" target="_blank">
                                <?php echo htmlspecialchars($row['news_title'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <span class="short-description">
                                <?php echo htmlspecialchars($row['news_description'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <div class="news-item-bottom">
                                <span class="data"><?php echo date('M d, Y', strtotime($row['date_posted'])); ?></span>
                                <span class="autori"><?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="no-news">
                        <p>No news articles available at the moment.</p>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

    <script src="js/script.js"></script>
</body>
</html>
