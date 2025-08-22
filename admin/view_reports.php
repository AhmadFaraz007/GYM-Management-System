<?php
session_start();
require_once '../includes/db.php';

// Admin access only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch reports
$progress = $pdo->query("SELECT pr.*, u.full_name AS member FROM progress_records pr LEFT JOIN users u ON pr.member_id = u.user_id ORDER BY pr.recorded_at DESC")->fetchAll();
$attendance = $pdo->query("SELECT a.*, u.full_name AS member FROM attendance a LEFT JOIN users u ON a.member_id = u.user_id ORDER BY a.checked_at DESC")->fetchAll();
$feedback = $pdo->query("SELECT f.*, u.full_name AS member FROM feedback f LEFT JOIN users u ON f.member_id = u.user_id ORDER BY f.submitted_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Feedback | FlexFusion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-dumbbell me-2"></i>FlexFusion</h4>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="manage_trainers.php"><i class="fas fa-user-tie"></i> Manage Trainers</a></li>
        <li><a href="manage_members.php"><i class="fas fa-users"></i> Manage Members</a></li>
        <li><a href="assign_trainers.php"><i class="fas fa-user-plus"></i> Assign Trainers</a></li>

        <li><a href="manage_workouts.php"><i class="fas fa-dumbbell"></i> Manage Workouts</a></li>
        <li><a href="manage_diets.php"><i class="fas fa-utensils"></i> Manage Diet Plans</a></li>
        <li><a href="manage_subscriptions.php"><i class="fas fa-credit-card"></i> Manage Subscriptions</a></li>
        <li><a href="view_reports.php" class="active"><i class="fas fa-chart-bar"></i> Reports & Feedback</a></li>
        <li><a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Reports & Feedback</h2>

        <!-- Progress Records Section -->
        <div class="card mb-4 fade-in">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Progress Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Weight (kg)</th>
                                <th>Calories</th>
                                <th>Notes</th>
                                <th>Recorded At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($progress as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['member']) ?></td>
                                <td><?= $row['weight'] ?></td>
                                <td><?= $row['calories_burned'] ?></td>
                                <td><?= htmlspecialchars($row['notes']) ?></td>
                                <td><?= date('M d, Y H:i', strtotime($row['recorded_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($progress)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No progress records found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attendance Logs Section -->
        <div class="card mb-4 fade-in">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance Logs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Checked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['member']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['checkin_date'])) ?></td>
                                <td>
                                    <span class="badge <?= $row['status'] === 'present' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($row['checked_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($attendance)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-calendar-check fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No attendance records found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Feedback Section -->
        <div class="card mb-4 fade-in">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Member Feedback</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($feedback as $row): ?>
                    <div class="col-md-6 mb-3">
                        <div class="feedback-card">
                            <div class="feedback-header">
                                <h6 class="mb-1"><?= htmlspecialchars($row['member']) ?></h6>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i><?= date('M d, Y H:i', strtotime($row['submitted_at'])) ?>
                                </small>
                            </div>
                            <p class="feedback-message mb-0"><?= htmlspecialchars($row['message']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($feedback)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No feedback found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const toggleSidebar = () => {
        document.querySelector('.sidebar').classList.toggle('active');
        document.querySelector('.main-content').classList.toggle('active');
    };

    // Add mobile menu button
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.className = 'btn btn-primary d-md-none position-fixed';
    mobileMenuBtn.style.cssText = 'top: 10px; right: 10px; z-index: 1001;';
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    mobileMenuBtn.onclick = toggleSidebar;
    document.body.appendChild(mobileMenuBtn);
});
</script>

<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5em 0.75em;
}

.feedback-card {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    height: 100%;
    transition: transform 0.3s ease;
}

.feedback-card:hover {
    transform: translateY(-3px);
}

.feedback-header {
    margin-bottom: 0.5rem;
}

.feedback-message {
    color: #6c757d;
    font-size: 0.9rem;
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

</body>
</html>
