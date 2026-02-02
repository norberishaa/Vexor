<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
require_once "classes/User.php";
require_once "classes/Cve.php";
require_once "classes/News.php";

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
                                    <?php if ($u['email'] !== $_SESSION['email']): ?>
                                        <button class="btn-small btn-edit"
                                            onclick="editUser(<?= $u['user_id'] ?>, '<?= htmlspecialchars($u['emri'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>', '<?= $u['admin'] ?>')">
                                        ✏️ Edit
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div id="edit-user-modal" class="edit-user-modal">
        <div class="top">
            <span class="top-title">Edit User:</span>
            <span id="close-user-modal">X</span>
        </div>
        <form method="POST" action="edit-user.php" class="edit-user-form">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="edit_name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="edit_email" required>
            </div>

            <div class="form-group">
                <label>Admin Status</label>
                <select name="admin" id="edit_admin">
                    <option value="no">👤 User</option>
                    <option value="yes">✅ Admin</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" id="delete-user-modal" class="btn-delete-modal">🗑️ Delete User</button>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- CVE TAB -->
    <div class="tab-content <?= $active_tab === 'cve' ? 'active' : '' ?>">
        <div class="admin-cve">
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
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- EDIT CVE MODAL -->
    <div id="edit-cve-modal" class="edit-user-modal">
        <div class="top">
            <span class="top-title">Edit CVE:</span>
            <span id="close-cve-modal">X</span>
        </div>
        <form method="POST" action="edit-cve.php" class="edit-user-form">
            <input type="hidden" name="cve_id" id="edit_cve_id">

            <div class="form-group">
                <label>CVE ID</label>
                <input type="text" name="cve_id_display" id="edit_cve_id_display" disabled>
            </div>

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="edit_cve_name" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_cve_status">
                    <option value="Not Patched">🔴 Unpatched</option>
                    <option value="Patched">✅ Patched</option>
                </select>
            </div>

            <div class="form-group">
                <label>Severity (1-10)</label>
                <input type="number" name="severity" id="edit_cve_severity" min="1" max="10" step="0.1" required>
            </div>

            <div class="form-group">
                <label>Date Reported</label>
                <input type="date" name="date_reported" id="edit_cve_date_reported" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_cve_description" required></textarea>
            </div>

            <div class="form-actions">
                <button type="button" id="delete-cve-modal" class="btn-delete-modal">🗑️ Delete CVE</button>
                <button type="submit">Save Changes</button>
            </div>
        </form>
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
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- EDIT NEWS MODAL -->
    <div id="edit-news-modal" class="edit-user-modal">
        <div class="top">
            <span class="top-title">Edit News Article:</span>
            <span id="close-news-modal">X</span>
        </div>
        <form method="POST" action="edit-news.php" class="edit-user-form">
            <input type="hidden" name="news_id" id="edit_news_id">

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="news_title" id="edit_news_title" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="news_description" id="edit_news_description" required></textarea>
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" id="edit_news_author" required>
            </div>

            <div class="form-group">
                <label>Article URL</label>
                <input type="url" name="article_url" id="edit_news_article_url" required>
            </div>

            <div class="form-actions">
                <button type="button" id="delete-news-modal" class="btn-delete-modal">🗑️ Delete Article</button>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>