<?php
session_start();
require_once '../../includes/db.php';

// Ensure only members can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$member_id = $_SESSION['user_id'];
$last_message_time = isset($_GET['last_message_time']) ? $_GET['last_message_time'] : '1970-01-01 00:00:00';

try {
    // Get trainer ID
    $trainer_stmt = $pdo->prepare("
        SELECT trainer_id
        FROM members_trainers
        WHERE member_id = ?
    ");
    $trainer_stmt->execute([$member_id]);
    $trainer = $trainer_stmt->fetch();
    
    if (!$trainer) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'messages' => []]);
        exit;
    }
    
    // Get new messages
    $stmt = $pdo->prepare("
        SELECT 
            cm.*,
            sender.full_name as sender_name,
            sender.profile_image as sender_image
        FROM chat_messages cm
        JOIN users sender ON cm.sender_id = sender.user_id
        WHERE ((cm.sender_id = ? AND cm.receiver_id = ?)
           OR (cm.sender_id = ? AND cm.receiver_id = ?))
           AND cm.sent_at > ?
        ORDER BY cm.sent_at ASC
    ");
    
    $stmt->execute([
        $member_id,
        $trainer['trainer_id'],
        $trainer['trainer_id'],
        $member_id,
        $last_message_time
    ]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 