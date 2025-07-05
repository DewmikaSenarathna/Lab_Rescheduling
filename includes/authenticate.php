<?php
// includes/authenticate.php
session_start();
require_once __DIR__ . '/../config.php';
require_once 'db.php';

// Process Registration
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $role, $password);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        // For non-admin roles, store department details
        if (in_array($role, ['student', 'coordinator', 'instructor'])) {
            $departmentName = trim($_POST['department']);
            // Check if department exists; if not, you may choose to insert it
            $deptStmt = $conn->prepare("SELECT department_id FROM department WHERE name = ?");
            $deptStmt->bind_param("s", $departmentName);
            $deptStmt->execute();
            $deptResult = $deptStmt->get_result();
            if ($deptRow = $deptResult->fetch_assoc()){
                $dept_id = $deptRow['department_id'];
            } else {
                $insertDept = $conn->prepare("INSERT INTO department (name) VALUES (?)");
                $insertDept->bind_param("s", $departmentName);
                $insertDept->execute();
                $dept_id = $conn->insert_id;
                $insertDept->close();
            }
            $deptStmt->close();

            // Insert into role-specific table using the existing schema
            if ($role === 'student') {
                $roleStmt = $conn->prepare("INSERT INTO student (student_id, name, email, department_id) VALUES (?, ?, ?, ?)");
            } elseif ($role === 'coordinator') {
                $roleStmt = $conn->prepare("INSERT INTO subject_coordinator (coordinator_id, name, email, department_id) VALUES (?, ?, ?, ?)");
            } elseif ($role === 'instructor') {
                $roleStmt = $conn->prepare("INSERT INTO lab_instructor (instructor_id, name, email, department_id) VALUES (?, ?, ?, ?)");
            }
            if (isset($roleStmt)) {
                $roleStmt->bind_param("issi", $user_id, $name, $email, $dept_id);
                $roleStmt->execute();
                $roleStmt->close();
            }
        }
        header("Location: /lab_rescheduling/pages/login.php");
    } else {
        header("Location: /lab_rescheduling/pages/register.php?error=Registration failed. Try a different email.");
    }
    exit;
}

// Process Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['register'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            // Admin users go directly to their dashboard; others go to an intermediate details page.
            if ($user['role'] === 'admin') {
                header("Location: /lab_rescheduling/pages/dashboard/admin_dashboard.php");
            } else {
                header("Location: /lab_rescheduling/pages/select_details.php");
            }
        } else {
            header("Location: /lab_rescheduling/pages/login.php?error=Incorrect password.");
        }
    } else {
        header("Location: /lab_rescheduling/pages/login.php?error=User not found.");
    }
    exit;
}
?>
