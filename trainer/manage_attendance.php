<?php
session_start();
require_once '../includes/db.php';

// Ensure only trainers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

$trainer_id = $_SESSION['user_id'];

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $status = $_POST['status'];
    $checkin_date = $_POST['checkin_date'];

    // Check if attendance already exists for this member and date
    $check = $pdo->prepare("SELECT id FROM attendance WHERE member_id = ? AND checkin_date = ?");
    $check->execute([$member_id, $checkin_date]);
    
    if ($check->rowCount() > 0) {
        // Update existing attendance
        $stmt = $pdo->prepare("UPDATE attendance SET status = ? WHERE member_id = ? AND checkin_date = ?");
        $stmt->execute([$status, $member_id, $checkin_date]);
    } else {
        // Insert new attendance
        $stmt = $pdo->prepare("INSERT INTO attendance (member_id, checkin_date, status) VALUES (?, ?, ?)");
        $stmt->execute([$member_id, $checkin_date, $status]);
    }
    
    header("Location: manage_attendance.php?success=1");
    exit;
}

// Get selected date from query parameter or default to today
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch assigned members
$members = $pdo->prepare("
    SELECT u.user_id, u.full_name, u.profile_image,
           (SELECT COUNT(*) FROM attendance a 
            WHERE a.member_id = u.user_id 
            AND a.status = 'present' 
            AND a.checkin_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as attendance_count
    FROM members_trainers mt 
    JOIN users u ON mt.member_id = u.user_id 
    WHERE mt.trainer_id = ?
    ORDER BY u.full_name
");
$members->execute([$trainer_id]);
$members = $members->fetchAll();

// Fetch attendance for selected date
$attendance = $pdo->prepare("
    SELECT a.*, u.full_name 
    FROM attendance a 
    JOIN users u ON a.member_id = u.user_id 
    WHERE a.checkin_date = ? 
    AND a.member_id IN (
        SELECT member_id 
        FROM members_trainers 
        WHERE trainer_id = ?
    )
    ORDER BY u.full_name
");
$attendance->execute([$selected_date, $trainer_id]);
$attendance = $attendance->fetchAll();

// Create a lookup array for attendance
$attendance_lookup = [];
foreach ($attendance as $record) {
    $attendance_lookup[$record['member_id']] = $record;
}

// Calculate attendance statistics
$total_members = count($members);
$present_count = 0;
$absent_count = 0;
foreach ($attendance_lookup as $record) {
    if ($record['status'] === 'present') $present_count++;
    else $absent_count++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance | FlexFusion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --trainer-primary: #2ecc71;
            --trainer-secondary: #27ae60;
            --trainer-dark: #2c3e50;
            --trainer-light: #ecf0f1;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--trainer-light);
        }
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, var(--trainer-dark), #34495e);
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
            background-color: var(--trainer-primary);
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
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .status-present {
            background-color: var(--trainer-primary);
            color: white;
        }
        .status-absent {
            background-color: #e74c3c;
            color: white;
        }
        .btn-attendance {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
        }
        .btn-attendance:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .date-header {
            background: linear-gradient(45deg, var(--trainer-primary), var(--trainer-secondary));
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .member-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--trainer-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .attendance-notes {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }
        .date-picker {
            background: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .attendance-streak {
            font-size: 0.8rem;
            color: var(--trainer-primary);
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center text-white mb-4">
        <i class="fas fa-dumbbell me-2"></i>Trainer Panel
    </h4>
    <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="my_members.php"><i class="fas fa-users me-2"></i>My Members</a>
    <a href="assign_workouts.php"><i class="fas fa-dumbbell me-2"></i>Assign Workouts</a>
    <a href="track_progress.php"><i class="fas fa-chart-line me-2"></i>Track Progress</a>
    <a href="manage_attendance.php" class="active"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<div class="main-content">
    <div class="container-fluid">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Attendance updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="date-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-calendar me-2"></i>Attendance Management
                </h4>
                <form class="d-flex align-items-center">
                    <input type="date" name="date" class="date-picker me-2" 
                           value="<?= $selected_date ?>" 
                           onchange="this.form.submit()">
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <h6 class="text-muted mb-2">Total Members</h6>
                    <h3 class="mb-0"><?= $total_members ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h6 class="text-muted mb-2">Present Today</h6>
                    <h3 class="mb-0 text-success"><?= $present_count ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h6 class="text-muted mb-2">Absent Today</h6>
                    <h3 class="mb-0 text-danger"><?= $absent_count ?></h3>
                </div>
            </div>
        </div>

        <div class="row">
            <?php foreach ($members as $member): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="member-avatar me-3">
                                        <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">
                                            <?= htmlspecialchars($member['full_name']) ?>
                                        </h5>
                                        <div class="attendance-streak">
                                            <i class="fas fa-fire me-1"></i>
                                            <?= $member['attendance_count'] ?> days present this month
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($attendance_lookup[$member['user_id']])): ?>
                                    <span class="status-badge status-<?= $attendance_lookup[$member['user_id']]['status'] ?>">
                                        <?= ucfirst($attendance_lookup[$member['user_id']]['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="member_id" value="<?= $member['user_id'] ?>">
                                <input type="hidden" name="checkin_date" value="<?= $selected_date ?>">
                                
                                <button type="submit" name="status" value="present" 
                                        class="btn btn-success btn-attendance flex-grow-1 <?= isset($attendance_lookup[$member['user_id']]) && $attendance_lookup[$member['user_id']]['status'] === 'present' ? 'active' : '' ?>">
                                    <i class="fas fa-check me-2"></i>Present
                                </button>
                                
                                <button type="submit" name="status" value="absent"
                                        class="btn btn-danger btn-attendance flex-grow-1 <?= isset($attendance_lookup[$member['user_id']]) && $attendance_lookup[$member['user_id']]['status'] === 'absent' ? 'active' : '' ?>">
                                    <i class="fas fa-times me-2"></i>Absent
                                </button>
                            </form>

                            <?php if (isset($attendance_lookup[$member['user_id']])): ?>
                                <div class="attendance-notes">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Last updated: <?= date('M d, Y', strtotime($attendance_lookup[$member['user_id']]['checkin_date'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 