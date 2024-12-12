<?php
include 'core/dbConfig.php';
session_start();

// Ensure only HR users can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hr') {
    header("Location: login.php");
    exit();
}

// Fetch the job post ID from the URL
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Fetch the current details of the job post
    $stmt = $conn->prepare("SELECT title, description, location FROM job_posts WHERE post_id = ? AND posted_by = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($title, $description, $location);
    $stmt->fetch();

    // If no job post is found, redirect to the HR home page
    if ($stmt->num_rows == 0) {
        header("Location: hr_home.php");
        exit();
    }
    $stmt->close();

    // Handle the update of the job post
    if (isset($_POST['update_post'])) {
        $new_title = $_POST['title'];
        $new_description = $_POST['description'];
        $new_location = $_POST['location'];

        // Update the job post in the database
        $update_stmt = $conn->prepare("UPDATE job_posts SET title = ?, description = ?, location = ? WHERE post_id = ? AND posted_by = ?");
        $update_stmt->bind_param("ssiii", $new_title, $new_description, $new_location, $post_id, $_SESSION['user_id']);
        if ($update_stmt->execute()) {
            $message = "Job post updated successfully!";
        } else {
            $message = "Error: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
} else {
    header("Location: hr_home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar-nav .nav-link {
            font-weight: 600;
            color: #0097b2 !important;
        }

        .navbar-nav .nav-link:hover {
            text-decoration: underline;
        }

        .btn-update{
            background-color: #0097b2;
            color: white;
        }

        .btn-update:hover{
            border-color: #0097b2;
            color:#0097b2;
        }

        footer {
            background-color: #0097b2;
            color: #000000;
            padding: 10px 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="hr_home.php">
                <img src="assets/FINDHIRE_Navbar1.png" alt="Company Logo" width="100" height="30" class="d-inline-block align-text-top">
                HR Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="hr_home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hr_message.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Edit Job Post</h2>
        <?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>

        <div class="card mb-4">
            <div class="card-header">Edit Job Post</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Job Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-update" name="update_post">Update Post</button>
                </form>
            </div>
        </div>
    </div>
    <br><br><br>

<footer>
    <p>&copy; 2024 FindHire. All rights reserved.</p>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
