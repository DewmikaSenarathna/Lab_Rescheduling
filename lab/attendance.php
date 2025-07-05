<?php
// lab/attendance.php

require_once '../includes/db.php';
include '../templates/header.php';

$role = $_SESSION['user']['role'];

if ($role === 'student') {
    // Students see only their own attendance records.
    $student_id = $_SESSION['user']['user_id'];
    $attendanceQuery = "
      SELECT a.*, s.course_code, ls.date 
      FROM attendance a
      JOIN lab_schedule ls ON a.schedule_id = ls.schedule_id
      JOIN subjects s ON ls.subject_id = s.subject_id
      WHERE a.student_id = ?
      ORDER BY ls.date DESC
    ";
    $stmt = $conn->prepare($attendanceQuery);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $records = $stmt->get_result();
} elseif ($role === 'instructor') {
    // Instructors see only attendance for lab sessions they instruct.
    $instructor_id = $_SESSION['user']['user_id'];
    $attendanceQuery = "
      SELECT a.*, u.name AS student_name, s.course_code, ls.date 
      FROM attendance a
      JOIN users u ON a.student_id = u.user_id
      JOIN lab_schedule ls ON a.schedule_id = ls.schedule_id
      JOIN subjects s ON ls.subject_id = s.subject_id
      WHERE ls.instructor_id = ?
      ORDER BY ls.date DESC
    ";
    $stmt = $conn->prepare($attendanceQuery);
    $stmt->bind_param("i", $instructor_id);
    $stmt->execute();
    $records = $stmt->get_result();
} elseif ($role === 'coordinator') {
    // Coordinators see attendance records only for subjects they coordinate.
    $coordinator_id = $_SESSION['user']['user_id'];
    $attendanceQuery = "
      SELECT a.*, u.name AS student_name, s.course_code, ls.date 
      FROM attendance a
      JOIN users u ON a.student_id = u.user_id
      JOIN lab_schedule ls ON a.schedule_id = ls.schedule_id
      JOIN subjects s ON ls.subject_id = s.subject_id
      WHERE s.coordinator_id = ?
      ORDER BY ls.date DESC
    ";
    $stmt = $conn->prepare($attendanceQuery);
    $stmt->bind_param("i", $coordinator_id);
    $stmt->execute();
    $records = $stmt->get_result();
} else {  // admin
    $attendanceQuery = "
      SELECT a.*, u.name AS student_name, s.course_code, ls.date 
      FROM attendance a
      JOIN users u ON a.student_id = u.user_id
      JOIN lab_schedule ls ON a.schedule_id = ls.schedule_id
      JOIN subjects s ON ls.subject_id = s.subject_id
      ORDER BY ls.date DESC
    ";
    $records = $conn->query($attendanceQuery);
}
?>

<h3>Attendance Records</h3>

<?php if ($role === 'instructor'): ?>
  <!-- Instructors mark attendance only for students in their department -->
  <form method="POST" action="update_attendance.php" class="mb-4 row g-2 align-items-center">
    <div class="col">
      <select name="student_id" class="form-select" required>
        <option value="">Select Student</option>
        <?php
          // Get the instructor's department first.
          $instructor_id = $_SESSION['user']['user_id'];
          $deptStmt = $conn->prepare("SELECT department_id FROM lab_instructor WHERE instructor_id = ?");
          $deptStmt->bind_param("i", $instructor_id);
          $deptStmt->execute();
          $deptResult = $deptStmt->get_result();
          $dept_id = 0;
          if ($deptRow = $deptResult->fetch_assoc()) {
              $dept_id = $deptRow['department_id'];
          }
          $deptStmt->close();
          
          // List all students from each department.
          $studentStmt = $conn->prepare("
              SELECT u.user_id, u.name 
              FROM users u 
              JOIN student st ON u.user_id = st.student_id 
              WHERE u.role = 'student' AND st.department_id = ?
              ORDER BY u.name
          ");
          $studentStmt->bind_param("i", $dept_id);
          $studentStmt->execute();
          $resultStudents = $studentStmt->get_result();
          while ($s = $resultStudents->fetch_assoc()):
        ?>
          <option value="<?= $s['user_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endwhile;
          $studentStmt->close();
        ?>
      </select>
    </div>
    <div class="col">
      <select name="schedule_id" class="form-select" required>
        <option value="">Select Lab Schedule</option>
        <?php 
          $scheduleStmt = $conn->prepare("
              SELECT ls.schedule_id, s.course_code, ls.date 
              FROM lab_schedule ls
              JOIN subjects s ON ls.subject_id = s.subject_id
              WHERE ls.instructor_id = ?
              ORDER BY ls.date
          ");
          $scheduleStmt->bind_param("i", $instructor_id);
          $scheduleStmt->execute();
          $resultSchedules = $scheduleStmt->get_result();
          while ($sch = $resultSchedules->fetch_assoc()):
        ?>
          <option value="<?= $sch['schedule_id'] ?>">
            <?= htmlspecialchars($sch['course_code']) ?> - <?= htmlspecialchars($sch['date']) ?>
          </option>
        <?php endwhile;
          $scheduleStmt->close();
        ?>
      </select>
    </div>
    <div class="col">
      <select name="status" class="form-select" required>
        <option value="Present">Present</option>
        <option value="Absent">Absent</option>
      </select>
    </div>
    <div class="col">
      <button type="submit" class="btn btn-success">Update Attendance</button>
    </div>
  </form>
<?php endif; ?>

<div class="table-responsive">
  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <?php if ($role !== 'student'): ?>
          <th>Student</th>
        <?php endif; ?>
        <th>Course Code</th>
        <th>Date</th>
        <th>Status</th>
        <?php if ($role === 'instructor'): ?>
          <th>Action</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $records->fetch_assoc()): ?>
        <tr>
          <?php if ($role !== 'student'): ?>
            <td><?= htmlspecialchars($row['student_name'] ?? 'N/A') ?></td>
          <?php endif; ?>
          <td><?= htmlspecialchars($row['course_code']) ?></td>
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <?php if ($role === 'instructor'): ?>
            <td>
              <form action="delete_attendance.php" method="POST" style="display:inline;">
                <input type="hidden" name="attendance_id" value="<?= $row['attendance_id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
              </form>
            </td>
          <?php endif; ?>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php 
if ($role === 'student') {
    $stmt->close();
}
include '../templates/footer.php';
?>
