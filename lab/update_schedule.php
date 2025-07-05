<?php
require_once '../includes/db.php';
session_start();

if (!in_array($_SESSION['user']['role'], ['instructor', 'coordinator'])) {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id   = intval($_POST['subject_id']);
    $date         = $_POST['date'];
    $time_slot    = $_POST['time_slot'];
    $lab_id       = intval($_POST['lab_id']);
    $instructor_id= intval($_POST['instructor_id']);

    $stmt = $conn->prepare("INSERT INTO lab_schedule (subject_id, date, time_slot, lab_id, instructor_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $subject_id, $date, $time_slot, $lab_id, $instructor_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: schedule.php");
exit;
?>
