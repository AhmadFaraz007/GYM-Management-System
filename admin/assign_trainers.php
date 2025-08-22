<?php
session_start();
require_once '../includes/db.php';

// Admin access only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle trainer assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $trainer_id = $_POST['trainer_id'];
    
    // Check if assignment already exists
    $check = $pdo->prepare("SELECT id FROM members_trainers WHERE member_id = ?");
    $check->execute([$member_id]);
    
    if ($check->rowCount() > 0) {
        // Update existing assignment
        $stmt = $pdo->prepare("UPDATE members_trainers SET trainer_id = ? WHERE member_id = ?");
        $stmt->execute([$trainer_id, $member_id]);
    } else {
        // Create new assignment
        $stmt = $pdo->prepare("INSERT INTO members_trainers (member_id, trainer_id) VALUES (?, ?)");
        $stmt->execute([$member_id, $trainer_id]);
    }
    
    header("Location: assign_trainers.php?success=1");
    exit;
}

// Fetch all members
$members = $pdo->query("
    SELECT u.*, 
           t.full_name as trainer_name,
           t.user_id as trainer_id
    FROM users u
    LEFT JOIN members_trainers mt ON u.user_id = mt.member_id
    LEFT JOIN users t ON mt.trainer_id = t.user_id
    WHERE u.role = 'member'
    ORDER BY u.full_name
")->fetchAll();

// Fetch all trainers
$trainers = $pdo->query("
    SELECT user_id, full_name 
    FROM users 
    WHERE role = 'trainer' 
    ORDER BY full_name
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Trainers | FlexFusion</title>
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
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .member-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .member-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .trainer-badge {
            background: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .no-trainer {
            background: var(--warning-color);
        }
        
        .btn-assign {
            background: var(--success-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-assign:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .form-select {
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center text-white mb-4">
        <i class="fas fa-dumbbell me-2"></i>Admin Panel
    </h4>
    <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="manage_trainers.php"><i class="fas fa-user-tie me-2"></i>Manage Trainers</a>
    <a href="manage_members.php"><i class="fas fa-users me-2"></i>Manage Members</a>
    <a href="assign_trainers.php" class="active"><i class="fas fa-user-plus me-2"></i>Assign Trainers</a>
    <a href="manage_workouts.php"><i class="fas fa-dumbbell me-2"></i>Manage Workouts</a>
    <a href="manage_diets.php"><i class="fas fa-utensils me-2"></i>Manage Diet Plans</a>
    <a href="manage_subscriptions.php"><i class="fas fa-credit-card me-2"></i>Manage Subscriptions</a>
    <a href="view_reports.php"><i class="fas fa-chart-bar me-2"></i>Reports & Feedback</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-plus me-2"></i>Assign Trainers to Members</h2>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Trainer assigned successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row g-4">
            <?php foreach ($members as $member): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="member-avatar me-3">
                                    <?php if ($member['profile_image']): ?>
                                        <img src="<?= htmlspecialchars($member['profile_image']) ?>" alt="Member">
                                    <?php else: ?>
                                        <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($member['full_name']) ?></h5>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($member['email']) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <?php if ($member['trainer_name']): ?>
                                    <span class="trainer-badge">
                                        <i class="fas fa-user-tie"></i>
                                        <?= htmlspecialchars($member['trainer_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="trainer-badge no-trainer">
                                        <i class="fas fa-exclamation-circle"></i>
                                        No Trainer Assigned
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <button class="btn-assign w-100" 
                                    onclick="openAssignModal(<?= $member['user_id'] ?>, '<?= htmlspecialchars($member['full_name']) ?>', <?= $member['trainer_id'] ?: 'null' ?>)">
                                <i class="fas fa-user-plus me-2"></i>
                                <?= $member['trainer_name'] ? 'Change Trainer' : 'Assign Trainer' ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Assign Trainer Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Trainer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm" method="post">
                    <input type="hidden" name="member_id" id="memberId">
                    <div class="mb-3">
                        <label class="form-label">Member</label>
                        <input type="text" class="form-control" id="memberName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Trainer</label>
                        <select name="trainer_id" class="form-select" required>
                            <option value="">Choose a trainer...</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer['user_id'] ?>">
                                    <?= htmlspecialchars($trainer['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>Assign Trainer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function openAssignModal(memberId, memberName, currentTrainerId) {
    document.getElementById('memberId').value = memberId;
    document.getElementById('memberName').value = memberName;
    
    const trainerSelect = document.querySelector('select[name="trainer_id"]');
    if (currentTrainerId) {
        trainerSelect.value = currentTrainerId;
    } else {
        trainerSelect.value = '';
    }
    
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}
</script>
</body>
</html> 