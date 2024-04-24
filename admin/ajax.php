<?php 

require 'includes/config.php';

if ($_SESSION['user_role'] != 'administrator') {
  header('Content-Type: application/json');
  echo json_encode(['error' => 'You are not allowed to access this page!']);
  exit;
}

if (isset($_GET['get_semester_courses'])) {
  $semester = clean_input($_GET['semester']);
  $courses = $db->multiple_row("SELECT * FROM courses WHERE semester = $semester");

  header('Content-Type: application/json');
  echo json_encode($courses);
  exit;
}

if (isset($_POST['update_attendance'])) {
  $id = clean_input($_POST['update_attendance']);
  $present_absent = clean_input($_POST['new_value']);

  if ($db->update('attendance', ['present_absent' => $present_absent], ['id' => $id])) {
    header('Content-Type: application/json');
    echo json_encode(['updated' => true]);
    exit;
  }
}

// DELETE CLASS FROM TIMETABLE
if (isset($_POST['delete_class'])) {
  $timetable_id = clean_input($_POST['delete_class']);

  if ($db->delete('timetable', ['id' => $timetable_id])) {
    header('Content-Type: application/json');
    echo json_encode(['deleted' => true]);
    exit;
  }
}

// DELETE ASSIGNMENT
if (isset($_POST['delete_assignment'])) {
  $id = clean_input($_POST['delete_assignment']);
  $course_id = clean_input($_POST['course_id']);
  $assignment = $db->single_row("SELECT * FROM assignments WHERE id = $id AND course_id = $course_id");

  $file = "../files/assignments/1/hell.txt";
  if (count($assignment) > 0) {
    $file_name = $assignment['name'];
    $file = "../files/assignments/$course_id/$file_name";
  }
  if ($db->delete('assignments', ['id' => $id])) {
    if (file_exists($file)) {
      unlink($file);
    }
    header('Content-Type: application/json');
    echo json_encode(['deleted' => true]);
    exit;
  }
}

// DELETE STUDENT ASSIGNMENT
if (isset($_POST['delete_submitted_assignment'])) {
  $id = clean_input($_POST['delete_submitted_assignment']);
  $course_id = clean_input($_POST['course_id']);
  $assignment = $db->single_row("SELECT * FROM student_assignment_submissions WHERE id = $id AND course_id = $course_id");

  $file = "../files/assignments/1/hell.txt";
  if (count($assignment) > 0) {
    $file_name = $assignment['file'];
    $file = "../files/assignments/$course_id/student_submissions/$file_name";
  }
  if ($db->delete('student_assignment_submissions', ['id' => $id])) {
    if (file_exists($file)) {
      unlink($file);
    }
    header('Content-Type: application/json');
    echo json_encode(['deleted' => true]);
    exit;
  }
}

// GET LEAVE DETAILS
if (isset($_GET['get_leave_details'])) {
  $id = clean_input($_GET['get_leave_details']);
  $leave = $db->single_row("SELECT * FROM leaves WHERE id = $id");
  header('Content-Type: application/json');
  echo json_encode($leave);
}