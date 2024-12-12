<?php

function getAllUsers($pdo, $username) {
    $query = "SELECT * FROM users WHERE username != :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sendMessage($pdo, $sender_username, $receiver_username, $message_text) {
    try {
        $sql = "INSERT INTO messages (sender_username, receiver_username, message_text) VALUES (:sender_username, :receiver_username, :message_text)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':sender_username', $sender_username);
        $stmt->bindParam(':receiver_username', $receiver_username);
        $stmt->bindParam(':message_text', $message_text);
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getMessages($pdo, $sender_username, $receiver_username) {
    try {
        $sql = "SELECT sender_username, message_text, timestamp FROM messages
                WHERE (sender_username = :sender_username AND receiver_username = :receiver_username)
                OR (sender_username = :receiver_username AND receiver_username = :sender_username)
                ORDER BY timestamp DESC"; // Order by timestamp in descending order
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':sender_username', $sender_username);
        $stmt->bindParam(':receiver_username', $receiver_username);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

?>
