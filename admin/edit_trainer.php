<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_trainers.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'trainer'");
$stmt->execute([$id]);
$trainer = $stmt->fetch();

if (!$trainer) {
    die("Trainer not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, phone=?, gender=?, date_of_birth=? WHERE user_id=?");
    $stmt->execute([$full_name, $email, $phone, $gender, $dob, $id]);

    header("Location: manage_trainers.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Trainer | FlexFusion Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
    }
    .sidebar {
      height: 100vh;
      background: #343a40;
      padding-top: 20px;
      position: fixed;
      width: 220px;
    }
    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: #f8f9fa;
      text-decoration: none;
      transition: 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #007bff;
      color: white;
    }
    .main-content {
      margin-left: 220px;
      padding: 20px;
    }
    .logout-btn {
      color: #fff;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h4 class="text-center text-white">FlexFusion Admin</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_trainers.php" class="active">Manage Trainers</a>
    <a href="manage_members.php">Manage Members</a>
    <a href="manage_workouts.php">Manage Workouts</a>
    <a href="manage_diets.php">Manage Diet Plans</a>
    <a href="manage_subscriptions.php">Manage Subscriptions</a>
    <a href="view_reports.php">Reports & Feedback</a>
    <a href="../auth/logout.php" class="mt-4 d-block text-center logout-btn">Logout</a>
  </div>

  <div class="main-content">
    <div class="container mt-4">
      <h2>Edit Trainer</h2>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($trainer['full_name']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($trainer['email']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($trainer['phone']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select" required>
            <option value="male" <?= $trainer['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= $trainer['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Date of Birth</label>
          <input type="date" name="dob" class="form-control" value="<?= $trainer['date_of_birth'] ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Trainer</button>
        <a href="manage_trainers.php" class="btn btn-secondary ms-2">Back</a>
      </form>
    </div>
  </div>

</body>
</html>
