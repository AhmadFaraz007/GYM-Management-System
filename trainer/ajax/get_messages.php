<?php
session_start();
require_once '../../includes/db.php';

// Ensure only trainers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'trainer') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$trainer_id = $_SESSION['user_id'];
$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : null;
$last_message_time = isset($_GET['last_message_time']) ? $_GET['last_message_time'] : '1970-01-01 00:00:00';

if (!$member_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Member ID is required']);
    exit;
}

try {
    // Verify that the member is assigned to this trainer
    $check_stmt = $pdo->prepare("
        SELECT 1 FROM members_trainers 
        WHERE trainer_id = ? AND member_id = ?
    ");
    $check_stmt->execute([$trainer_id, $member_id]);
    
    if (!$check_stmt->fetch()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid member']);
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
    
    $stmt->execute([$trainer_id, $member_id, $member_id, $trainer_id, $last_message_time]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 