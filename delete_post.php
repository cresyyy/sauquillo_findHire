<?php
include 'core/dbConfig.php';
session_start();

// Ensure only HR users can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hr') {
    header("Location: login.php");
    exit();
}

// Check if post_id is provided in the URL
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Check if the post belongs to the logged-in HR user
    $hr_id = $_SESSION['user_id'];
    
    // Prepare SQL to check if the post is associated with the logged-in HR user
    if ($stmt = $conn->prepare("SELECT posted_by FROM job_posts WHERE post_id = ?")) {
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->bind_result($posted_by);
        $stmt->fetch();
        $stmt->close();

        if ($posted_by == $hr_id) {
            // Delete the job post if it belongs to the logged-in HR user
            if ($stmt = $conn->prepare("DELETE FROM job_posts WHERE post_id = ?")) {
                $stmt->bind_param("i", $post_id);
                if ($stmt->execute()) {
                    header("Location: hr_home.php");  // Redirect to HR home after deletion
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            echo "You do not have permission to delete this post.";
        }
    }
} else {
    echo "No job post ID specified.";
}
?>
