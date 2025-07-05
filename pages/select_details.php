<?php 
// pages/select_details.php
include '../templates/header.php'; 
require_once __DIR__ . '/../includes/db.php';

// Fetch semesters from the database.
$semesters = [];
$query = "SELECT semester_id, name FROM semester ORDER BY semester_id";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $semesters[] = $row;
    }
}
$userRole = $_SESSION['user']['role'];
?>
<div class="card">
  <h3>
    <?php 
      if ($userRole === 'student') { 
          echo "Select Your Semester & Subjects (by Course Code)"; 
      } elseif ($userRole === 'coordinator') { 
          echo "Select the Subjects You Coordinate (by Course Code)"; 
      } elseif ($userRole === 'instructor') { 
          echo "Select the Labs You Instruct (by Course Code)";
      } 
    ?>
  </h3>
  <form action="../includes/save_details.php" method="POST">
    <div class="mb-3">
      <label class="form-label">Semester</label>
      <select name="semester" id="semester" class="form-select" required>
        <option value="">Select Semester</option>
        <?php foreach ($semesters as $sem): ?>
          <option value="<?= $sem['semester_id'] ?>"><?= htmlspecialchars($sem['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">
        <?php 
          if ($userRole === 'student') { 
              echo "Subjects (Course Codes)"; 
          } elseif ($userRole === 'coordinator') { 
              echo "Your Subjects (Course Codes)"; 
          } elseif ($userRole === 'instructor') { 
              echo "Labs (Course Codes)"; 
          } 
        ?>
      </label>
      <!-- The subject dropdown will be populated -->
      <select name="item[]" id="itemSelect" class="form-select" multiple required>
        <option value="">Select a semester first</option>
      </select>
      <small class="form-text text-muted">Select multiple if applicable.</small>
    </div>
    <button type="submit" class="btn btn-primary w-100">Save & Continue</button>
  </form>
</div>

<script>
// When the semester dropdown changes, fetch subjects for that semester.
document.getElementById('semester').addEventListener('change', function(){
  const semesterId = this.value;
  const itemSelect = document.getElementById('itemSelect');
  itemSelect.innerHTML = "<option value=''>Loading...</option>";
  
  fetch('../includes/get_subjects.php?semester_id=' + semesterId)
    .then(response => response.text())
    .then(data => {
      itemSelect.innerHTML = data;
    })
    .catch(() => {
      itemSelect.innerHTML = "<option value=''>Error loading data</option>";
    });
});
</script>

<?php 
include '../templates/footer.php';
?>
