<?php 
// pages/index.php
include '../templates/header.php'; 
?>
<div class="intro">
  <h2>Welcome to the Lab Rescheduling System</h2>
  <p>
    Experience seamless lab management with a vibrant, user centric platform that lets you view schedules, request reschedules, and track attendance with ease.
  </p>

  <!-- Bootstrap flex + gap utility to space buttons -->
  <div class="d-flex justify-content-center gap-3 flex-wrap">
    <a href="login.php" class="btn btn-primary">
      <i class="fa fa-sign-in-alt"></i> Login
    </a>
    <a href="register.php" class="btn btn-success">
      <i class="fa fa-user-plus"></i> Create Account
    </a>
  </div>
</div>
<?php 
include '../templates/footer.php'; 
?>
