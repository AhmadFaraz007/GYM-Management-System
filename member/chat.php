<?php
session_start();
require_once '../includes/db.php';

// Ensure only members can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

$member_id = $_SESSION['user_id'];

// Fetch assigned trainer
$trainer_stmt = $pdo->prepare("
    SELECT u.user_id, u.full_name, u.profile_image
    FROM members_trainers mt
    JOIN users u ON mt.trainer_id = u.user_id
    WHERE mt.member_id = ?
");
$trainer_stmt->execute([$member_id]);
$trainer = $trainer_stmt->fetch();

// Fetch chat history with trainer
$messages = [];
if ($trainer) {
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
    $chat_stmt->execute([$member_id, $trainer['user_id'], $trainer['user_id'], $member_id]);
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
            flex-direction: column;
        }
        
        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .chat-avatar {
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
        }
        
        .chat-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .chat-info h5 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .chat-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
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
        <i class="fas fa-dumbbell me-2"></i>Member Panel
    </h4>
    <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="my_workout.php"><i class="fas fa-dumbbell me-2"></i>My Workout</a>
    <a href="my_diet.php"><i class="fas fa-utensils me-2"></i>My Diet</a>
    <a href="my_progress.php"><i class="fas fa-chart-line me-2"></i>My Progress</a>
    <a href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php" class="active"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="feedback.php"><i class="fas fa-comment-dots me-2"></i>Feedback</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="chat-container">
            <div class="chat-header">
                <?php if ($trainer): ?>
                    <div class="chat-avatar">
                        <?php if ($trainer['profile_image']): ?>
                            <img src="<?= htmlspecialchars($trainer['profile_image']) ?>" alt="Trainer">
                        <?php else: ?>
                            <?= strtoupper(substr($trainer['full_name'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="chat-info">
                        <h5><?= htmlspecialchars($trainer['full_name']) ?></h5>
                        <p>Your Trainer</p>
                    </div>
                <?php else: ?>
                    <div class="chat-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="chat-info">
                        <h5>No Trainer Assigned</h5>
                        <p>Please wait for a trainer to be assigned to you</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= $message['sender_id'] == $member_id ? 'sent' : 'received' ?>">
                        <div class="message-content">
                            <?= htmlspecialchars($message['message']) ?>
                        </div>
                        <div class="message-time">
                            <?= date('h:i A', strtotime($message['sent_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($trainer): ?>
                <div class="chat-input">
                    <input type="text" id="messageInput" placeholder="Type your message..." autocomplete="off">
                    <button onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            <?php endif; ?>
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
            receiver_id: <?= $trainer ? $trainer['user_id'] : 'null' ?>,
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
    $.ajax({
        url: 'ajax/get_messages.php',
        method: 'GET',
        success: function(response) {
            if (response.success && response.messages) {
                const messagesDiv = document.getElementById('chatMessages');
                messagesDiv.innerHTML = '';
                
                response.messages.forEach(message => {
                    const type = message.sender_id == <?= $member_id ?> ? 'sent' : 'received';
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
