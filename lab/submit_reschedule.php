<?php
// lab/submit_reschedule.php
session_start();
require_once '../includes/db.php';

$student_id    = $_SESSION['user']['user_id'];
$course_code   = trim($_POST['course_code']);
$reason        = trim($_POST['reason']);
$original_date = $_POST['original_date'];
$medical_form  = null;

// Process file upload
if ($_FILES['document']['error'] === UPLOAD_ERR_OK) {
    $filename    = uniqid('doc_') . "_" . basename($_FILES["document"]["name"]);
    $target_file = '../assets/uploads/' . $filename;
    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        $medical_form = 'assets/uploads/' . $filename;
    }
}

// Lookup subject_id using Course Code
$stmt2 = $conn->prepare("SELECT subject_id FROM subjects WHERE course_code = ?");
$stmt2->bind_param("s", $course_code);
$stmt2->execute();
$res = $stmt2->get_result();
if($row = $res->fetch_assoc()){
    $subject_id = $row['subject_id'];
} else {
    die("Error: Subject not found for Course Code: " . htmlspecialchars($course_code));
}
$stmt2->close();

// Insert new reschedule request with default status 'Pending'
$stmt = $conn->prepare("INSERT INTO reschedule_request (student_id, subject_id, original_date, reason, medical_form, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("issss", $student_id, $subject_id, $original_date, $reason, $medical_form);

if ($stmt->execute()) {
    echo "<script>
            alert('Reschedule request submitted successfully!');
            window.location.href='request_reschedule.php';
          </script>";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>
