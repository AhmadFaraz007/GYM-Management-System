<?php
session_start();
require_once '../includes/db.php';

// Admin access only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch trainers
$trainers = $pdo->query("SELECT * FROM users WHERE role = 'trainer' ORDER BY created_at DESC")->fetchAll();

// Delete trainer
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role = 'trainer'")->execute([$id]);
    header("Location: manage_trainers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trainers | FlexFusion</title>
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
        <li><a href="manage_trainers.php" class="active"><i class="fas fa-user-tie"></i> Manage Trainers</a></li>
        <li><a href="manage_members.php"><i class="fas fa-users"></i> Manage Members</a></li>
        <li><a href="assign_trainers.php"><i class="fas fa-user-plus"></i> Assign Trainers</a></li>

        <li><a href="manage_workouts.php"><i class="fas fa-dumbbell"></i> Manage Workouts</a></li>
        <li><a href="manage_diets.php"><i class="fas fa-utensils"></i> Manage Diet Plans</a></li>
        <li><a href="manage_subscriptions.php"><i class="fas fa-credit-card"></i> Manage Subscriptions</a></li>
        <li><a href="view_reports.php"><i class="fas fa-chart-bar"></i> Reports & Feedback</a></li>
        <li><a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manage Trainers</h2>
            <a href="edit_trainer.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Trainer
            </a>
        </div>

        <div class="row">
            <?php foreach ($trainers as $trainer): ?>
            <div class="col-md-4 mb-4">
                <div class="card trainer-card fade-in">
                    <div class="card-body">
                        <div class="trainer-header d-flex align-items-center mb-3">
                            <div class="trainer-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="trainer-info ms-3">
                                <h5 class="mb-1"><?= htmlspecialchars($trainer['full_name']) ?></h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($trainer['email']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="trainer-details">
                            <p class="mb-2">
                                <i class="fas fa-phone me-2"></i><?= htmlspecialchars($trainer['phone'] ?? 'Not provided') ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-venus-mars me-2"></i><?= ucfirst($trainer['gender'] ?? 'Not specified') ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-calendar me-2"></i>Joined: <?= date('M d, Y', strtotime($trainer['created_at'])) ?>
                            </p>
                        </div>
                        <div class="trainer-actions mt-3 pt-3 border-top">
                            <a href="edit_trainer.php?id=<?= $trainer['user_id'] ?>" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="?delete=<?= $trainer['user_id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this trainer?')"
                               class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($trainers)): ?>
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                <p class="text-muted">No trainers found.</p>
                <a href="edit_trainer.php" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Add Your First Trainer
                </a>
            </div>
        </div>
        <?php endif; ?>
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
.trainer-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.trainer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.trainer-avatar {
    width: 50px;
    height: 50px;
    background-color: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #6c757d;
}

.trainer-details {
    color: #6c757d;
    font-size: 0.9rem;
}

.trainer-actions {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
}

.btn-outline-primary:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
</style>

</body>
</html>
