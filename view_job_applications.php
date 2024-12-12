<?php
include 'core/dbConfig.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'applicant') {
    header("Location: login.php");
    exit();
}

$applicant_id = $_SESSION['user_id'];

// Fetch all job applications of the applicant
$applications = $conn->prepare("SELECT ja.application_id, ja.status, jp.title, jp.post_id 
                                FROM job_applications ja
                                JOIN job_posts jp ON ja.post_id = jp.post_id
                                WHERE ja.applicant_id = ?");
$applications->bind_param("i", $applicant_id);
$applications->execute();
$result = $applications->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Job Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
    <h2 class="text-center">My Job <span style="color: #0097b2;">Applications</span></h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead class="text-center">
                <tr>
                    <th>Job Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($app = $result->fetch_assoc()): ?>
                    <tr class="text-center">
                        <td><?php echo htmlspecialchars($app['title']); ?></td>
                        <td>
                            <?php if ($app['status'] == 'accepted'): ?>
                                <span class="badge bg-success">Accepted</span>
                            <?php elseif ($app['status'] == 'rejected'): ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No job applications submitted yet.</div>
    <?php endif; ?>

</div>
<br><br><br>

<footer>
    <p>&copy; 2024 FindHire. All rights reserved.</p>
</footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

