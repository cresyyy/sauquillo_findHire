<?php
include 'core/dbConfig.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hr') {
    header("Location: login.php");
    exit();
}

$application_id = $_POST['application_id'];
$action = $_POST['action'];

// Validate the action
if ($action != 'accept' && $action != 'reject') {
    die("Invalid action.");
}

// Update the status of the application
$status = ($action == 'accept') ? 'accepted' : 'rejected';

// Update status in job_applications table
$update = $conn->prepare("UPDATE job_applications SET status = ? WHERE application_id = ?");
$update->bind_param("si", $status, $application_id);

if ($update->execute()) {
    header("Location: view_applications.php?status=success");
} else {
    echo "Error updating application status: " . $update->error;
}
?>
