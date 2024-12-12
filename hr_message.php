<?php
include('core/dbConfig.php');
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$selected_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

// Fetch all users except the current user
$user_query = "SELECT * FROM users WHERE user_id != '$current_user_id'";
$user_result = mysqli_query($conn, $user_query);


if (!$user_result) {
    die("Error fetching users: " . mysqli_error($conn));
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['receiver_id'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $receiver_id = $_POST['receiver_id'];

    $insert_query = "INSERT INTO messages (sender_id, receiver_id, message, created_at) 
                     VALUES ('$current_user_id', '$receiver_id', '$message', NOW())";

    if (!mysqli_query($conn, $insert_query)) {
        die("Error sending message: " . mysqli_error($conn));
    }

    // Reload the conversation
    $selected_user_id = $receiver_id;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-nav .nav-link {
            font-weight: 600;
            color: #0097b2 !important;
        }

        .navbar-nav .nav-link:hover {
            text-decoration: underline;
        }

        .sent {
            text-align: right;
            background-color: #d3f8e2;
            padding: 10px;
            margin: 5px;
            border-radius: 10px;
            max-width: 60%;
            margin-left: auto;
        }

        .received {
            text-align: left;
            background-color: #e2e2e2;
            padding: 10px;
            margin: 5px;
            border-radius: 10px;
            max-width: 60%;
        }

        #chat-box {
            max-height: 400px;
            overflow-y: scroll;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        .message-container {
            display: flex;
            align-items: center;
            gap: 10px; 
        }

        .message-container textarea {
            flex: 1; 
            resize: none; 
            height: 50px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .message-container .btn-send {
            padding: 10px 20px;
            background-color: #0097b2;
            color: white;
            border: 1px solid #0097b2;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease; 
        }

        .message-container .btn-send:hover {
            background-color: white;
            color: #0097b2;
            border-color: #0097b2; 
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
        <h2 class="text-center">Messages</h2>

        <form action="hr_message.php" method="GET" class="mb-3">
            <label for="user_id">Select a User to Chat:</label>
            <select name="user_id" id="user_id" class="form-select" onchange="this.form.submit()">
                <option value="">Select a User</option>
                <?php
                if (mysqli_num_rows($user_result) > 0) {
                    while ($row = mysqli_fetch_assoc($user_result)) {
                        $selected = $selected_user_id == $row['user_id'] ? 'selected' : '';
                        echo "<option value='{$row['user_id']}' $selected>{$row['username']} ({$row['role']})</option>";
                    }
                } else {
                    echo "<option value=''>No users available</option>";
                }
                ?>
            </select>
        </form>

        <?php if ($selected_user_id): ?>
            <h3>Chat with <?php
                $user_query = "SELECT username FROM users WHERE user_id = '$selected_user_id'";
                $user_result = mysqli_query($conn, $user_query);
                $user_row = mysqli_fetch_assoc($user_result);
                echo $user_row['username'];
            ?></h3>
            <div id="chat-box" class="mb-4">
                <?php
                $message_query = "SELECT * FROM messages 
                                  WHERE (sender_id = '$current_user_id' AND receiver_id = '$selected_user_id') 
                                  OR (sender_id = '$selected_user_id' AND receiver_id = '$current_user_id') 
                                  ORDER BY created_at ASC";
                $message_result = mysqli_query($conn, $message_query);

                if ($message_result && mysqli_num_rows($message_result) > 0) {
                    while ($row = mysqli_fetch_assoc($message_result)) {
                        $message_class = ($row['sender_id'] == $current_user_id) ? 'sent' : 'received';
                        $sender_name = ($row['sender_id'] == $current_user_id) ? 'You' : $user_row['username'];
                        echo "<div class='$message_class'>
                                <strong>$sender_name:</strong>
                                <p>{$row['message']}</p>
                                <span>{$row['created_at']}</span>
                              </div>";
                    }
                } else {
                    echo "<p>No messages yet. Start the conversation!</p>";
                }
                ?>
            </div>

            <form action="applicant_message.php?user_id=<?php echo $selected_user_id; ?>" method="POST">
                <div class="message-container">
                    <textarea name="message" placeholder="Type a message..." required></textarea>
                    <button type="submit" class="btn btn-send">Send</button>
                </div>
                <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
            </form>
        <?php else: ?>
            <div class="alert alert-info">Select a user to start chatting.</div>
        <?php endif; ?>
    </div>
    <br><br><br>

<footer>
    <p>&copy; 2024 FindHire. All rights reserved.</p>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
