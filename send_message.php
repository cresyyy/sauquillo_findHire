<?php
include('core/dbConfig.php');
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = mysqli_real_escape_string($conn, $_POST['message']);
$timestamp = date('Y-m-d H:i:s');

$query = "INSERT INTO messages (sender_id, receiver_id, message, status, created_at) 
          VALUES ('$sender_id', '$receiver_id', '$message', 'unread', '$timestamp')";

if (mysqli_query($conn, $query)) {
    header("Location: hr_message.php?user_id=$receiver_id");
    exit;
} else {
    die("Error inserting message: " . mysqli_error($conn));
}
?>
