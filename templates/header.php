<?php 
  if (session_status() === PHP_SESSION_NONE) session_start(); 

  // Determine dashboard link
  $dashboardLink = "/lab_rescheduling/pages/index.php";
  if (!empty($_SESSION['user'])) {
    switch($_SESSION['user']['role']) {
      case 'student':     $dashboardLink = "/lab_rescheduling/pages/dashboard/student_dashboard.php";     break;
      case 'coordinator': $dashboardLink = "/lab_rescheduling/pages/dashboard/coordinator_dashboard.php"; break;
      case 'instructor':  $dashboardLink = "/lab_rescheduling/pages/dashboard/instructor_dashboard.php";  break;
      case 'admin':       $dashboardLink = "/lab_rescheduling/pages/dashboard/admin_dashboard.php";       break;
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lab Rescheduling System</title>
  <!-- Google Fonts & FontAwesome -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/lab_rescheduling/assets/css/styles.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="<?= $dashboardLink ?>">
        <i class="fa fa-flask"></i> Lab Scheduler
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
          <?php if (!empty($_SESSION['user'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="/lab_rescheduling/includes/logout.php">
                <i class="fa fa-sign-out-alt"></i> Logout
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="/lab_rescheduling/pages/login.php">
                <i class="fa fa-sign-in-alt"></i> Login
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container mt-4">
