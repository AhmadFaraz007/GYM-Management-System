<?php
session_start();
require_once '../includes/db.php';

// Admin access only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Add diet plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_diet'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO diet_plans (title, description, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$title, $desc, $created_by]);
    header("Location: manage_diets.php");
    exit;
}

// Delete diet plan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM diet_plans WHERE plan_id = ?")->execute([$id]);
    header("Location: manage_diets.php");
    exit;
}

// Fetch diet plans
$diets = $pdo->query("SELECT dp.*, u.full_name AS creator FROM diet_plans dp LEFT JOIN users u ON dp.created_by = u.user_id ORDER BY dp.created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Diet Plans | FlexFusion</title>
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
        <li><a href="manage_diets.php" class="active"><i class="fas fa-utensils"></i> Manage Diet Plans</a></li>
        <li><a href="manage_subscriptions.php"><i class="fas fa-credit-card"></i> Manage Subscriptions</a></li>
        <li><a href="view_reports.php"><i class="fas fa-chart-bar"></i> Reports & Feedback</a></li>
        <li><a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manage Diet Plans</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDietModal">
                <i class="fas fa-plus me-2"></i>Add Diet Plan
            </button>
        </div>

        <div class="row">
            <?php foreach ($diets as $diet): ?>
            <div class="col-md-4 mb-4">
                <div class="card diet-card fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?= htmlspecialchars($diet['title']) ?></h5>
                        <div class="dropdown">
                            <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editDietModal<?= $diet['plan_id'] ?>">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="?delete=<?= $diet['plan_id'] ?>" 
                                       onclick="return confirm('Are you sure you want to delete this diet plan?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?= htmlspecialchars($diet['description']) ?></p>
                        <div class="diet-meta">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>Created by: <?= $diet['creator'] ?? 'Unknown' ?>
                            </small>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-clock me-1"></i>Created: <?= date('M d, Y', strtotime($diet['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($diets)): ?>
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                <p class="text-muted">No diet plans found.</p>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addDietModal">
                    <i class="fas fa-plus me-2"></i>Add Your First Diet Plan
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Diet Plan Modal -->
<div class="modal fade" id="addDietModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Diet Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Plan Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" required></textarea>
                </div>
          </div>
          <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_diet" class="btn btn-primary">Add Plan</button>
          </div>
        </form>
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
.diet-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.diet-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.diet-meta {
    border-top: 1px solid #eee;
    margin-top: 1rem;
    padding-top: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.dropdown-item {
    padding: 0.5rem 1rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item.text-danger:hover {
    background-color: #fff5f5;
}
</style>

</body>
</html>
