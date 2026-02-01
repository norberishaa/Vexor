<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
$active_tab = $_GET['tab'] ?? 'home';

$getUsers = $conn->prepare("SELECT user_id, emri, email, admin, data_e_krijimit FROM users");
$getUsers->execute();
$users_result = $getUsers->get_result();

$getCves = $conn->prepare("SELECT cve_id, name, status, severity, date_reported, description FROM cve_list ORDER BY severity DESC");
$getCves->execute();
$cves_result = $getCves->get_result();

$getNews = $conn->prepare("SELECT news_id, news_title, news_description, author, date_posted, article_url FROM news ORDER BY date_posted DESC");
$getNews->execute();
$news_result = $getNews->get_result();

$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalCves = $conn->query("SELECT COUNT(*) as count FROM cve_list")->fetch_assoc()['count'];
$totalNews = $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
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
                    <b>Production Environmet!</b>
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
                            <th>Admin Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <?php if ($users_result->num_rows > 0): ?>
                            <?php while ($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                                    <td><?= htmlspecialchars($user['emri']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php if ($user['admin'] === 'yes'): ?>
                                            <span class="badge badge-success">✅ Admin</span>
                                        <?php else: ?>
                                            <span class="badge badge-default">👤 User</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?=$user['data_e_krijimit']?></td>
                                    
                                    <td class="actions">
                                        <button class="btn-small btn-edit" 
                                                onclick="editUser(<?= $user['user_id'] ?>, '<?= htmlspecialchars($user['emri'], ENT_QUOTES) ?>', '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>', '<?= $user['admin'] ?>')">
                                            ✏️ Edit
                                        </button>
                                        <a href="toggle-admin.php?id=<?= $user['user_id'] ?>&return=admin.php?tab=users" 
                                        class="btn-small btn-toggle"
                                        onclick="return confirm('Toggle admin status for <?= htmlspecialchars($user['emri']) ?>?')">
                                            <?= $user['admin'] === 'yes' ? '⬇️ Demote' : '⬆️ Promote' ?>
                                        </a>
                                        
                                        <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                                            <a href="delete-user.php?id=<?= $user['user_id'] ?>&return=admin.php?tab=users" 
                                            class="btn-small btn-delete"
                                            onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($user['emri']) ?>? This cannot be undone!')">
                                                🗑️ Delete
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- CVE TAB -->
    <div class="tab-content <?= $active_tab === 'cve' ? 'active' : '' ?>">
        <div class="admin-cves">
            <h2>CVEs</h2>
            <p>Total CVEs: <?= $totalCves ?></p>
        </div>
    </div>

    <!-- NEWS TAB -->
    <div class="tab-content <?= $active_tab === 'news' ? 'active' : '' ?>">
        <div class="admin-news">
            <h2>News</h2>
            <p>Total news: <?= $totalNews ?></p>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>