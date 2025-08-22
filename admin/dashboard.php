<?php
session_start();
require_once '../includes/db.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['full_name'] ?? 'Admin';

// Fetch statistics
$stats = [
    'trainers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'trainer'")->fetchColumn(),
    'members' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'member'")->fetchColumn(),
    'active_subs' => $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE end_date >= CURDATE()")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(amount_paid) FROM subscriptions WHERE payment_status = 'paid'")->fetchColumn()
];

// Fetch recent activities
$recent_activities = $pdo->query("
    SELECT 'subscription' as type, s.created_at, u.full_name, s.package_name as details
    FROM subscriptions s
    JOIN users u ON s.member_id = u.user_id
    UNION ALL
    SELECT 'progress' as type, pr.recorded_at, u.full_name, CONCAT('Weight: ', pr.weight, 'kg') as details
    FROM progress_records pr
    JOIN users u ON pr.member_id = u.user_id
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | FlexFusion</title>
    <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
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

        .sidebar-header {
            padding: 0 20px;
            margin-bottom: 20px;
    }

        .sidebar-header h4 {
            color: white;
            font-size: 1.2rem;
            margin: 0;
            padding: 10px 0;
            text-align: center;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
            padding: 0;
        }

        .sidebar-menu a {
      display: block;
      padding: 12px 20px;
            color: #ffffffcc;
      text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
            background-color: var(--primary-color);
      color: white;
            border-left-color: white;
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        .sidebar-menu a.logout {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
    .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .page-header {
            background: white;
      padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-header h2 {
            margin: 0;
            color: var(--dark-color);
            font-size: 1.8rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stats-info {
            flex: 1;
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark-color);
    }

        .stats-label {
            color: #666;
            font-size: 0.9rem;
        }

        .activity-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h4 {
            margin: 0;
            color: var(--dark-color);
            font-size: 1.4rem;
        }

        .activity-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .activity-card:hover {
            transform: translateX(5px);
            background: #f1f3f5;
        }

        .activity-card:last-child {
            margin-bottom: 0;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 15px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .action-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
      text-decoration: none;
            color: var(--dark-color);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            color: var(--dark-color);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 15px;
            background: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 15px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 1rem;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-dumbbell me-2"></i>FlexFusion</h4>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="manage_trainers.php"><i class="fas fa-user-tie"></i> Manage Trainers</a></li>
        <li><a href="manage_members.php"><i class="fas fa-users"></i> Manage Members</a></li>
        <li><a href="assign_trainers.php"><i class="fas fa-user-plus"></i> Assign Trainers</a></li>
        <li><a href="manage_workouts.php"><i class="fas fa-dumbbell"></i> Manage Workouts</a></li>
        <li><a href="manage_diets.php"><i class="fas fa-utensils"></i> Manage Diet Plans</a></li>
        <li><a href="manage_subscriptions.php"><i class="fas fa-credit-card"></i> Manage Subscriptions</a></li>
        <li><a href="view_reports.php"><i class="fas fa-chart-bar"></i> Reports & Feedback</a></li>
        <li><a href="../auth/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <h2>Welcome, <?= htmlspecialchars($admin_name) ?> 👋</h2>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(52, 152, 219, 0.1); color: var(--primary-color);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-value"><?= $stats['trainers'] ?></div>
                    <div class="stats-label">Total Trainers</div>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(46, 204, 113, 0.1); color: var(--success-color);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-value"><?= $stats['members'] ?></div>
                    <div class="stats-label">Total Members</div>
          </div>
        </div>
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(241, 196, 15, 0.1); color: var(--warning-color);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-value"><?= $stats['active_subs'] ?></div>
                    <div class="stats-label">Active Subscriptions</div>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(231, 76, 60, 0.1); color: var(--danger-color);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-value">$<?= number_format($stats['revenue'], 2) ?></div>
                    <div class="stats-label">Total Revenue</div>
            </div>
          </div>
        </div>

        <div class="row">
            <!-- Recent Activities -->
            <div class="col-md-8">
                <div class="activity-section">
                    <div class="section-header">
                        <h4>Recent Activities</h4>
                    </div>
                    <?php if (empty($recent_activities)): ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <p>No recent activities found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-card">
                                <div class="d-flex align-items-center">
                                    <div class="activity-icon" style="background: rgba(52, 152, 219, 0.1); color: var(--primary-color);">
                                        <i class="fas <?= $activity['type'] === 'subscription' ? 'fa-credit-card' : 'fa-chart-line' ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($activity['full_name']) ?></h6>
                                        <p class="mb-0 text-muted">
                                            <?= $activity['type'] === 'subscription' ? 'New subscription' : 'Progress update' ?>: 
                                            <?= htmlspecialchars($activity['details']) ?>
                                        </p>
                                        <small class="text-muted">
                                            <?= date('M d, Y H:i', strtotime($activity['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-4">
                <div class="activity-section">
                    <div class="section-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="quick-actions">
                        <a href="manage_members.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h6 class="mb-0">Add Member</h6>
                        </a>
                        <a href="assign_trainers.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h6 class="mb-0">Assign Trainer</h6>
                        </a>
                        <a href="manage_workouts.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                            <h6 class="mb-0">Add Workout</h6>
                        </a>
                        <a href="manage_subscriptions.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h6 class="mb-0">New Subscription</h6>
                        </a>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
