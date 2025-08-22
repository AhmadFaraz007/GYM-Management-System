<?php
session_start();
require_once '../includes/db.php';

// Admin access only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Add subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_sub'])) {
    $member_id = $_POST['member_id'];
    $package = $_POST['package_name'];
    $amount = $_POST['amount_paid'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $status = $_POST['payment_status'];

    $stmt = $pdo->prepare("INSERT INTO subscriptions (member_id, package_name, amount_paid, start_date, end_date, payment_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$member_id, $package, $amount, $start, $end, $status]);
    header("Location: manage_subscriptions.php");
    exit;
}

// Delete subscription
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM subscriptions WHERE id = ?")->execute([$id]);
    header("Location: manage_subscriptions.php");
    exit;
}

// Fetch members
$members = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'member'")->fetchAll();

// Fetch subscriptions
$subs = $pdo->query("SELECT s.*, u.full_name FROM subscriptions s JOIN users u ON s.member_id = u.user_id ORDER BY s.created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscriptions | FlexFusion</title>
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
        <li><a href="manage_subscriptions.php" class="active"><i class="fas fa-credit-card"></i> Manage Subscriptions</a></li>
        <li><a href="view_reports.php"><i class="fas fa-chart-bar"></i> Reports & Feedback</a></li>
        <li><a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manage Subscriptions</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subModal">
                <i class="fas fa-plus me-2"></i>Add Subscription
            </button>
        </div>

        <div class="row">
            <?php foreach ($subs as $sub): ?>
            <div class="col-md-4 mb-4">
                <div class="card subscription-card fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?= htmlspecialchars($sub['package_name']) ?></h5>
                        <span class="badge <?= $sub['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning' ?>">
                            <?= ucfirst($sub['payment_status']) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="subscription-info">
                            <p class="mb-2">
                                <i class="fas fa-user me-2"></i>Member: <?= htmlspecialchars($sub['full_name']) ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-dollar-sign me-2"></i>Amount: $<?= number_format($sub['amount_paid'], 2) ?>
                            </p>
                            <div class="subscription-dates">
                                <p class="mb-2">
                                    <i class="fas fa-calendar-plus me-2"></i>Start: <?= date('M d, Y', strtotime($sub['start_date'])) ?>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-calendar-minus me-2"></i>End: <?= date('M d, Y', strtotime($sub['end_date'])) ?>
                                </p>
                            </div>
                        </div>
                        <div class="subscription-actions mt-3 pt-3 border-top">
                            <a href="?delete=<?= $sub['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this subscription?')"
                               class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($subs)): ?>
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                <p class="text-muted">No subscriptions found.</p>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#subModal">
                    <i class="fas fa-plus me-2"></i>Add Your First Subscription
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Subscription Modal -->
<div class="modal fade" id="subModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Member</label>
                    <select name="member_id" class="form-select" required>
                        <option value="">Choose a member...</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['user_id'] ?>"><?= $m['full_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Package Name</label>
                    <input type="text" name="package_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount Paid</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="amount_paid" step="0.01" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select" required>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_sub" class="btn btn-primary">Add Subscription</button>
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
.subscription-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.subscription-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.subscription-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.subscription-dates {
    background-color: #f8f9fa;
    padding: 0.75rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5em 0.75em;
}

.btn-outline-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
</style>

</body>
</html>
