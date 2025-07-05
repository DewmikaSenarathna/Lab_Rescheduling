<?php
// includes/save_details.php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save intermediate details 
    $_SESSION['details'] = [
        'semester' => $_POST['semester'],
        'items'    => $_POST['item']  // array of course codes representing subjects/labs
    ];
    
    // Redirect based on user role
    $role = $_SESSION['user']['role'];
    if ($role === 'student') {
        header("Location: /lab_rescheduling/pages/dashboard/student_dashboard.php");
    } elseif ($role === 'coordinator') {
        header("Location: /lab_rescheduling/pages/dashboard/coordinator_dashboard.php");
    } elseif ($role === 'instructor') {
        header("Location: /lab_rescheduling/pages/dashboard/instructor_dashboard.php");
    } else {
        header("Location: /lab_rescheduling/pages/index.php");
    }
    exit;
}
?>
