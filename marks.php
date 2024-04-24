<?php 

require 'admin/includes/config.php';
// echo '<pre>';var_dump($_SERVER);echo 
if (!isset($_SESSION['user_role'])) {
  header('Location: ' . URL . 'login.php');
  exit;
} else if ($_SESSION['user_role'] == 'administrator') {
  header('Location: ' . ADMIN_URL);
  exit;
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
    <!-- <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>


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
      <form action="<?= URL ?>marks.php" method="GET" name="specific_course_form2">
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
              <th>Total marks</th>
              <th>Obtained marks</th>
              <th>Date</th>
              <th>Exam type</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $query = "SELECT marks.type, marks.id, marks.student_id, marks.course_id, marks.semester, marks.assignment_id, marks.total_marks, marks.marks_obtained, marks.date, users.username 
              FROM marks 
              INNER JOIN users ON users.id = marks.student_id
              AND marks.course_id = $selected_course
              AND marks.semester = $semester
              AND marks.student_id = {$_SESSION['user_id']}
              ORDER BY marks.date DESC";
              
              $marks = $db->multiple_row($query);
              if (count($marks) > 0):
                foreach ($marks as $item):
            ?>
            <tr>
              <td><?= $item['total_marks']; ?></td>
              <td><?= $item['marks_obtained']; ?></td>
              <td><?= date_format(date_create($item['date']), 'd-m-Y'); ?></td>
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