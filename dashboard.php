<?php
require_once "config/auth_check.php";
require_once "config/db.php";
require_once "classes/Cve.php";

$cve = new Cve($conn);

$result = $cve->getAll();
$stats = $cve->getStats();

$selectedCve = null;
if (isset($_GET['cve'])) {
    $selectedResult = $cve->getById($_GET['cve']);
    if ($selectedResult->num_rows > 0) {
        $selectedCve = $selectedResult->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fonts.css">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/vexor_logo/vexor_black.ico" />
    <title>Vexor - Dashboard</title>
</head>
<body>
    <nav class="nav-bar">
        <div class="nav-logo-container">
            <a href="index.html"><img src="images/vexor_logo/vexor_black.svg" id="nav-logo" alt="Vexor Logo"></a>
        </div>
        
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div class="nav-links" id="navLinks">
            <a href="index.html">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="news.php">News</a>
            <a href="contact.html">Contact</a>
            <button class="log-in" onclick="location.href='log-in.php'">Log In</button>
        </div>
    </nav>
    
    <div class="dashboard">
        <div class="dashboard-overview">
            <div class="dashboard-overview-item">
                <div class="overview-item-title">Total Tracked CVE's</div>
                <div class="overview-item-content"><?= $stats['total'] ?></div>
            </div>
            <div class="dashboard-overview-item">
                <div class="overview-item-title">Unpatched Vulnerabilities</div>
                <div class="overview-item-content red"><?= $stats['unpatched'] ?></div>
            </div>
            <div class="dashboard-overview-item">
                <div class="overview-item-title">Patched Vulnerabilities</div>
                <div class="overview-item-content green"><?= $stats['patched'] ?></div>
            </div>
            <div class="dashboard-overview-item">
                <div class="overview-item-title">High Severity</div>
                <div class="overview-item-content red"><?= $stats['high'] ?></div>
            </div>
            <div class="dashboard-overview-item">
                <div class="overview-item-title">Medium Severity</div>
                <div class="overview-item-content yellow"><?= $stats['medium'] ?></div>
            </div>
        </div>
        <div class="dashboard-cve-container">
            <div class="cve-table-container">
                <div class="table-head">
                    <h1>CVE Tracking Overview</h1>
                    <input type="text" id="cveSearch" placeholder="Search..">
                </div>
                <table class="cve-table">
                    <thead>
                        <tr>
                            <th >CVE ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Severity</th>
                            <th>Date Reported</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr data-cve="<?= htmlspecialchars($row['cve_id']) ?>">
                                    <td><?= htmlspecialchars($row['cve_id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['status']) ?></td>
                                    <td><?= htmlspecialchars($row['severity']) ?></td>
                                    <td><?= htmlspecialchars($row['date_reported']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No CVEs found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <?php if ($selectedCve): ?>
            <div class="cve-description-wrapper active">
                <div class="cve-description-container">

                    <h1><?= htmlspecialchars($selectedCve['name']) ?></h1>

                    <div class="short-description">
                        <h2><?= htmlspecialchars($selectedCve['cve_id']) ?></h2>
                        <h2><?= htmlspecialchars($selectedCve['severity']) ?>/10</h2>
                    </div>

                    <div class="short-description">
                        <h2><?= htmlspecialchars($selectedCve['status']) ?></h2>
                        <h2><?= htmlspecialchars($selectedCve['date_reported']) ?></h2>
                    </div>

                    <p><?= htmlspecialchars($selectedCve['description']) ?></p>

                </div>
            </div>
    <?php endif; ?>

    <script src="js/script.js"></script>
</body>
</html>