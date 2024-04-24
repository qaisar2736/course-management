<?php 

require 'includes/config.php';
// echo '<pre>';var_dump($_SERVER);echo 
if (!isset($_SESSION['user_role'])) {
  header('Location: ' . URL . 'login.php');
  exit;
} else if ($_SESSION['user_role'] != 'administrator') {
  header('Location: ' . URL);
  exit;
}

if (isset($_POST['save_marks'])) {
  $data = array(
    'student_id' => clean_input($_POST['student']),
    'total_marks' => clean_input($_POST['total']),
    'marks_obtained' => clean_input($_POST['marks_obtained']),
    'type' => clean_input($_POST['exam_type']),
    'course_id' => clean_input($_GET['course']),
    'semester' => clean_input($_GET['semester']),
    'date' => date('Y-m-d')
  );

  if ($db->insert('marks', $data)) {
    unset($_GET['add_new']);

    $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Marks saved successfully.
    </div>';
    $query_string = '';
    foreach ($_GET as $name => $value) {
      $query_string .= "$name=$value&";
    }
    $query_string = rtrim($query_string, '&');
    header('Location: ' . ADMIN_URL . 'marks.php?' . $query_string);
    exit;
  }
}

require 'includes/header.php';
require 'includes/sidebar.php';
require 'includes/topbar.php';

?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Marks</h1>
    <?php if(isset($_GET['course']) && isset($_GET['semester'])): ?>
      <a href="<?= ADMIN_URL . 'marks.php?add_new&' . $_SERVER['QUERY_STRING'] ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a>
    <?php endif; ?>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
  <div class="row">
    <div class="col-md-5">
      <?= $_SESSION['message'] ?>
    </div>
  </div>
  <?php endif; ?><?php unset($_SESSION['message']); ?>

  <?php if (isset($_GET['add_new']) && isset($_GET['semester']) && isset($_GET['course'])): ?>
  <?php 
    $s = clean_input($_GET['semester']);$c = clean_input($_GET['course']);
    $query = "SELECT course_students.course_id, course_students.student_id, course_students.semester, users.id, users.username, users.roll_number
    FROM course_students 
    INNER JOIN users 
    ON course_students.student_id = users.id 
    WHERE course_students.course_id = $c
    AND course_students.semester = $s
    AND users.role = 'student'";
    $students = $db->multiple_row($query);
  ?>
    <div class="row">
      <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8">
        <?php if(count($students) == 0): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Error!</strong> No student added in this semester.
        </div>
        <?php endif; ?>
        <form action="" name="add_marks_form" method="POST">
          <div class="form-group">
            <label for="student">Student:</label>
            <select name="student" id="" class="form-control">
              <option value="">Select student</option>
              <?php foreach($students as $student): ?>
                <option value="<?= $student['id'] ?>"><?= strtoupper($student['roll_number']) ?> - <?= ucfirst($student['username']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="total">Total:</label>
            <input type="number" name="total" class="form-control">
          </div>

          <div class="form-group">
            <label for="marks_obtained">Marks obtained:</label>
            <input type="number" name="marks_obtained" class="form-control">
          </div>

          <div class="form-group">
            <label for="exam_type">Exam type:</label>
            <select name="exam_type" id="" class="form-control">
              <option value="">Select exam type</option>
              <option value="mid">Mid</option>
              <option value="final">Final</option>
            </select>
          </div>

          <div class="form-group">
            <input type="submit" name="save_marks" class="btn btn-success btn-sm" value="Save">
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <?php 
  $semester = 0;$courses = [];
  if (isset($_GET['semester'])) {
    $semester = clean_input($_GET['semester']);

    $courses = $db->multiple_row("SELECT * FROM courses WHERE semester = $semester");
  }
  ?>

  <?php if($semester != 0 && count($courses) == 0): ?>
  <div class="row">
    <div class="col-xl-5 col-lg-6 col-md-8 col-sm-8">
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> No course added in semester <?= $semester ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php 
  $selected_course = 0;
  if (isset($_GET['course'])) {
    $selected_course = clean_input($_GET['course']);
  }
  ?>

  <div class="row">
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-5">
      <form action="" method="GET" name="specific_course_form">
        <div class="form-group">
          <label for="semester">Semester:</label>
          <select name="semester" id="course_semester" class="form-control">
            <option value="">Select semester</option>
            <option value="1" <?= ($semester == 1) ? "selected": false; ?>>1</option>
            <option value="2" <?= ($semester == 2) ? "selected": false; ?>>2</option>
            <option value="3" <?= ($semester == 3) ? "selected": false; ?>>3</option>
            <option value="4" <?= ($semester == 4) ? "selected": false; ?>>4</option>
            <option value="5" <?= ($semester == 5) ? "selected": false; ?>>5</option>
            <option value="6" <?= ($semester == 6) ? "selected": false; ?>>6</option>
            <option value="7" <?= ($semester == 7) ? "selected": false; ?>>7</option>
            <option value="8" <?= ($semester == 8) ? "selected": false; ?>>8</option>
          </select>
        </div>
      </form>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-5">
      <?php if(count($courses) > 0): ?>
      <form action="<?= ADMIN_URL ?>marks.php" method="GET" name="specific_course_form2">
        <input type="hidden" name="semester" value="<?= (isset($_GET['semester'])) ? $_GET['semester']: false; ?>">
        <div class="form-group">
          <label for="courses">Courses:</label>
          <select name="course" id="course" class="form-control">
            <option value="">Select course</option>
            <?php foreach($courses as $course): ?>
            <option value="<?= $course['id'] ?>" <?= ($selected_course == $course['id']) ? "selected": false; ?>><?= $course['course_name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <?php if (isset($_GET['semester']) && isset($_GET['course'])): ?>
    <div class="row">
      <div class="col-md-8">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Student name</th>
              <th>Total marks</th>
              <th>Obtained marks</th>
              <th>Exam type</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $query = "SELECT marks.type, marks.id, marks.student_id, marks.course_id, marks.semester, marks.assignment_id, marks.total_marks, marks.marks_obtained, users.username 
              FROM marks 
              INNER JOIN users ON users.id = marks.student_id
              AND marks.course_id = $selected_course
              AND semester = $semester";
              $marks = $db->multiple_row($query);
              if (count($marks) > 0):
                foreach ($marks as $item):
            ?>
            <tr>
              <td><?= ucfirst($item['username']); ?></td>
              <td><?= $item['total_marks']; ?></td>
              <td><?= $item['marks_obtained']; ?></td>
              <td><?= (!empty($item['assignment_id'])) ? ucfirst('Assignment'): ucfirst($item['type']); ?></td>
            </tr>
                <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td colspan="4" class="text-center"><i>No record found!</i></td>
              </tr>
              <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>