<?php
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Optional role check
function require_role($role) {
    if ($_SESSION['role'] !== $role) {
        header("Location: ../auth/login.php");
        exit;
    }
}
?>
