<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
require_once "classes/User.php";
require_once "classes/Cve.php";
require_once "classes/News.php";

// Create class instances, passing the db connection
$user = new User($conn);
$cve = new Cve($conn);
$news = new News($conn);

$active_tab = $_GET['tab'] ?? 'home';

// Use class methods to get data
$users_result = $user->getAll();
$cves_result = $cve->getAll();
$news_result = $news->getAll();

$totalUsers = $user->getCount();
$totalCves = $cve->getCount();
$totalNews = $news->getCount();

$stats = $cve->getStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fonts.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/vexor_logo/vexor_black.ico" />
    <title>Vexor - Admin</title>
</head>
<body>
    <div class="admin-header">

        <div class="admin-header-top">
            <img src="images/vexor_logo/vexor_white.svg">
        </div>

        <div class="admin-header-tabs">    
            <button class="admin-tab <?= $active_tab === 'home' ? 'active' : '' ?>" data-admintab="home">
                🏠 Home
            </button>
            <button class="admin-tab <?= $active_tab === 'users' ? 'active' : '' ?>" data-admintab="users">
                👥 Users (<?= $totalUsers ?>)
            </button>
            <button class="admin-tab <?= $active_tab === 'cve' ? 'active' : '' ?>" data-admintab="cve">
                🛡️ CVE Management (<?= $totalCves ?>)
            </button>
            <button class="admin-tab <?= $active_tab === 'news' ? 'active' : '' ?>" data-admintab="news">
                📰 News (<?= $totalNews ?>)
            </button>
        </div>

        <div class="admin-header-bottom">
            <a href="index.html" class="admin-leave">< Leave</a>
        </div>
    </div>

    <!-- HOME TAB -->
    <div class="tab-content <?= $active_tab === 'home' ? 'active' : '' ?>">
        <div class="admin-home">
            <div class="admin-home-text">
                <h1>Welcome to the admin dashboard, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
                <div class="warning">
                    <b>Production Environment!</b>
                    <p>Changes made here reflect directly in the database. Please be responsible.</p>
                </div>
            </div>

            <div class="admin-home-cards">
                <div class="card">
                    <span>Total Users</span>
                    <p><?= $totalUsers ?></p>
                </div>
                <div class="card">
                    <span>Tracked CVEs</span>
                    <p><?= $totalCves ?></p>
                </div>
                <div class="card">
                    <span>Unpatched</span>
                    <p><?= $stats['unpatched'] ?></p>
                </div>
                <div class="card">
                    <span>High Severity</span>
                    <p><?= $stats['high'] ?></p>
                </div>
                <div class="card">
                    <span>Total News</span>
                    <p><?= $totalNews ?></p>
                </div>
                <div class="card">
                    <span>Current Date</span>
                    <p class="date"><?= date('Y-m-d') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- USERS TAB -->
    <div class="tab-content <?= $active_tab === 'users' ? 'active' : '' ?>">
        <div class="admin-users">
            <div class="admin-users-text">
                <h1>Users</h1>
                <p>See and edit all active users:</p>
            </div>

            <div class="admin-users-content">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['user_id']) ?></td>
                                <td><?= htmlspecialchars($u['emri']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?= $u['admin'] === 'yes' 
                                        ? '<span class="badge badge-success">✅ Admin</span>' 
                                        : '<span class="badge badge-default">👤 User</span>' ?>
                                </td>
                                <td class="actions">
                                    <button class="btn-small btn-edit"
                                            onclick="editUser(<?= $u['user_id'] ?>, '<?= htmlspecialchars($u['emri'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>', '<?= $u['admin'] ?>')">
                                        ✏️ Edit
                                    </button>
                                    <?php if ($u['user_id'] !== $_SESSION['user_id']): ?>
                                        <a href="toggle-admin.php?id=<?= $u['user_id'] ?>&return=admin.php?tab=users"
                                           class="btn-small btn-toggle"
                                           onclick="return confirm('Toggle admin for <?= htmlspecialchars($u['emri']) ?>?')">
                                            <?= $u['admin'] === 'yes' ? '⬇️ Demote' : '⬆️ Promote' ?>
                                        </a>
                                        <a href="delete-user.php?id=<?= $u['user_id'] ?>&return=admin.php?tab=users"
                                           class="btn-small btn-delete"
                                           onclick="return confirm('Delete <?= htmlspecialchars($u['emri']) ?>?')">
                                            🗑️ Delete
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CVE TAB -->
    <div class="tab-content <?= $active_tab === 'cve' ? 'active' : '' ?>">
        <div class="admin-cves">
            <div class="admin-cves-text">
                <h1>CVE Management</h1>
                <p>View and manage all tracked CVEs:</p>
            </div>

            <div class="admin-cves-content">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>CVE ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Severity</th>
                            <th>Date Reported</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $cves_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['cve_id']) ?></td>
                                <td><?= htmlspecialchars($c['name']) ?></td>
                                <td><?= htmlspecialchars($c['status']) ?></td>
                                <td>
                                    <span style="color: <?= $c['severity'] >= 8 ? '#d32f2f' : '#f57c00' ?>; font-weight: bold;">
                                        <?= htmlspecialchars($c['severity']) ?>/10
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($c['date_reported']) ?></td>
                                <td class="actions">
                                    <button class="btn-small btn-edit"
                                            onclick="editCve('<?= htmlspecialchars($c['cve_id'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['status'], ENT_QUOTES) ?>', <?= $c['severity'] ?>, '<?= htmlspecialchars($c['date_reported'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['description'], ENT_QUOTES) ?>')">
                                        ✏️ Edit
                                    </button>
                                    <a href="delete-cve.php?id=<?= htmlspecialchars($c['cve_id']) ?>&return=admin.php?tab=cve"
                                       class="btn-small btn-delete"
                                       onclick="return confirm('Delete <?= htmlspecialchars($c['cve_id']) ?>?')">
                                        🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- NEWS TAB -->
    <div class="tab-content <?= $active_tab === 'news' ? 'active' : '' ?>">
        <div class="admin-news">
            <div class="admin-news-text">
                <h1>News Management</h1>
                <p>View and manage all news articles:</p>
            </div>

            <div class="admin-news-content">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Date Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($n = $news_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($n['news_title']) ?></td>
                                <td><?= htmlspecialchars($n['author']) ?></td>
                                <td><?= htmlspecialchars($n['date_posted']) ?></td>
                                <td class="actions">
                                    <button class="btn-small btn-edit"
                                            onclick="editNews(<?= $n['news_id'] ?>, '<?= htmlspecialchars($n['news_title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($n['news_description'], ENT_QUOTES) ?>', '<?= htmlspecialchars($n['author'], ENT_QUOTES) ?>', '<?= htmlspecialchars($n['article_url'], ENT_QUOTES) ?>')">
                                        ✏️ Edit
                                    </button>
                                    <a href="delete-news.php?id=<?= $n['news_id'] ?>&return=admin.php?tab=news"
                                       class="btn-small btn-delete"
                                       onclick="return confirm('Delete this article?')">
                                        🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>