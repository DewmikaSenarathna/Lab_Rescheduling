<?php
// lab/request_status.php
require_once '../includes/db.php';
include '../templates/header.php';

// Check if the user is a student
if ($_SESSION['user']['role'] !== 'student') {
    header("Location: ../pages/login.php");
    exit;
}

$student_id = $_SESSION['user']['user_id'];

// Prepare the query to get the student’s reschedule requests and join with the subjects table
$query = "SELECT r.*, s.course_code 
          FROM reschedule_request AS r 
          JOIN subjects AS s ON r.subject_id = s.subject_id 
          WHERE r.student_id = ? 
          ORDER BY r.created_at DESC";
$stmt  = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h3>My Reschedule Requests</h3>
<div class="table-responsive">
  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>Course Code</th>
        <th>Original Date</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Submitted On</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['course_code']) ?></td>
          <td><?= htmlspecialchars($row['original_date']) ?></td>
          <td><?= htmlspecialchars($row['reason']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php
$stmt->close();
include '../templates/footer.php';
?>
