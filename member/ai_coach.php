<?php
session_start();
require_once '../includes/db.php';

// Only members allowed
if ($_SESSION['role'] !== 'member') header("Location: ../auth/login.php");
$member_id = $_SESSION['user_id'];

// Handle chat messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    
    // Default response in case Python script fails
    $response = "I'm here to help you with your fitness journey! Please ask me about workouts, nutrition, or any fitness-related questions.";
    
    // Try to call Python script to get AI response
    $command = "python ../ai_coach/simple_chatbot.py " . escapeshellarg($message);
    $output = shell_exec($command);
    
    // Check if we got a valid response from Python
    if ($output && trim($output) !== '') {
        $response = trim($output);
    }
    
    // Ensure response is not null or empty
    if (empty($response)) {
        $response = "I'm here to help you with your fitness journey! Please ask me about workouts, nutrition, or any fitness-related questions.";
    }
    
    // Save chat history to database
    $stmt = $pdo->prepare("INSERT INTO ai_chat_history (member_id, user_message, ai_response) VALUES (?, ?, ?)");
    $stmt->execute([$member_id, $message, $response]);
}

// Fetch chat history
$stmt = $pdo->prepare("
    SELECT user_message, ai_response, created_at 
    FROM ai_chat_history 
    WHERE member_id = ? 
    ORDER BY created_at DESC 
    LIMIT 50
");
$stmt->execute([$member_id]);
$chat_history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Fitness Coach | FlexFusion</title>
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
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .message {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }
        
        .message.user {
            align-items: flex-end;
        }
        
        .message.ai {
            align-items: flex-start;
        }
        
        .message-content {
            max-width: 70%;
            padding: 15px;
            border-radius: 15px;
            position: relative;
        }
        
        .message.user .message-content {
            background: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 15px;
        }
        
        .message.ai .message-content {
            background: #f8f9fa;
            color: var(--dark-color);
            border-radius: 15px 15px 15px 0;
        }
        
        .message-time {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }
        
        .chat-input {
            padding: 20px;
            border-top: 1px solid #eee;
            background: white;
            border-radius: 0 0 15px 15px;
        }
        
        .chat-input form {
            display: flex;
            gap: 10px;
        }
        
        .chat-input textarea {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            resize: none;
            height: 50px;
        }
        
        .chat-input button {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .chat-input button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .ai-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 10px;
        }
        
        .typing-indicator {
            display: none;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #666;
            border-radius: 50%;
            margin-right: 5px;
            animation: typing 1s infinite;
        }
        
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .suggestions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .suggestion-chip {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .suggestion-chip:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
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
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="feedback.php"><i class="fas fa-comment-dots me-2"></i>Feedback</a>
    <a href="exercise_guide.php"><i class="fas fa-dumbbell me-2"></i>Exercise Guide</a>
    <a href="ai_coach.php" class="active"><i class="fas fa-robot me-2"></i>AI Coach</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="chat-container">
            <div class="chat-header">
                <h4><i class="fas fa-robot me-2"></i>AI Fitness Coach</h4>
                <p class="mb-0">Your personalized fitness assistant</p>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <!-- Welcome Message -->
                <div class="message ai">
                    <div class="message-content">
                        <p>Hello! I'm your AI Fitness Coach. I can help you with:</p>
                        <ul>
                            <li>Workout recommendations</li>
                            <li>Nutrition advice</li>
                            <li>Form guidance</li>
                            <li>Progress tracking</li>
                            <li>Motivation and support</li>
                        </ul>
                        <p>How can I help you today?</p>
                    </div>
                    <div class="message-time"><?= date('H:i') ?></div>
                </div>
                
                <!-- Chat History -->
                <?php foreach (array_reverse($chat_history) as $chat): ?>
                <div class="message user">
                    <div class="message-content">
                        <?= nl2br(htmlspecialchars($chat['user_message'])) ?>
                    </div>
                    <div class="message-time"><?= date('H:i', strtotime($chat['created_at'])) ?></div>
                </div>
                
                <div class="message ai">
                    <div class="message-content">
                        <?= nl2br(htmlspecialchars($chat['ai_response'])) ?>
                    </div>
                    <div class="message-time"><?= date('H:i', strtotime($chat['created_at'])) ?></div>
                </div>
                <?php endforeach; ?>
                
                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typingIndicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            
            <!-- Quick Suggestions -->
            <div class="suggestions">
                <div class="suggestion-chip" onclick="sendSuggestion('What workout should I do today?')">Workout Advice</div>
                <div class="suggestion-chip" onclick="sendSuggestion('How can I improve my diet?')">Nutrition Tips</div>
                <div class="suggestion-chip" onclick="sendSuggestion('I need motivation')">Get Motivated</div>
                <div class="suggestion-chip" onclick="sendSuggestion('How can I track my progress?')">Progress Tracking</div>
            </div>
            
            <div class="chat-input">
                <form id="chatForm" method="POST">
                    <textarea name="message" placeholder="Type your message..." required></textarea>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const message = form.querySelector('textarea').value;
    
    // Show typing indicator
    document.getElementById('typingIndicator').style.display = 'block';
    
    // Add user message to chat
    addMessage(message, 'user');
    
    // Submit form
    fetch('ai_coach.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message=' + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(html => {
        // Hide typing indicator
        document.getElementById('typingIndicator').style.display = 'none';
        
        // Reload page to show new messages
        location.reload();
    });
    
    // Clear input
    form.querySelector('textarea').value = '';
});

function addMessage(message, type) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    
    messageDiv.innerHTML = `
        <div class="message-content">
            ${message}
        </div>
        <div class="message-time">${new Date().toLocaleTimeString()}</div>
    `;
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function sendSuggestion(suggestion) {
    const textarea = document.querySelector('textarea');
    textarea.value = suggestion;
    document.getElementById('chatForm').dispatchEvent(new Event('submit'));
}
</script>
</body>
</html> 