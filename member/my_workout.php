<?php
session_start();
require_once '../includes/db.php';
// Only members allowed
if ($_SESSION['role'] !== 'member') header("Location: ../auth/login.php");
$member_id = $_SESSION['user_id'];

// Fetch most recent workout assignment
$stmt = $pdo->prepare("
    SELECT wp.title, wp.description, mp.assigned_at
    FROM member_plans mp
    JOIN workout_plans wp ON mp.workout_plan_id = wp.plan_id
    WHERE mp.member_id = ?
    ORDER BY mp.assigned_at DESC
    LIMIT 1
");
$stmt->execute([$member_id]);
$workout = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Workout | FlexFusion</title>
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
        
        .workout-title {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }
        
        .workout-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .workout-date {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .alert-warning {
            background: linear-gradient(45deg, var(--warning-color), #f39c12);
            color: white;
        }
        
        h2 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .text-muted {
            color: #7f8c8d !important;
        }
        
        .workout-card {
            padding: 2rem;
        }
        
        .workout-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
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
    <a href="my_workout.php" class="active"><i class="fas fa-dumbbell me-2"></i>My Workout</a>
    <a href="my_diet.php"><i class="fas fa-utensils me-2"></i>My Diet</a>
    <a href="my_progress.php"><i class="fas fa-chart-line me-2"></i>My Progress</a>
    <a href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="feedback.php"><i class="fas fa-comment-dots me-2"></i>Feedback</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
  </div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h2><i class="fas fa-dumbbell me-2"></i>My Workout Plan 💪</h2>
        <p class="text-muted">View your current workout plan and stay motivated!</p>

    <?php if ($workout): ?>
            <div class="card workout-card">
                <div class="workout-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <h4 class="workout-title"><?= htmlspecialchars($workout['title']) ?></h4>
                <div class="workout-description">
                    <?= nl2br(htmlspecialchars($workout['description'])) ?>
                </div>
                <div class="workout-date">
                    <i class="fas fa-calendar-alt"></i>
                    Assigned: <?= date('d M Y', strtotime($workout['assigned_at'])) ?>
                </div>
      </div>
    <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                No workout plan assigned yet. Your trainer will assign you a plan soon!
            </div>
    <?php endif; ?>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
