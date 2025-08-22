<?php
session_start();
require_once '../includes/db.php';

// Ensure only members can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

$member_id = $_SESSION['user_id'];

// Fetch member's workout plan
$workout_stmt = $pdo->prepare("
    SELECT wp.title, wp.description, mp.assigned_at
    FROM member_plans mp
    JOIN workout_plans wp ON mp.workout_plan_id = wp.plan_id
    WHERE mp.member_id = ?
    ORDER BY mp.assigned_at DESC
    LIMIT 1
");
$workout_stmt->execute([$member_id]);
$workout = $workout_stmt->fetch();

// Fetch member's diet plan
$diet_stmt = $pdo->prepare("
    SELECT dp.title, dp.description, mp.assigned_at
    FROM member_plans mp
    JOIN diet_plans dp ON mp.diet_plan_id = dp.plan_id
    WHERE mp.member_id = ?
    ORDER BY mp.assigned_at DESC
    LIMIT 1
");
$diet_stmt->execute([$member_id]);
$diet = $diet_stmt->fetch();

// Fetch latest progress record
$progress_stmt = $pdo->prepare("
    SELECT weight, calories_burned, recorded_at
    FROM progress_records
    WHERE member_id = ?
    ORDER BY recorded_at DESC
    LIMIT 1
");
$progress_stmt->execute([$member_id]);
$progress = $progress_stmt->fetch();

// Fetch today's attendance
$attendance_stmt = $pdo->prepare("
    SELECT status
    FROM attendance
    WHERE member_id = ? AND checkin_date = CURDATE()
");
$attendance_stmt->execute([$member_id]);
$attendance = $attendance_stmt->fetch();

// Fetch unread messages
$messages_stmt = $pdo->prepare("
    SELECT COUNT(*) as unread
    FROM chat_messages
    WHERE receiver_id = ? AND sent_at > (
        SELECT COALESCE(MAX(sent_at), '1970-01-01')
        FROM chat_messages
        WHERE sender_id = ? AND receiver_id = ?
    )
");
$messages_stmt->execute([$member_id, $member_id, $member_id]);
$unread_messages = $messages_stmt->fetch()['unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Member Dashboard | FlexFusion</title>
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
    
    .welcome-card {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      animation: slideDown 0.5s ease-out;
    }
    
    .stats-card {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: all 0.3s;
      animation: fadeIn 0.5s ease-out;
    }
    
    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .stats-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 1rem;
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      color: white;
    }
    
    .stats-value {
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 0.5rem;
    }
    
    .stats-label {
      color: #666;
      font-size: 0.9rem;
    }
    
    .alert {
      border-radius: 15px;
      border: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      padding: 1.5rem;
      animation: fadeIn 0.5s ease-out;
    }
    
    .alert-primary {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      color: white;
    }
    
    .notification-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: var(--danger-color);
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      font-size: 0.8rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    @keyframes slideDown {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
    
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 2rem;
    }
    
    .action-card {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: all 0.3s;
      animation: fadeIn 0.5s ease-out;
    }
    
    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .action-icon {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: var(--primary-color);
    }
    
    .action-title {
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 0.5rem;
    }
    
    .action-description {
      color: #666;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white mb-4">
      <i class="fas fa-dumbbell me-2"></i>Member Panel
    </h4>
    <a href="dashboard.php" class="active"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="my_workout.php"><i class="fas fa-dumbbell me-2"></i>My Workout</a>
    <a href="my_diet.php"><i class="fas fa-utensils me-2"></i>My Diet</a>
    <a href="my_progress.php"><i class="fas fa-chart-line me-2"></i>My Progress</a>
    <a href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php" class="position-relative">
      <i class="fas fa-comments me-2"></i>Chat
      <?php if ($unread_messages > 0): ?>
        <span class="notification-badge"><?= $unread_messages ?></span>
      <?php endif; ?>
    </a>
    <a href="feedback.php"><i class="fas fa-comment-dots me-2"></i>Feedback</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="container-fluid">
      <!-- Welcome Card -->
      <div class="welcome-card">
        <h2><i class="fas fa-user-circle me-2"></i>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> 🏋️‍♀️</h2>
        <p class="mb-0">Track your fitness journey and stay motivated!</p>
      </div>

      <!-- Stats Cards -->
      <div class="row">
        <div class="col-md-4">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-dumbbell"></i>
                </div>
            <div class="stats-value">
              <?= $workout ? 'Active' : 'Not Assigned' ?>
            </div>
            <div class="stats-label">Workout Plan Status</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-fire"></i>
                </div>
            <div class="stats-value">
              <?= $progress ? number_format($progress['calories_burned']) : '0' ?>
            </div>
            <div class="stats-label">Calories Burned Today</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-calendar-check"></i>
                </div>
            <div class="stats-value">
              <?= $attendance ? ucfirst($attendance['status']) : 'Not Checked' ?>
            </div>
            <div class="stats-label">Today's Attendance</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <a href="my_workout.php" class="action-card text-decoration-none">
          <div class="action-icon">
            <i class="fas fa-dumbbell"></i>
          </div>
          <div class="action-title">View Workout Plan</div>
          <div class="action-description">Check your assigned exercises and routines</div>
        </a>
        <a href="my_diet.php" class="action-card text-decoration-none">
          <div class="action-icon">
            <i class="fas fa-utensils"></i>
          </div>
          <div class="action-title">View Diet Plan</div>
          <div class="action-description">Access your nutrition guidelines</div>
        </a>
        <a href="my_progress.php" class="action-card text-decoration-none">
          <div class="action-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="action-title">Track Progress</div>
          <div class="action-description">Monitor your fitness journey</div>
        </a>
        <a href="attendance.php" class="action-card text-decoration-none">
          <div class="action-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="action-title">Check Attendance</div>
          <div class="action-description">View your gym attendance history</div>
        </a>
        <a href="exercise_guide.php" class="action-card text-decoration-none">
          <div class="action-icon">
            <i class="fas fa-robot"></i>
          </div>
          <div class="action-title">Exercise Guide</div>
          <div class="action-description">AI-powered exercise tutorials and form analysis</div>
        </a>
      </div>

      <!-- Tip Alert -->
      <div class="alert alert-primary mt-4">
        <i class="fas fa-lightbulb me-2"></i>
        Tip: Stay consistent with your workouts and diet plan to achieve your fitness goals faster!
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
