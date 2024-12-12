<?php
include 'core/dbConfig.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id']; // Get receiver ID from URL

// Fetch messages between the sender and receiver
$stmt = $conn->prepare("SELECT m.message, m.created_at, u.username AS sender_name
                        FROM messages m
                        JOIN users u ON m.sender_id = u.user_id
                        WHERE (m.sender_id = ? AND m.receiver_id = ?)
                           OR (m.sender_id = ? AND m.receiver_id = ?)
                        ORDER BY m.created_at ASC");
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Messages</title>
</head>
<body>
    <h2>Conversation</h2>

    <?php if ($result->num_rows > 0): ?>
        <div>
            <?php while ($row = $result->fetch_assoc()): ?>
                <p><strong><?php echo htmlspecialchars($row['sender_name']); ?>:</strong> <?php echo htmlspecialchars($row['message']); ?>
                    <br><small><?php echo $row['created_at']; ?></small></p>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No messages yet.</p>
    <?php endif; ?>

    <br>
    <a href="send_message.php?receiver_id=<?php echo $receiver_id; ?>">Send New Message</a><br>
    <a href="hr_home.php">Back to HR Home</a>
</body>
</html>
