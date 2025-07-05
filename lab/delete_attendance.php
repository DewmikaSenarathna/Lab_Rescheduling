<?php
// lab/delete_attendance.php
require_once '../includes/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'instructor') {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_id'])) {
    $attendance_id = intval($_POST['attendance_id']);
    $stmt = $conn->prepare("DELETE FROM attendance WHERE attendance_id = ?");
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: attendance.php");
exit;
?>
