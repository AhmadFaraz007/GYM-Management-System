<?php
session_start();
require_once '../includes/db.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch all members
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'member'");
    $stmt->execute();
    $members = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching members: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members | FlexFusion</title>
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
        <li><a href="manage_members.php" class="active"><i class="fas fa-users"></i> Manage Members</a></li>
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
            <h2 class="mb-0">Manage Members</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="fas fa-user-plus me-2"></i>Add New Member
            </button>
        </div>

        <div class="card fade-in">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>DOB</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($members): ?>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td><?= $member['user_id'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2">
                                                    <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                                                </div>
                                                <?= htmlspecialchars($member['full_name']) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($member['email']) ?></td>
                                        <td><?= htmlspecialchars($member['phone']) ?></td>
                                        <td><?= htmlspecialchars($member['gender']) ?></td>
                                        <td><?= htmlspecialchars($member['date_of_birth']) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit_member.php?id=<?= $member['user_id'] ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit Member">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_member.php?id=<?= $member['user_id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this member?')"
                                                   data-bs-toggle="tooltip" 
                                                   title="Delete Member">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No members found.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMemberForm" method="post" action="add_member.php">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addMemberForm" class="btn btn-primary">Add Member</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

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
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 2rem;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.btn-group .btn i {
    font-size: 0.875rem;
}
</style>

</body>
</html>
