<?php
include 'core/dbConfig.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hr') {
    header("Location: login.php");
    exit();
}

$post_id = $_GET['post_id'];

$applications = $conn->prepare("SELECT ja.application_id, ja.resume, ja.status, u.username AS applicant_name 
                                FROM job_applications ja 
                                JOIN users u ON ja.applicant_id = u.user_id 
                                WHERE ja.post_id = ?");
$applications->bind_param("i", $post_id);
$applications->execute();
$result = $applications->get_result();

// Handle accept/reject
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_id = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action == 'accept' || $action == 'reject') {
        // Update status for the application
        $status = ($action == 'accept') ? 'accepted' : 'rejected';
        $update = $conn->prepare("UPDATE job_applications SET status = ? WHERE application_id = ?");
        $update->bind_param("si", $status, $application_id);
        $update->execute();
        $update->close();
    }

    header("Location: view_applications.php?post_id=$post_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
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
            <a class="navbar-brand" href="hr_home.php">
                <img src="assets/FINDHIRE_navbar1.png" alt="Company Logo" width="100" height="30" class="d-inline-block align-text-top">
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
    <h2 class="text-center">
        Applications for Job Post: <span style="color: #0097b2;">
        <?php 
            
            $post_id = $_GET['post_id'];  
            $sql = "SELECT title FROM job_posts WHERE post_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $post_id);
                $stmt->execute();
                $stmt->bind_result($job_title);
                $stmt->fetch();
                $stmt->close();
                echo htmlspecialchars($job_title);  
            } else {
                echo "Error fetching job title.";
            }
        ?></span>
    </h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr class="text-center">
                    <th>Applicant Name</th>
                    <th>Status</th>
                    <th>Resume</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($app = $result->fetch_assoc()): ?>
                    <tr class="text-center">
                        <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                        <td>
                            <?php if ($app['status'] == 'accepted'): ?>
                                <span class="badge bg-success">Accepted</span>
                            <?php elseif ($app['status'] == 'rejected'): ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark" style="font-size:14px; margin-top: 5px;">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="uploads/<?php echo htmlspecialchars($app['resume']); ?>" target="_blank" class="btn btn-link">View Resume</a>
                        </td>
                        <td>
                            <?php if ($app['status'] == 'pending'): ?>
                                <form action="view_applications.php?post_id=<?php echo $post_id; ?>" method="POST">
                                    <input type="hidden" name="application_id" value="<?php echo $app['application_id']; ?>">
                                    <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No applications yet.</div>
    <?php endif; ?>

</div>
<br><br><br>

<footer>
    <p>&copy; 2024 FindHire. All rights reserved.</p>
</footer>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
