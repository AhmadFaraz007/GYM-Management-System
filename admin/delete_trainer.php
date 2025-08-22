<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Ensure it's a trainer
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'trainer'");
    $stmt->execute([$id]);
    $trainer = $stmt->fetch();

    if ($trainer) {
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $deleteStmt->execute([$id]);
    }
}

header("Location: manage_trainers.php");
exit;
