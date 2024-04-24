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

if (isset($_POST['add_class'])) {
  $day = clean_input($_POST['day']);
  $semester = clean_input($_POST['semester']);
  $course = clean_input($_POST['course']);
  $time = clean_input($_POST['course_time']);

  $data = array(
    'course_id' => $course,
    'added_by' => $_SESSION['user_id'],
    'day' => $day,
    'time' => "$time:00",
    'semester' => $semester
  );

  if ($db->insert('timetable', $data)) {
    $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Class added successfully.
    </div>';
    header('Location: ' . ADMIN_URL . 'timetable.php?' . $_SERVER['QUERY_STRING']);
    exit;
  }
}

if (isset($_GET['delete_class'])) {
  $id = clean_input($_GET['delete_class']);

  if ($db->delete('timetable', ['id' => $id])) {
    $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Class deleted successfully.
    </div>';
    header('Location: ' . ADMIN_URL . 'timetable.php?day=' . $_GET['day']);
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
    <h1 class="h3 mb-0 text-gray-800">Timetable</h1>
    <?php if (isset($_GET['day'])): ?>
    <a href="<?= ADMIN_URL ?>timetable.php?add_new&day=<?= $_GET['day'] ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new class</a>
    <?php endif; ?>
  </div>

  <?php 
  $day = 0;
  if (isset($_GET['day'])) {
    $day = clean_input($_GET['day']);
  }
  ?>

<?php if (isset($_GET['add_new']) && isset($_GET['day'])): ?>
    <div class="row">
      <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8">
        <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
        <?php unset($_SESSION['message']); ?>
        <form action="" name="tt_add_course_form" method="POST">
          <div class="form-group">
            <label for="day">Day:</label>
            <select name="day" id="timetable_day" class="form-control">
              <option value="">Select day</option>
              <option value="1" <?= ($day == 1) ? "selected": false; ?>><?= getTextDay(1) ?></option>
              <option value="2" <?= ($day == 2) ? "selected": false; ?>><?= getTextDay(2) ?></option>
              <option value="3" <?= ($day == 3) ? "selected": false; ?>><?= getTextDay(3) ?></option>
              <option value="4" <?= ($day == 4) ? "selected": false; ?>><?= getTextDay(4) ?></option>
              <option value="5" <?= ($day == 5) ? "selected": false; ?>><?= getTextDay(5) ?></option>
            </select>
          </div>

          <div class="form-group">
            <label for="semester">Select semester:</label>
            <select name="semester" id="semester" class="form-control">
              <option value="">Select</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
            </select>
          </div>

          
          <div class="form-group">
            <label for="course_name">Select course:</label>
            <select name="course" id="course" class="form-control">
            </select>
          </div>

          <div class="form-group">
            <label for="course_time">Time:</label>
            <input type="time" name="course_time" class="form-control" id="course_time">
          </div>

          <div class="form-group">
            <input type="submit" name="add_class" class="btn btn-primary btn-sm" value="Add">
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>

  <div class="row">
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-5">
      <form action="" method="GET" name="timetable_day_form">
        <div class="form-group">
          <label for="day">Day:</label>
          <select name="day" id="timetable_day" class="form-control">
            <option value="">Select day</option>
            <option value="1" <?= ($day == 1) ? "selected": false; ?>><?= getTextDay(1) ?></option>
            <option value="2" <?= ($day == 2) ? "selected": false; ?>><?= getTextDay(2) ?></option>
            <option value="3" <?= ($day == 3) ? "selected": false; ?>><?= getTextDay(3) ?></option>
            <option value="4" <?= ($day == 4) ? "selected": false; ?>><?= getTextDay(4) ?></option>
            <option value="5" <?= ($day == 5) ? "selected": false; ?>><?= getTextDay(5) ?></option>
          </select>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($_GET['day'])): ?>
  <?php $day = clean_input($_GET['day']); ?>
  <div class="row">
      <div class="col-xl-8 col-lg-7 col-md-10">
        <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
        <?php unset($_SESSION['message']); ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Semester</th>
              <th>Class</th>
              <th>Time</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php 
            $classes = $db->multiple_row("SELECT courses.course_name, timetable.id, timetable.course_id,timetable.added_by,timetable.day,timetable.time,timetable.semester
            FROM courses
            INNER JOIN timetable ON timetable.course_id = courses.id WHERE timetable.day = $day ORDER BY timetable.time ASC");
            if (count($classes) > 0):
            foreach ($classes as $class):
          ?>
            <tr role="row">
              <td><?= $class['semester'] ?></td>
              <td><?= $class['course_name'] ?></td>
              <td><?= $class['time'] ?></td>
              <td>
                <a href="<?= ADMIN_URL ?>timetable.php?day=<?= $day; ?>&delete_class=<?= $class['id'] ?>" class="btn btn-danger btn-circle btn-sm delete">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center"><i>No class added yet!</i></td>
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