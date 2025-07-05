<?php
// lab/review_requests.php
include '../templates/header.php';
require_once '../includes/db.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['user_id'];

// Build the base query joining subjects to get course code and other details.
$query = "
  SELECT r.*, s.course_code, s.name AS subject_name, u.name AS student_name 
  FROM reschedule_request r
  JOIN subjects s ON r.subject_id = s.subject_id
  JOIN users u ON r.student_id = u.user_id
";

// Append conditions based on role.
if ($role === 'coordinator') {
    // Subject coordinator sees only requests for the subjects they coordinate.
    $query .= " WHERE s.coordinator_id = {$user_id}";
} elseif ($role === 'instructor') {
    // Lab instructor sees only requests forwarded by a coordinator.
    $query .= " WHERE r.status = 'Forwarded by Coordinator'";
}
// Admin sees all requests.
$query .= " ORDER BY r.created_at DESC";
$result = $conn->query($query);
?>

<div class="card">
  <h3>Reschedule Requests</h3>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Student</th>
          <th>Course Code</th>
          <th>Subject</th>
          <th>Reason</th>
          <th>Document</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['course_code']) ?></td>
            <td><?= htmlspecialchars($row['subject_name']) ?></td>
            <td><?= htmlspecialchars($row['reason']) ?></td>
            <td>
              <?php if ($row['medical_form']): ?>
                <a href="../<?= $row['medical_form'] ?>" target="_blank">View</a>
              <?php else: ?>
                N/A
              <?php endif; ?>
            </td>
            <td><span class="badge bg-info"><?= htmlspecialchars($row['status']) ?></span></td>
            <td>
              <?php if ($role === 'admin'): ?>
                <!-- Admin can approve or reject requests -->
                <form method="POST" action="process_review.php">
                  <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                  <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button><br><br>
                  <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                </form>
              <?php elseif ($role === 'coordinator'): ?>
                <!-- In the coordinator view, display a dropdown of lab instructors -->
                <form method="POST" action="process_review.php">
                  <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                  <select name="instructor_id" class="form-select mb-2" required>
                    <option value="">Select Lab Instructor</option>
                    <?php
                      // Get the department_id for coordinator.
                      $deptQuery = "SELECT department_id FROM subject_coordinator WHERE coordinator_id = ?";
                      $deptStmt = $conn->prepare($deptQuery);
                      $deptStmt->bind_param("i", $user_id);
                      $deptStmt->execute();
                      $deptResult = $deptStmt->get_result();
                      if ($deptRow = $deptResult->fetch_assoc()) {
                          $department_id = $deptRow['department_id'];
                          
                          // List lab instructors in the same department.
                          $insQuery = "SELECT li.instructor_id AS user_id, u.name FROM lab_instructor li JOIN users u ON li.instructor_id = u.user_id WHERE li.department_id = ? ORDER BY u.name";
                          $insStmt = $conn->prepare($insQuery);
                          $insStmt->bind_param("i", $department_id);
                          $insStmt->execute();
                          $insResult = $insStmt->get_result();
                          while ($ins = $insResult->fetch_assoc()):
                    ?>
                        <option value="<?= $ins['user_id'] ?>"><?= htmlspecialchars($ins['name']) ?></option>
                    <?php 
                          endwhile;
                          $insStmt->close();
                      }
                      $deptStmt->close();
                    ?>
                  </select>
                  <button name="action" value="forward" class="btn btn-success btn-sm">Forward</button><br><br>
                  <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                </form>
              <?php elseif ($role === 'instructor'): ?>
                <!-- Lab instructor view -->
                <?php if ($row['status'] == 'Forwarded by Coordinator'): ?>
                  <a href="/lab_rescheduling/lab/reschedule_schedule.php?request_id=<?= $row['request_id'] ?>" class="btn btn-warning btn-sm">Schedule Lab</a>
                <?php else: ?>
                  N/A
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../templates/footer.php'; ?>
