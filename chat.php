<?php
include('core/dbConfig.php');
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$receiver_id) {
    echo "No user selected.";
    exit;
}

// Fetch the conversation between the sender and receiver
$query = "SELECT * FROM messages 
          WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') 
          OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id') 
          ORDER BY created_at ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}
?>

<h2>Chat with User</h2>
<div id="chat-box">
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        // Determine the message sender
        $sender_name = ($row['sender_id'] == $sender_id) ? 'You' : 'Other User';
        $message_class = ($row['sender_id'] == $sender_id) ? 'sent' : 'received';
        echo "<div class='$message_class'>
                <strong>$sender_name:</strong>
                <p>{$row['message']}</p>
                <span>{$row['created_at']}</span>
              </div>";
    }
    ?>
</div>


<form action="send_message.php" method="POST">
    <textarea name="message" placeholder="Type a message..." required></textarea>
    <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
    <button type="submit">Send</button>
</form>
