<?php
session_start();
include 'models.php';  // Include models for sendMessage function

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendMessageBtn'])) {
    $receiver_username = trim($_POST['receiver_username']);
    $message_text = trim($_POST['message_text']);
   
    if (!empty($receiver_username) && !empty($message_text)) {
        $sender_username = $_SESSION['username']; // Get the sender's username from session
       
        // Send the message
        if (sendMessage($pdo, $sender_username, $receiver_username, $message_text)) {
            $_SESSION['message'] = "Message sent successfully!";
            $_SESSION['status'] = "200";  // Success status
        } else {
            $_SESSION['message'] = "Failed to send message.";
            $_SESSION['status'] = "400";  // Failure status
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields!";
        $_SESSION['status'] = "400";  // Failure status
    }

    // Redirect back to the message page
    header("Location: hr_home.php?receiver=" . urlencode($receiver_username));
    exit();
}
?>
