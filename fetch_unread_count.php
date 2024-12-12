<?php
include 'core/dbConfig.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch unread messages count for the logged-in user
$query = "SELECT COUNT(*) AS unread_count FROM messages 
          WHERE receiver_id = '$user_id' AND status = 'unread'";
$result = $conn->query($query);
$row = $result->fetch_assoc();

echo json_encode(['unread_count' => $row['unread_count']]);
?>
