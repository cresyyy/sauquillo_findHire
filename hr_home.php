<?php
include 'core/dbConfig.php';
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hr') {
    header("Location: login.php");
    exit();
}


$hr_id = $_SESSION['user_id'];
$username = '';
$job_posts = null;

if ($stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?")) {
    $stmt->bind_param("i", $hr_id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
}

// Handle job post creation
if (isset($_POST['create_post'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];

    if ($stmt = $conn->prepare("INSERT INTO job_posts (title, description, location, posted_by) VALUES (?, ?, ?, ?)")) {
        $stmt->bind_param("sssi", $title, $description, $location, $hr_id);
        if ($stmt->execute()) {
            $message = "Job post created successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

if ($stmt = $conn->prepare("
    SELECT jp.post_id, jp.title, jp.description, jp.location,
           u.username AS posted_by_username,
           COUNT(ja.application_id) AS application_count
    FROM job_posts jp
    LEFT JOIN job_applications ja ON jp.post_id = ja.post_id
    JOIN users u ON jp.posted_by = u.user_id
    WHERE jp.posted_by = ?
    GROUP BY jp.post_id
    ORDER BY jp.created_at DESC
")) {
    $stmt->bind_param("i", $hr_id);
    $stmt->execute();
    $job_posts = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .card {
            box-shadow: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: #0097b2;
            opacity: 100%;
        }

        .post-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .post-buttons-left, .post-buttons-right {
            display: flex;
        }

        .post-buttons-left {
            justify-content: flex-start;
        }

        .post-buttons-right {
            justify-content: flex-end;
        }

        .btn-create {
            background-color: #0097b2;
            color: white;
        }

        .btn-create:hover {
            border-color: #0097b2;
            color: #0097b2;
        }

        .btn-view {
            border-color: #0097b2;
            color: #0097b2;
        }

        .btn-view:hover {
            background-color: #0097b2;
            color: white;
        }

        .btn-edit {
            background-color: #00171d;
            color: white;
        }

        .btn-edit:hover {
            border-color: #00171d;
            color: #00171d;
        }

        .btn-delete {
            background-color: #b21b00;
            color: white;
        }

        .btn-delete:hover {
            border-color: #b21b00;
            color: #b21b00;
        }

        .navbar-nav .nav-link {
            font-weight: 600;
            color: #0097b2 !important;
        }

        .navbar-nav .nav-link:hover {
            text-decoration: underline;
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
                HR's Portal
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
        <h2 class="text-center">Welcome, HR <span style="color: #0097b2;"><?php echo htmlspecialchars($username); ?>!</span></h2>
        <?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>

        <div class="card mb-4">
            <div class="card-header" style="font-weight: 600;">Create Job Post</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Job Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <button type="submit" class="btn btn-create" name="create_post">Create Post</button>
                </form>
            </div>
        </div>

        <h3>Jobs Posted</h3>
        <?php if ($job_posts->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $job_posts->fetch_assoc()): ?>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="font-weight:600"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text">Description: <?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="card-text">Location: <?php echo htmlspecialchars($row['location']); ?></p>
                                <p class="card-text">Posted by: <?php echo htmlspecialchars($row['posted_by_username']); ?></p>
                                <p class="card-text">Applications: <span class="badge bg-secondary"><?php echo htmlspecialchars($row['application_count']); ?></span></p>

                                <div class="post-buttons">
                                    <div class="post-buttons-left">
                                        <a href="view_applications.php?post_id=<?php echo $row['post_id']; ?>" class="btn btn-view">View Applications</a>
                                    </div>
                                    <div class="post-buttons-right">
                                        <a href="edit_post.php?post_id=<?php echo $row['post_id']; ?>" class="btn btn-edit" style="margin-right:3px">Edit Post</a>
                                        <a href="delete_post.php?post_id=<?php echo $row['post_id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this job post?');">Delete Post</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No job posts available.</div>
        <?php endif; ?>
    </div>
    <br><br><br>

    <footer>
        <p>&copy; 2024 FindHire. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
