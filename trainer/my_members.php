<?php
// File: my_members.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

$trainer_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT u.* FROM members_trainers mt JOIN users u ON mt.member_id = u.user_id WHERE mt.trainer_id = ?");
    $stmt->execute([$trainer_id]);
    $members = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Members | FlexFusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #27ae60;
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
            background-color: rgba(46, 204, 113, 0.05);
        }
        
        h2 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .member-info {
            display: flex;
            align-items: center;
        }
        
        .member-name {
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .member-email {
            color: #666;
            font-size: 0.9rem;
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
    <a href="my_members.php" class="active"><i class="fas fa-users me-2"></i>My Members</a>
    <a href="assign_workouts.php"><i class="fas fa-dumbbell me-2"></i>Assign Workouts</a>
    <a href="track_progress.php"><i class="fas fa-chart-line me-2"></i>Track Progress</a>
    <a href="manage_attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h2><i class="fas fa-users me-2"></i>My Assigned Members</h2>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <div class="member-info">
                                            <div class="member-avatar">
                                                <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="member-name"><?= htmlspecialchars($member['full_name']) ?></div>
                                                <div class="member-email"><?= htmlspecialchars($member['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($member['email']) ?></td>
                                    <td><?= htmlspecialchars($member['phone']) ?></td>
                                    <td><?= htmlspecialchars($member['gender']) ?></td>
                                    <td><?= htmlspecialchars($member['date_of_birth']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
