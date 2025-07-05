<?php
// lab/schedule.php
require_once '../includes/db.php';
include '../templates/header.php';

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['user_id'];

// Start with a base query using WHERE 1=1.
$query = "
  SELECT ls.*, s.course_code, s.name AS subject_name, l.lab_name, u.name AS instructor_name 
  FROM lab_schedule ls
  LEFT JOIN subjects s ON ls.subject_id = s.subject_id
  LEFT JOIN lab l ON ls.lab_id = l.lab_id
  LEFT JOIN users u ON ls.instructor_id = u.user_id
  WHERE 1=1
";

if ($role === 'coordinator') {
    // Subject coordinators see only schedules for subjects they coordinate.
    $query .= " AND s.coordinator_id = {$user_id}";
} elseif ($role === 'instructor') {
    // Instructors see only their own schedules.
    $query .= " AND ls.instructor_id = {$user_id}";
} elseif ($role === 'student') {
    // Students should see only the schedules for subjects they selected.
    if (!isset($_SESSION['details']['items']) || empty($_SESSION['details']['items'])) {
        echo "<div class='alert alert-warning'>Please complete your subject selection <a href='/lab_rescheduling/pages/select_details.php'>here</a>.</div>";
        include '../templates/footer.php';
        exit;
    }
    // Build an IN clause from the selected course codes.
    $selected = array_map(function($val) use ($conn) {
        return "'" . $conn->real_escape_string($val) . "'";
    }, $_SESSION['details']['items']);
    $inClause = implode(",", $selected);
    $query .= " AND s.course_code IN ($inClause)";
}

$query .= " ORDER BY ls.date";
$result = $conn->query($query);
?>

<div class="card">
  <h3>Lab Schedule (Original Sessions)</h3>
  
  <?php if ($role === 'coordinator'): ?>
  <!-- Subject coordinators can add/update schedules -->
  <form method="POST" action="update_schedule.php" class="mb-4 row g-2">
    <div class="col-md-3">
      <select name="subject_id" class="form-select" required>
        <option value="">Select Course Code</option>
        <?php
        // For coordinators only, list the subjects they coordinate.
        $subQuery = "SELECT subject_id, course_code, name FROM subjects WHERE coordinator_id = {$user_id} ORDER BY course_code";
        $subResult = $conn->query($subQuery);
        while ($s = $subResult->fetch_assoc()):
        ?>
          <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['course_code']) ?> - <?= htmlspecialchars($s['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="date" class="form-control" required>
    </div>
    <div class="col-md-2">
      <input type="text" name="time_slot" class="form-control" placeholder="Time Slot" required>
    </div>
    <div class="col-md-2">
      <select name="lab_id" class="form-select" required>
        <option value="">Select Lab</option>
        <?php
        $labsQuery = "SELECT lab_id, lab_name FROM lab ORDER BY lab_name";
        $labsResult = $conn->query($labsQuery);
        while ($lab = $labsResult->fetch_assoc()):
        ?>
          <option value="<?= $lab['lab_id'] ?>"><?= htmlspecialchars($lab['lab_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="instructor_id" class="form-select" required>
        <option value="">Select Instructor</option>
        <?php
        $instrQuery = "SELECT user_id, name FROM users WHERE role = 'instructor' ORDER BY name";
        $instrResult = $conn->query($instrQuery);
        while ($ins = $instrResult->fetch_assoc()):
        ?>
          <option value="<?= $ins['user_id'] ?>"><?= htmlspecialchars($ins['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-1">
      <button type="submit" class="btn btn-success w-100">Add</button>
    </div>
  </form>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Course Code</th>
          <th>Subject</th>
          <th>Date</th>
          <th>Time Slot</th>
          <th>Lab</th>
          <th>Instructor</th>
          <?php if ($role === 'coordinator'): ?>
            <th>Action</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['course_code']) ?></td>
            <td><?= htmlspecialchars($row['subject_name']) ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['time_slot']) ?></td>
            <td><?= htmlspecialchars($row['lab_name']) ?></td>
            <td><?= htmlspecialchars($row['instructor_name']) ?></td>
            <?php if ($role === 'coordinator'): ?>
              <td>
                <form method="POST" action="delete_schedule.php" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                  <input type="hidden" name="schedule_id" value="<?= $row['schedule_id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                </form>
              </td>
            <?php endif; ?>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../templates/footer.php'; ?>
