<?php
// includes/get_subjects.php
require_once __DIR__ . '/db.php';

// Check if the semester_id is sent via GET
if (isset($_GET['semester_id'])) {
    $semester_id = intval($_GET['semester_id']);
    

    $query = "SELECT subject_id, name, course_code FROM subjects WHERE semester_id = ? ORDER BY course_code";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<option value=''>Error preparing statement.</option>";
        exit;
    }
    $stmt->bind_param("i", $semester_id);
    
    if (!$stmt->execute()){
        echo "<option value=''>Error executing query.</option>";
        exit;
    }
    
    $result = $stmt->get_result();
    
    $options = "<option value=''>Select Subject</option>";
    while ($row = $result->fetch_assoc()) {
        $course_code = htmlspecialchars($row['course_code']);
        $subject_name = htmlspecialchars($row['name']);
        $options .= "<option value='{$course_code}'>{$course_code} - {$subject_name}</option>";
    }
    echo $options;
    $stmt->close();
}
?>
