<?php
session_start();
require_once '../includes/db.php';

// Ensure only trainers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

$trainer_id = $_SESSION['user_id'];

// Fetch assigned members
$members_stmt = $pdo->prepare("
    SELECT u.user_id, u.full_name, u.profile_image,
           (SELECT COUNT(*) FROM chat_messages 
            WHERE sender_id = u.user_id 
            AND receiver_id = ? 
            AND sent_at > COALESCE(
                (SELECT MAX(sent_at) FROM chat_messages 
                 WHERE sender_id = ? AND receiver_id = u.user_id),
                '1970-01-01 00:00:00'
            )) as unread_count
    FROM members_trainers mt
    JOIN users u ON mt.member_id = u.user_id
    WHERE mt.trainer_id = ?
    ORDER BY u.full_name
");
$members_stmt->execute([$trainer_id, $trainer_id, $trainer_id]);
$members = $members_stmt->fetchAll();

// Get selected member (if any)
$selected_member_id = isset($_GET['member_id']) ? $_GET['member_id'] : null;

// Fetch chat history with selected member
$messages = [];
if ($selected_member_id) {
    $chat_stmt = $pdo->prepare("
        SELECT 
            cm.*,
            sender.full_name as sender_name,
            sender.profile_image as sender_image
        FROM chat_messages cm
        JOIN users sender ON cm.sender_id = sender.user_id
        WHERE (cm.sender_id = ? AND cm.receiver_id = ?)
           OR (cm.sender_id = ? AND cm.receiver_id = ?)
        ORDER BY cm.sent_at ASC
    ");
    $chat_stmt->execute([$trainer_id, $selected_member_id, $selected_member_id, $trainer_id]);
    $messages = $chat_stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat | FlexFusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light-color);
        }
        
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, var(--dark-color), #34495e);
            padding-top: 20px;
            position: fixed;
            width: 250px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #ffffffcc;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background-color: var(--primary-color);
            color: white;
            border-left-color: white;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        
        .chat-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: calc(100vh - 100px);
            display: flex;
        }
        
        .members-list {
            width: 300px;
            border-right: 1px solid #eee;
            overflow-y: auto;
        }
        
        .member-item {
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 1px solid #eee;
        }
        
        .member-item:hover {
            background-color: #f8f9fa;
        }
        
        .member-item.active {
            background-color: #e9ecef;
        }
        
        .member-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            position: relative;
        }
        
        .member-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .unread-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .member-info h5 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .member-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .message {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 15px;
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }
        
        .message.sent {
            background: var(--primary-color);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }
        
        .message.received {
            background: #f1f1f1;
            color: var(--dark-color);
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }
        
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 5px;
        }
        
        .chat-input {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        
        .chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
            transition: all 0.3s;
        }
        
        .chat-input input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .chat-input button {
            padding: 12px 25px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .chat-input button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .no-chat-selected {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
            text-align: center;
            padding: 20px;
        }
        
        .no-chat-selected i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center text-white mb-4">
        <i class="fas fa-dumbbell me-2"></i>Trainer Panel
    </h4>
    <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="my_members.php"><i class="fas fa-users me-2"></i>My Members</a>
    <a href="assign_workouts.php"><i class="fas fa-dumbbell me-2"></i>Assign Workouts</a>
    <a href="track_progress.php"><i class="fas fa-chart-line me-2"></i>Track Progress</a>
    <a href="manage_attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php" class="active"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="chat-container">
            <div class="members-list">
                <?php foreach ($members as $member): ?>
                    <div class="member-item <?= $selected_member_id == $member['user_id'] ? 'active' : '' ?>" 
                         onclick="window.location.href='?member_id=<?= $member['user_id'] ?>'">
                        <div class="member-avatar">
                            <?php if ($member['profile_image']): ?>
                                <img src="<?= htmlspecialchars($member['profile_image']) ?>" alt="Member">
                            <?php else: ?>
                                <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                            <?php endif; ?>
                            <?php if ($member['unread_count'] > 0): ?>
                                <span class="unread-badge"><?= $member['unread_count'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <h5><?= htmlspecialchars($member['full_name']) ?></h5>
                            <p>Member</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="chat-area">
                <?php if ($selected_member_id): ?>
                    <?php
                    $selected_member = array_filter($members, function($m) use ($selected_member_id) {
                        return $m['user_id'] == $selected_member_id;
                    });
                    $selected_member = reset($selected_member);
                    ?>
                    <div class="chat-header">
                        <div class="member-avatar">
                            <?php if ($selected_member['profile_image']): ?>
                                <img src="<?= htmlspecialchars($selected_member['profile_image']) ?>" alt="Member">
                            <?php else: ?>
                                <?= strtoupper(substr($selected_member['full_name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <h5><?= htmlspecialchars($selected_member['full_name']) ?></h5>
                            <p>Member</p>
                        </div>
                    </div>
                    
                    <div class="chat-messages" id="chatMessages">
                        <?php foreach ($messages as $message): ?>
                            <div class="message <?= $message['sender_id'] == $trainer_id ? 'sent' : 'received' ?>">
                                <div class="message-content">
                                    <?= htmlspecialchars($message['message']) ?>
                                </div>
                                <div class="message-time">
                                    <?= date('h:i A', strtotime($message['sent_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="chat-input">
                        <input type="text" id="messageInput" placeholder="Type your message..." autocomplete="off">
                        <button onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="no-chat-selected">
                        <i class="fas fa-comments"></i>
                        <h4>Select a member to start chatting</h4>
                        <p>Choose a member from the list to view and send messages</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    $.ajax({
        url: 'ajax/send_message.php',
        method: 'POST',
        data: {
            receiver_id: <?= $selected_member_id ?: 'null' ?>,
            message: message
        },
        success: function(response) {
            messageInput.value = '';
            appendMessage(message, 'sent');
            loadMessages();
        },
        error: function() {
            // Silently handle error
            console.log('Message send failed');
        }
    });
}

function appendMessage(message, type) {
    const messagesDiv = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    
    const timeString = new Date().toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit', 
        hour12: true 
    });
    
    messageDiv.innerHTML = `
        <div class="message-content">${message}</div>
        <div class="message-time">${timeString}</div>
    `;
    
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function loadMessages() {
    if (!<?= $selected_member_id ? 'true' : 'false' ?>) return;
    
    $.ajax({
        url: 'ajax/get_messages.php',
        method: 'GET',
        data: {
            member_id: <?= $selected_member_id ?: 'null' ?>
        },
        success: function(response) {
            if (response.success && response.messages) {
                const messagesDiv = document.getElementById('chatMessages');
                messagesDiv.innerHTML = '';
                
                response.messages.forEach(message => {
                    const type = message.sender_id == <?= $trainer_id ?> ? 'sent' : 'received';
                    appendMessage(message.message, type);
                });
            }
        }
    });
}

// Send message on Enter key
document.getElementById('messageInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Load messages every 2 seconds
setInterval(loadMessages, 2000);

// Initial load
loadMessages();
</script>
</body>
</html>
