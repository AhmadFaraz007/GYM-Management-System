<?php
session_start();
require_once '../includes/db.php';

// Only members allowed
if ($_SESSION['role'] !== 'member') header("Location: ../auth/login.php");
$member_id = $_SESSION['user_id'];

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO feedback (member_id, message) VALUES (?, ?)");
        $stmt->execute([$member_id, $message]);
        $success_message = "Thank you for your feedback!";
    }
}

// Fetch member's previous feedback
$stmt = $pdo->prepare("
    SELECT message, submitted_at 
    FROM feedback 
    WHERE member_id = ? 
    ORDER BY submitted_at DESC
");
$stmt->execute([$member_id]);
$previous_feedback = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | FlexFusion</title>
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
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            background: white;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .feedback-form {
            padding: 2rem;
        }
        
        .feedback-history {
            margin-top: 2rem;
        }
        
        .feedback-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        
        .feedback-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .feedback-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .feedback-message {
            margin-top: 0.5rem;
            color: #2c3e50;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 10px 25px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        textarea.form-control {
            border-radius: 10px;
            padding: 15px;
            resize: none;
            height: 150px;
        }
        
        textarea.form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
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
    <a href="feedback.php" class="active"><i class="fas fa-comment-dots me-2"></i>Feedback</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h2><i class="fas fa-comment-dots me-2"></i>Feedback</h2>
        <p class="text-muted">Share your thoughts and suggestions with us!</p>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card feedback-form">
                    <h4 class="mb-4">Submit Feedback</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea class="form-control" id="message" name="message" required 
                                    placeholder="Share your thoughts, suggestions, or concerns..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card feedback-history">
                    <h4 class="mb-4">Your Previous Feedback</h4>
                    <?php if (empty($previous_feedback)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No feedback submitted yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($previous_feedback as $feedback): ?>
                            <div class="feedback-item">
                                <div class="feedback-date">
                                    <i class="fas fa-clock me-2"></i>
                                    <?= date('F d, Y H:i', strtotime($feedback['submitted_at'])) ?>
                                </div>
                                <div class="feedback-message">
                                    <?= nl2br(htmlspecialchars($feedback['message'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 