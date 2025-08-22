<?php
session_start();
require_once '../includes/db.php';

// Ensure only members can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

$member_id = $_SESSION['user_id'];

// Fetch progress records using PDO
$stmt = $pdo->prepare("SELECT weight, calories_burned, notes, recorded_at 
                       FROM progress_records 
                       WHERE member_id = :member_id 
                       ORDER BY recorded_at DESC");
$stmt->execute(['member_id' => $member_id]);
$progress_records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Progress | FlexFusion</title>
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
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px;
            font-weight: 500;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #eee;
        }
        
        .table tbody tr {
            transition: all 0.3s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .progress-value {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .progress-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .progress-notes {
            color: #666;
            font-style: italic;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .alert-info {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
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
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
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
            margin-bottom: 15px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .stats-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .stats-label {
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
    <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="my_workout.php"><i class="fas fa-dumbbell me-2"></i>My Workout</a>
    <a href="my_diet.php"><i class="fas fa-utensils me-2"></i>My Diet</a>
    <a href="my_progress.php" class="active"><i class="fas fa-chart-line me-2"></i>My Progress</a>
    <a href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="feedback.php"><i class="fas fa-comment-dots me-2"></i>Feedback</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h2><i class="fas fa-chart-line me-2"></i>My Progress 📈</h2>
        <p class="text-muted">Track your fitness progress over time.</p>

        <?php if (count($progress_records) > 0): ?>
            <!-- Progress Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-weight"></i>
                        </div>
                        <div class="stats-value">
                            <?= number_format($progress_records[0]['weight'], 1) ?> kg
                        </div>
                        <div class="stats-label">Current Weight</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="stats-value">
                            <?= number_format($progress_records[0]['calories_burned']) ?>
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
                            <?= count($progress_records) ?>
                        </div>
                        <div class="stats-label">Total Records</div>
                    </div>
                </div>
            </div>

            <!-- Progress Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Weight (kg)</th>
                                    <th>Calories Burned</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($progress_records as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="progress-date">
                                                <i class="fas fa-calendar me-2"></i>
                                                <?= date('d M Y', strtotime($row['recorded_at'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress-value">
                                                <?= number_format($row['weight'], 1) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress-value">
                                                <?= number_format($row['calories_burned']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress-notes">
                                                <?= htmlspecialchars($row['notes']) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No progress records found. Start tracking your fitness journey today!
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
