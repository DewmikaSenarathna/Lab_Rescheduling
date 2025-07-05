<?php
// lab/reschedule_schedule.php
require_once '../includes/db.php';
include '../templates/header.php';

if ($_SESSION['user']['role'] !== 'instructor') {
    header("Location: ../pages/login.php");
    exit;
}

if (!isset($_GET['request_id'])) {
    echo "<div class='alert alert-danger'>No reschedule request selected.</div>";
    include '../templates/footer.php';
    exit;
}
$request_id = intval($_GET['request_id']);

// Fetch the reschedule request details
$stmt = $conn->prepare("SELECT r.*, s.course_code, s.name AS subject_name FROM reschedule_request r JOIN subjects s ON r.subject_id = s.subject_id WHERE r.request_id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
if(!$request = $result->fetch_assoc()){
    echo "<div class='alert alert-danger'>Request not found.</div>";
    include '../templates/footer.php';
    exit;
}
$stmt->close();
?>
<div class="card">
  <h3>Schedule Reschedule Lab (Course Code: <?= htmlspecialchars($request['course_code']) ?>)</h3>
  <p><strong>Subject:</strong> <?= htmlspecialchars($request['subject_name']) ?></p>
  <p><strong>Original Date:</strong> <?= htmlspecialchars($request['original_date']) ?></p>
  <p><strong>Reason:</strong> <?= htmlspecialchars($request['reason']) ?></p>
  
  <form method="POST" action="update_reschedule_schedule.php">
    <input type="hidden" name="request_id" value="<?= $request_id ?>">
    <div class="mb-3">
      <label class="form-label">New Reschedule Date</label>
      <input type="date" name="new_date" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">New Time Slot</label>
      <input type="text" name="new_time" class="form-control" placeholder="e.g., 10:00-12:00" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Select Lab (for Reschedule)</label>
      <select name="lab_id" class="form-select" required>
        <option value="">Select Lab</option>
        <?php 
        $labsQuery = "SELECT lab_id, lab_name FROM lab ORDER BY lab_name";
        $labsResult = $conn->query($labsQuery);
        while ($lab = $labsResult->fetch_assoc()):
        ?>
          <option value="<?= $lab['lab_id'] ?>"><?= htmlspecialchars($lab['lab_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit Reschedule</button>
  </form>
</div>
<?php include '../templates/footer.php'; ?>
