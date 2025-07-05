<?php
  include '../../templates/header.php';
?>
<div class="card mb-4">
  <h3>Lab Instructor Dashboard</h3>
</div>

<div class="dashboard-grid">
  <div class="dashboard-card">
    <i class="fa fa-calendar-check"></i>
    <a href="/lab_rescheduling/lab/schedule.php">My Lab Schedule</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-envelope"></i>
    <a href="/lab_rescheduling/lab/review_requests.php">Forwarded Reschedule Requests</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-user-check"></i>
    <a href="/lab_rescheduling/lab/attendance.php">Update Attendance</a>
  </div>
  <div class="dashboard-card">
    <i class="fa fa-edit"></i>
    <a href="/lab_rescheduling/lab/reschedule_schedule.php">Schedule a Rescheduled Lab</a>
  </div>
</div>

<?php
  include '../../templates/footer.php';
?>
