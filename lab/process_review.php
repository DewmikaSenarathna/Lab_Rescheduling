<?php
// lab/process_review.php
require_once '../includes/db.php';
session_start();

if (!in_array($_SESSION['user']['role'], ['admin', 'coordinator'])) {
    header("Location: ../pages/login.php");
    exit;
}

$request_id = $_POST['request_id'];
$action     = $_POST['action'];

if ($action === 'approve') {
    if ($_SESSION['user']['role'] === 'admin') {
        $status = 'Approved by Admin';
    } else {
        // For coordinator, "forward" action will update status.
        $status = 'Forwarded by Coordinator';
    }
} elseif ($action === 'forward') {
    // For coordinator forwarding.
    $status = 'Forwarded by Coordinator';
} else {
    $status = ($_SESSION['user']['role'] === 'admin') ? 'Rejected by Admin' : 'Rejected by Coordinator';
}

$stmt = $conn->prepare("UPDATE reschedule_request SET status = ? WHERE request_id = ?");
$stmt->bind_param("si", $status, $request_id);
$stmt->execute();

header("Location: review_requests.php");
exit;
?>
