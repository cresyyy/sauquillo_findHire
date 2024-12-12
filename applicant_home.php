<?php
include 'core/dbConfig.php';
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'applicant') {
    header("Location: login.php");
    exit();
}

$applicant_id = $_SESSION['user_id'];

// Fetch the username of the logged-in applicant
$result = $conn->query("SELECT username FROM users WHERE user_id = $applicant_id");
$user = $result->fetch_assoc();
$username = $user['username'];


$job_posts = $conn->query("SELECT jp.post_id, jp.title, jp.description, jp.location, 
                                  EXISTS (SELECT 1 FROM job_applications ja 
                                          WHERE ja.post_id = jp.post_id 
                                          AND ja.applicant_id = $applicant_id) AS has_applied
                           FROM job_posts jp
                           ORDER BY jp.created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-nav .nav-link {
            font-weight: 600;
            color: #0097b2 !important;
        }

        .navbar-nav .nav-link:hover {
            text-decoration: underline;
        }
        .btn-apply{
         background-color: #0097b2;
         color: white;
        }

        .btn-apply:hover{
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
            <a class="navbar-brand" href="applicant_home.php">
                <img src="assets/FINDHIRE_navbar1.png" alt="Company Logo" width="100" height="30" class="d-inline-block align-text-top">
                Applicant's Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="applicant_home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_job_applications.php">My Applications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="applicant_message.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Welcome, Applicant <span style="color: #0097b2;"><?php echo htmlspecialchars($username); ?>!</span></h2>

        <h3 class="mt-4">Available Job Posts</h3>
        <?php if ($job_posts->num_rows > 0): ?>
            <div class="row">
                <?php while ($post = $job_posts->fetch_assoc()): ?>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                    <?php if ($post['has_applied']): ?>
                                        <span class="badge bg-success">Applied</span>
                                    <?php endif; ?>
                                </h5>
                                <p class="card-text">Description: <?php echo htmlspecialchars($post['description']); ?></p>
                                <p class="card-text">Location: <?php echo htmlspecialchars($post['location']); ?></p>
                                <?php if (!$post['has_applied']): ?>
                                    <button class="btn btn-apply" onclick="showApplicationForm(<?php echo $post['post_id']; ?>)">Apply</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No job posts available.</div>
        <?php endif; ?>
    </div>

    <div id="applicationOverlay" class="overlay d-none">
        <div class="modal-content card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Apply for Job Post</h5>
                <button type="button" class="btn-close" aria-label="Close" onclick="closeApplicationForm()"></button>
            </div>
            <div class="card-body">
                <form id="applicationForm" action="submit_application.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="post_id" id="post_id">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="resume" class="form-label">Resume</label>
                        <input type="file" class="form-control" name="resume" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit_application">Submit Application</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    
        function showApplicationForm(postId) {
            document.getElementById('post_id').value = postId; 
            document.getElementById('applicationOverlay').classList.remove('d-none'); 
        }

        
        function closeApplicationForm() {
            document.getElementById('applicationOverlay').classList.add('d-none'); 
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <br><br><br>

<footer>
    <p>&copy; 2024 FindHire. All rights reserved.</p>
</footer>
</body>
</html>
