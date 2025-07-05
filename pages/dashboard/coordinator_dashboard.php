<?php
  include '../../templates/header.php';
?>
<div class="card mb-4">
  <h3>Subject Coordinator Dashboard</h3>
</div>

<div class="dashboard-grid">
  <div class="dashboard-card">
    <i class="fa fa-calendar"></i>
    <a href="/lab_rescheduling/lab/schedule.php">Manage Lab Schedules</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-tasks"></i>
    <a href="/lab_rescheduling/lab/review_requests.php">Review Reschedule Requests</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-eye"></i>
    <a href="/lab_rescheduling/lab/attendance.php">View Student Attendance</a>
  </div>
</div>

<?php
  include '../../templates/footer.php';
?>
