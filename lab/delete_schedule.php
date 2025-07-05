<?php
// lab/delete_schedule.php
require_once '../includes/db.php';
session_start();

if (!in_array($_SESSION['user']['role'], ['instructor', 'coordinator'])) {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id'])) {
    $schedule_id = intval($_POST['schedule_id']);
    $stmt = $conn->prepare("DELETE FROM lab_schedule WHERE schedule_id = ?");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: schedule.php");
exit;
?>
