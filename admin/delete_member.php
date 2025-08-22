<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
    $stmt->execute([$id]);
}

header("Location: manage_members.php?deleted=1");
exit;
