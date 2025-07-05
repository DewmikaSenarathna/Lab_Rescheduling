<?php
  include '../../templates/header.php';
?>
<div class="card mb-4">
  <h3>Admin Dashboard</h3>
</div>

<div class="dashboard-grid">
  <div class="dashboard-card">
    <i class="fa fa-calendar-alt"></i>
    <a href="/lab_rescheduling/lab/schedule.php">All Lab Schedules</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-tasks"></i>
    <a href="/lab_rescheduling/lab/review_requests.php">Review All Requests</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-chart-line"></i>
    <a href="/lab_rescheduling/lab/attendance.php">All Attendance Records</a>
  </div>
</div>

<?php
  include '../../templates/footer.php';
?>
