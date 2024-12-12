<?php
include 'core/dbConfig.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_application'])) {
    $post_id = $_POST['post_id'];
    $applicant_id = $_SESSION['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // Ensure the uploads directory exists
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Handle file upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resume_name = time() . "_" . basename($_FILES['resume']['name']);
        $target_file = $target_dir . $resume_name;

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO job_applications (applicant_id, post_id, resume, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("iis", $applicant_id, $post_id, $resume_name);

            if ($stmt->execute()) {
                header("Location: applicant_home.php?success=1");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Failed to upload resume.";
        }
    } else {
        echo "Please upload a valid resume.";
    }
}
?>
