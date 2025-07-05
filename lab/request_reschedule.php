<?php 
// lab/request_reschedule.php
include '../templates/header.php';

if ($_SESSION['user']['role'] !== 'student') {
    header("Location: ../pages/login.php");
    exit;
}
?>
<div class="card">
  <h3>Request Lab Reschedule</h3>
  <form action="submit_reschedule.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Course Code</label>
      <input type="text" name="course_code" class="form-control" placeholder="Enter Course Code" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Original Date</label>
      <input type="date" name="original_date" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Reason for Reschedule</label>
      <textarea name="reason" class="form-control" placeholder="Explain your reason" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Upload Supporting Document</label>
      <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.png" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit Request</button>
  </form>
</div>
<?php include '../templates/footer.php'; ?>
