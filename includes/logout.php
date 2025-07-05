<?php
// includes/logout.php
session_start();
session_unset();
session_destroy();
header("Location: /lab_rescheduling/pages/login.php");
exit;
?>
