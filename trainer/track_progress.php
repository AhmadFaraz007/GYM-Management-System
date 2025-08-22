<?php
session_start();
require_once '../includes/db.php';
if ($_SESSION['role'] !== 'trainer') header("Location: ../auth/login.php");
$trainer_id = $_SESSION['user_id'];

$members = $pdo->prepare("
  SELECT u.user_id, u.full_name
  FROM members_trainers mt JOIN users u ON mt.member_id = u.user_id
  WHERE mt.trainer_id = ?
");
$members->execute([$trainer_id]);
$members = $members->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $mid = $_POST['member_id'];
  $weight = $_POST['weight'];
  $cal = $_POST['calories'];
  $notes = $_POST['notes'];
  $pdo->prepare("
    INSERT INTO progress_records (member_id, recorded_by, weight, calories_burned, notes)
    VALUES (?, ?, ?, ?, ?)
  ")->execute([$mid, $trainer_id, $weight, $cal, $notes]);
  header("Location: track_progress.php?ok=1");
  exit;
}

// fetch existing
$records = $pdo->query("
  SELECT pr.*, u.full_name AS member, tr.full_name AS trainer
  FROM progress_records pr
  JOIN users u ON pr.member_id = u.user_id
  JOIN users tr ON pr.recorded_by = tr.user_id
  ORDER BY pr.recorded_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Progress | FlexFusion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--trainer-primary);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
        }
        .btn-success {
            background-color: var(--trainer-primary);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-success:hover {
            background-color: var(--trainer-secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table thead th {
            background: var(--trainer-dark);
            color: white;
            font-weight: 500;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .progress-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .section-title {
            color: var(--trainer-dark);
            font-weight: 600;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 10px;
            color: var(--trainer-primary);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stats-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--trainer-primary);
        }
        .stats-label {
            color: #666;
            font-size: 0.9rem;
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
    <a href="track_progress.php" class="active"><i class="fas fa-chart-line me-2"></i>Track Progress</a>
    <a href="manage_attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
    <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat</a>
    <a href="../auth/logout.php" class="mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="section-title">
            <i class="fas fa-chart-line"></i>Track Member Progress
        </h2>

  <?php if (isset($_GET['ok'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>Progress recorded successfully!
            </div>
  <?php endif; ?>

        <div class="progress-card">
            <form method="post">
    <div class="row g-3">
      <div class="col-md-3">
        <select name="member_id" class="form-select" required>
                            <option value="">Select Member</option>
          <?php foreach ($members as $m): ?>
            <option value="<?= $m['user_id'] ?>"><?= htmlspecialchars($m['full_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
                    <div class="col-md-2">
                        <input type="number" step="0.1" name="weight" class="form-control" placeholder="Weight (kg)" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="calories" class="form-control" placeholder="Calories" required>
      </div>
                    <div class="col-md-3">
                        <input type="text" name="notes" class="form-control" placeholder="Notes">
      </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-plus me-2"></i>Add
                        </button>
      </div>
    </div>
  </form>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-value"><?= count($records) ?></div>
                    <div class="stats-label">Total Records</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-value"><?= count($members) ?></div>
                    <div class="stats-label">Active Members</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-value">
                        <?= array_sum(array_column($records, 'calories_burned')) ?>
                    </div>
                    <div class="stats-label">Total Calories Tracked</div>
                </div>
            </div>
        </div>

        <h4 class="section-title">
            <i class="fas fa-history"></i>Recent Progress Records
        </h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Weight</th>
                        <th>Calories</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                        <th>Date</th>
                    </tr>
                </thead>
    <tbody>
      <?php foreach ($records as $r): ?>
        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="member-avatar me-2">
                                        <?= strtoupper(substr($r['member'], 0, 1)) ?>
                                    </div>
                                    <?= htmlspecialchars($r['member']) ?>
                                </div>
                            </td>
                            <td><?= $r['weight'] ?> kg</td>
          <td><?= $r['calories_burned'] ?></td>
          <td><?= htmlspecialchars($r['notes']) ?></td>
          <td><?= htmlspecialchars($r['trainer']) ?></td>
                            <td><?= date('M d, Y H:i', strtotime($r['recorded_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
