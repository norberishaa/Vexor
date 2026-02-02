<?php
require_once "config/auth_admin.php";
require_once "config/db.php";
require_once "classes/Cve.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php?tab=cve');
    exit();
}

$cve_id = $_POST['cve_id'] ?? '';
$name = $_POST['name'] ?? '';
$status = $_POST['status'] ?? '';
$severity = $_POST['severity'] ?? '';
$date_reported = $_POST['date_reported'] ?? '';
$description = $_POST['description'] ?? '';

// Validate required fields
if (empty($cve_id) || empty($name) || empty($status) || empty($severity) || empty($date_reported) || empty($description)) {
    $_SESSION['error'] = "All fields are required.";
    header('Location: admin.php?tab=cve');
    exit();
}

// Validate severity is between 1 and 10
if ($severity < 1 || $severity > 10) {
    $_SESSION['error'] = "Severity must be between 1 and 10.";
    header('Location: admin.php?tab=cve');
    exit();
}

// Validate status
$valid_statuses = ['Not Patched', 'Patched'];
if (!in_array($status, $valid_statuses)) {
    $_SESSION['error'] = "Invalid status value.";
    header('Location: admin.php?tab=cve');
    exit();
}

try {
    $cve = new Cve($conn);
    $result = $cve->update($cve_id, $name, $status, $severity, $date_reported, $description);
    
    if ($result) {
        $_SESSION['success'] = "CVE updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update CVE.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header('Location: admin.php?tab=cve');
exit();
?>