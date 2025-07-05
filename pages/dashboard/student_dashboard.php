<?php
  include '../../templates/header.php';
?>
<div class="card mb-4">
  <h3>Student Dashboard</h3>
</div>

<div class="dashboard-grid">
  <div class="dashboard-card">
    <i class="fa fa-calendar-alt"></i>
    <a href="/lab_rescheduling/lab/schedule.php">View Lab Schedule</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-edit"></i>
    <a href="/lab_rescheduling/lab/request_reschedule.php">Request Lab Reschedule</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-history"></i>
    <a href="/lab_rescheduling/lab/request_status.php">My Reschedule Requests</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-user-check"></i>
    <a href="/lab_rescheduling/lab/attendance.php">View My Attendance</a>
  </div>
</div>

<?php
  include '../../templates/footer.php';
?>
