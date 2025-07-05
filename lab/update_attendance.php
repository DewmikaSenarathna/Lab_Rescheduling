<?php
// lab/update_attendance.php
require_once '../includes/db.php';
session_start();

if (!in_array($_SESSION['user']['role'], ['instructor', 'coordinator'])) {
  header("Location: ../pages/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['schedule_id'], $_POST['status'])) {
    $student_id  = intval($_POST['student_id']);
    $schedule_id = intval($_POST['schedule_id']);
    $status      = $_POST['status'];

    $stmt = $conn->prepare("
        INSERT INTO attendance (student_id, schedule_id, status)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ");
    $stmt->bind_param("iis", $student_id, $schedule_id, $status);
    $stmt->execute();
    $stmt->close();
}

header("Location: attendance.php");
exit;
?>
