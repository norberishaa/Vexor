<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
require_once "classes/Cve.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php?tab=cve');
    exit();
}

// Get CVE ID from POST data
$cve_id = $_POST['cve_id'] ?? '';

// Validate CVE ID
if (empty($cve_id)) {
    $_SESSION['error'] = "CVE ID is required.";
    header('Location: admin.php?tab=cve');
    exit();
}

try {
    $cve = new Cve($conn);
    $result = $cve->delete($cve_id);
    
    if ($result) {
        $_SESSION['success'] = "CVE deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete CVE. It may not exist.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header('Location: admin.php?tab=cve');
exit();
?>