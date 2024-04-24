<?php 

require 'admin/includes/config.php';

if (!isset($_SESSION['user_role'])) {
  header('Location: login.php');
  exit;
}

if ($_SESSION['user_role'] == 'administrator') {
  header('Location: admin/');
  exit;
}

if (isset($_POST['student_add_course'])) {
  $semester = clean_input($_POST['semester']);
  $db->delete('course_students', ['student_id' => $_SESSION['user_id'], 'semester' => $semester]);
  if (isset($_POST['courses'])) {
    foreach ($_POST['courses'] as $id) {
      $id = clean_input($id);
      $data = array(
        'course_id' => $id,
        'student_id' => $_SESSION['user_id'],
        'semester' => $semester
      );
      $already_added = $db->single_row("SELECT * FROM course_students WHERE student_id = {$_SESSION['user_id']} AND course_id=$id");
      
      if (count($already_added) == 0) {
        if ($db->insert('course_students', $data)) {
          $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> Course data updated successfully.
          </div>';
        }
      }
    }
    header('Location: ' . URL . 'courses.php?' . $_SERVER['QUERY_STRING']);
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
    <h1 class="h3 mb-0 text-gray-800">Courses</h1>
    <!-- <a href="<?= URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new course</a> -->
  </div>

  <?php
  $semester = 1;
  if (isset($_GET['semester'])) {
    $semester = clean_input($_GET['semester']);
  }
  ?>

  <div class="row mb-4">
    <div class="col-md-3">
      <form action="" name="student_add_course_form">
        <input type="hidden" name="add_new" value="1">
        <label for="semester">Semester</label>
        <select name="semester" id="semester" class="form-control">
          <option value="">Select semester</option>
          <option value="1" <?= ($semester == 1) ? "selected" : false; ?>>1</option>
          <option value="2" <?= ($semester == 2) ? "selected" : false; ?>>2</option>
          <option value="3" <?= ($semester == 3) ? "selected" : false; ?>>3</option>
          <option value="4" <?= ($semester == 4) ? "selected" : false; ?>>4</option>
          <option value="5" <?= ($semester == 5) ? "selected" : false; ?>>5</option>
          <option value="6" <?= ($semester == 6) ? "selected" : false; ?>>6</option>
          <option value="7" <?= ($semester == 7) ? "selected" : false; ?>>7</option>
          <option value="8" <?= ($semester == 8) ? "selected" : false; ?>>8</option>
        </select>
      </form>
    </div>
  </div>

  <?php if ($semester > 0):
    $courses = $db->multiple_row("SELECT * FROM courses WHERE semester = $semester");
    if (count($courses) > 0):
  ?>
  <div class="row">
    <div class="col-md-5">
      <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
      <?php unset($_SESSION['message']); ?>
      <form action="" method="POST">
        <input type="hidden" name="semester" value="<?= $semester ?>">
        <div class="form-group">
          <label for="courses">Courses offered in this semester:</label>
          <?php foreach ($courses as $course): ?>
          <?php 
            $checked = '';
            $sql = "SELECT * FROM course_students WHERE course_id = {$course['id']} AND student_id = {$_SESSION['user_id']}";
            $already_taking = $db->single_row($sql);
            if (count($already_taking) > 0) {
              $checked = 'checked';
            }
          ?>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="<?= $course['id'] ?>" name="courses[]" value="<?= $course['id'] ?>" <?= $checked ?>>
            <label class="custom-control-label" for="<?= $course['id'] ?>"><?= $course['course_name']; ?></label>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="form-group">
          <input type="submit" name="student_add_course" value="Update" class="btn btn-success btn-sm">
        </div>
      </form>
    </div>
  </div>
    <?php else: ?>
      <div class="row">
        <div class="col-md-5">
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error!</strong> No course added in this semester!
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php 
    $sql = 'SELECT courses.course_name, course_students.id, course_students.course_id, course_students.student_id, course_students.semester 
    FROM courses 
    INNER JOIN course_students ON courses.id = course_students.course_id
    AND course_students.semester=' . $semester . '
    AND course_students.student_id=' . $_SESSION['user_id'];
    $courses_taking = $db->multiple_row($sql);
    if (count($courses_taking) > 0): // count($courses) > 0
  ?>
  <div class="row">
    <div class="col-md-5">
      <div class="card">
        <div class="card-header"><h5>Courses you are taking in semester <?= $semester ?>:</h5></div>
        <div class="card-body">
        <ul class="list-group">
          <?php foreach($courses_taking as $course): ?>
          <li class="list-group-item"><?= $course['course_name'] ?></li>
          <?php endforeach; ?>
        </ul>
        </div>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="row">
    <div class="col-md-5">
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> You have not set any course yet!
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>
