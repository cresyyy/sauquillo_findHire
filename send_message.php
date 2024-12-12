<?php
include('core/dbConfig.php');
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = mysqli_real_escape_string($conn, $_POST['message']);
$timestamp = date('Y-m-d H:i:s');

// Insert the message into the database with 'unread' status
$query = "INSERT INTO messages (sender_id, receiver_id, message, status, created_at) 
          VALUES ('$sender_id', '$receiver_id', '$message', 'unread', '$timestamp')";

// Execute the query and check if it was successful
if (mysqli_query($conn, $query)) {
    // Redirect back to the chat page if the message is inserted
    header("Location: hr_message.php?user_id=$receiver_id");
    exit;
} else {
    die("Error inserting message: " . mysqli_error($conn));
}
?>
