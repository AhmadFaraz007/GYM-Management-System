<?php
session_start();
require_once '../../includes/db.php';

// Basic validation
if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = trim($_POST['message']);

if (empty($message)) {
    die(json_encode(['success' => false, 'message' => 'Message cannot be empty']));
}

try {
    // Direct database insertion
    $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$sender_id, $receiver_id, $message])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} catch (PDOException $e) {
    error_log("Chat Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 