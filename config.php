<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lab_rescheduling');

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Email settings
define('EMAIL_FROM', 'noreply@lab-system.com');
define('EMAIL_FROM_NAME', 'Lab Scheduler');
define('EMAIL_HOST', 'smtp.example.com');
define('EMAIL_USERNAME', 'your@email.com');
define('EMAIL_PASSWORD', 'yourpassword');

if (session_status() === PHP_SESSION_NONE) session_start();
?>
