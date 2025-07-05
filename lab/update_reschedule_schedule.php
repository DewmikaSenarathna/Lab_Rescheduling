<?php
// lab/update_reschedule_schedule.php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['new_date'], $_POST['new_time'], $_POST['lab_id'])) {
    // Retrieve variables from POST.
    $request_id = intval($_POST['request_id']);
    $new_date   = $_POST['new_date'];
    $new_time   = $_POST['new_time'];
    $lab_id     = intval($_POST['lab_id']);
    
    $stmt = $conn->prepare("SELECT subject_id FROM reschedule_request WHERE request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$request = $result->fetch_assoc()) {
        die("Reschedule request not found.");
    }
    
    $stmt->close();
    
    $subject_id   = $request['subject_id'];
    $instructor_id = $_SESSION['user']['user_id'];
    
    $stmt2 = $conn->prepare("INSERT INTO lab_schedule (subject_id, date, time_slot, lab_id, instructor_id, semester_id, department_id) VALUES (?, ?, ?, ?, ?, (SELECT semester_id FROM subjects WHERE subject_id = ?), (SELECT department_id FROM subjects WHERE subject_id = ?))");
    
    if (!$stmt2) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt2->bind_param("issiiii", $subject_id, $new_date, $new_time, $lab_id, $instructor_id, $subject_id, $subject_id);
    
    if (!$stmt2->execute()) {
        die("Error inserting reschedule lab: " . $stmt2->error);
    }
    
    $stmt2->close();
    
    // Update the reschedule_request status to 'Scheduled'
    $stmt3 = $conn->prepare("UPDATE reschedule_request SET status = 'Scheduled' WHERE request_id = ?");
    if (!$stmt3) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt3->bind_param("i", $request_id);
    $stmt3->execute();
    $stmt3->close();
}

header("Location: /lab_rescheduling/lab/review_requests.php");
exit;
?>
