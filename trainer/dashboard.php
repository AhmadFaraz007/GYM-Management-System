<?php
session_start();
require_once '../includes/db.php';

// Ensure only trainer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

$full_name = $_SESSION['full_name'] ?? 'Trainer';
$trainer_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trainer Dashboard | FlexFusion</title>
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
      overflow: hidden;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .card-body {
      padding: 1.5rem;
    }
    
    .card-icon {
      font-size: 2.5rem;
      opacity: 0.8;
      transition: all 0.3s;
    }
    
    .card:hover .card-icon {
      transform: scale(1.1);
    }
    
    .bg-primary {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)) !important;
    }
    
    .bg-success {
      background: linear-gradient(45deg, var(--success-color), #27ae60) !important;
    }
    
    .bg-warning {
      background: linear-gradient(45deg, var(--warning-color), #f39c12) !important;
    }
    
    .alert {
      border-radius: 15px;
      border: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .alert-info {
      background: linear-gradient(45deg, #3498db, #2980b9);
      color: white;
    }
    
    h2 {
      color: var(--dark-color);
      font-weight: 600;
    }
    
    .text-muted {
      color: #7f8c8d !important;
    }
    
    .fs-3 {
      font-weight: 600;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white mb-4">
      <i class="fas fa-dumbbell me-2"></i>Trainer Panel
    </h4>
    <a href="dashboard.php" class="active"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="my_members.php"><i class="fas fa-users me-2"></i>My Members</a>
    <a href="assign_workouts.php"><i class="fas fa-dumbbell me-2"></i>Assign Workouts</a>
    <a href="track_progress.php"><i class="fas fa-chart-line me-2"></i>Track Progress</a>
    <a href="manage_attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="container-fluid">
      <h2>Welcome, <?php echo htmlspecialchars($full_name); ?> 💪</h2>
      <p class="text-muted">Here's an overview of your training tasks:</p>

      <div class="row mt-4 g-4">
        <div class="col-md-4">
          <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h5>Assigned Members</h5>
                  <p class="fs-3 mb-0">
                    <?php
                      try {
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM members_trainers WHERE trainer_id = ?");
                        $stmt->execute([$trainer_id]);
                        echo $stmt->fetchColumn();
                      } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                      }
                    ?>
                  </p>
                </div>
                <div class="card-icon">
                  <i class="fas fa-users"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0 bg-success text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h5>Pending Workouts</h5>
                  <p class="fs-3 mb-0">5</p>
                </div>
                <div class="card-icon">
                  <i class="fas fa-dumbbell"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0 bg-warning text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h5>Messages</h5>
                  <p class="fs-3 mb-0">2</p>
                </div>
                <div class="card-icon">
                  <i class="fas fa-comments"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="alert alert-info mt-5">
        <i class="fas fa-lightbulb me-2"></i>
        Tip: Regularly check in with your members to track their progress and motivate them!
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
