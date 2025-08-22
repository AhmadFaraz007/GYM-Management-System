<?php
session_start();
require_once '../includes/db.php';

// Check if trainer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit();
}

$trainer_id = $_SESSION['user_id'];

// Fetch members assigned to this trainer
$member_stmt = $pdo->prepare("
    SELECT u.user_id, u.full_name
    FROM members_trainers mt
    JOIN users u ON mt.member_id = u.user_id
    WHERE mt.trainer_id = ?
");
$member_stmt->execute([$trainer_id]);
$members = $member_stmt->fetchAll();

// Fetch all workout and diet plans
$workouts = $pdo->query("SELECT plan_id, title FROM workout_plans")->fetchAll();
$diets = $pdo->query("SELECT plan_id, title FROM diet_plans")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $workout_id = $_POST['workout_plan'];
    $diet_id = $_POST['diet_plan'];

    // Insert into member_plans
    $insert_stmt = $pdo->prepare("
        INSERT INTO member_plans (member_id, workout_plan_id, diet_plan_id)
        VALUES (?, ?, ?)
    ");
    $insert_stmt->execute([$member_id, $workout_id, $diet_id]);

    $success_message = "Plans assigned successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Workout & Diet Plans | FlexFusion</title>
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
        
        .form-select, .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .alert-success {
            background: linear-gradient(45deg, var(--success-color), #27ae60);
            color: white;
        }
        
        h2 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
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
    <a href="assign_workouts.php" class="active"><i class="fas fa-dumbbell me-2"></i>Assign Workouts</a>
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
                <h2><i class="fas fa-dumbbell me-2"></i>Assign Workout & Diet Plans</h2>

    <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                    </div>
    <?php endif; ?>

    <form method="post">
                    <div class="mb-4">
            <label for="member_id" class="form-label">Select Member</label>
            <select name="member_id" id="member_id" class="form-select" required>
                <option value="">-- Select Member --</option>
                <?php foreach ($members as $member): ?>
                    <option value="<?= $member['user_id'] ?>">
                        <?= htmlspecialchars($member['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

                    <div class="mb-4">
            <label for="workout_plan" class="form-label">Workout Plan</label>
            <select name="workout_plan" id="workout_plan" class="form-select" required>
                <option value="">-- Select Workout Plan --</option>
                <?php foreach ($workouts as $w): ?>
                    <option value="<?= $w['plan_id'] ?>">
                        <?= htmlspecialchars($w['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

                    <div class="mb-4">
            <label for="diet_plan" class="form-label">Diet Plan</label>
            <select name="diet_plan" id="diet_plan" class="form-select" required>
                <option value="">-- Select Diet Plan --</option>
                <?php foreach ($diets as $d): ?>
                    <option value="<?= $d['plan_id'] ?>">
                        <?= htmlspecialchars($d['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Assign Plans
                    </button>
    </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
