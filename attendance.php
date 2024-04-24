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

if (isset($_POST['update_attendance'])) {
  $day = clean_input($_POST['day']);
  $json_objects = json_decode($_POST['object']);

  foreach ($json_objects as $object) {
    $class_id = clean_input($object->id);
    $present_absent = clean_input($object->value);
    $user_id = $_SESSION['user_id'];
    $data = array(
      'user_id' => $user_id,
      'class_id' => $class_id,
      'day' => $day,
      'present_absent' => $present_absent,
      'date' => date('Y-m-d')
    );

    $already_added = $db->single_row("SELECT * FROM attendance WHERE class_id = $class_id AND user_id = {$_SESSION['user_id']}");
    if (count($already_added) == 0) {
      if ($db->insert('attendance', $data)) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Success!</strong> Course data updated successfully.
        </div>';
      }
    }
  }
  header('Location: ' . URL . 'attendance.php');
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
    <h1 class="h3 mb-0 text-gray-800">Attendance</h1>
    <!-- <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>

  <?php if (isset($_SESSION['message'])): ?>
  <div class="row">
    <div class="col-md-10 col-sm-8">
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success!</strong> Attendance submitted successfully
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- CHECK IF USER HAS ALREADY SUBMITTED ATTENDENCE FOR TODAY OR NOT -->
  <?php 
  // GET TODAYS WEEK DAY
  $todays_date = date('Y-m-d');
  $query = "SELECT * FROM attendance WHERE date = '$todays_date' AND user_id = {$_SESSION['user_id']}";
  $user_attendance = $db->multiple_row($query);
  if (count($user_attendance) == 0):
  ?>
    <?php 
      $daynum = date('w', strtotime('today'));
      $sql = "SELECT course_students.student_id, timetable.id,timetable.course_id,timetable.added_by,timetable.day,timetable.time,timetable.semester, courses.course_name
      FROM course_students 
      INNER JOIN timetable 
      ON course_students.course_id = timetable.course_id 
      INNER JOIN courses 
      ON courses.id = timetable.course_id
      AND timetable.day = $daynum 
      AND course_students.student_id = {$_SESSION['user_id']}
      ORDER BY timetable.time ASC";
      $classes = $db->multiple_row($sql);
      if (count($classes) > 0):
    ?>
    <div class="row">
      <div class="col-md-10 col-sm-8">
        <p>Submit your attendance for today (<?= getTextDay($daynum) ?>)</p>
        <div class="alert alert-warning">
          <strong>Note:</strong> You can only submit your attendance one time!
        </div>
        <form action="" method="POST" name="student_attendance_form">
          <input type="hidden" name="day" value="<?= $daynum ?>">
          <input type="hidden" name="object" value="">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Semester</th>
                <th>Course</th>
                <th>Time</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($classes as $class): ?>
              <tr>
                <td><?= $class['semester'] ?></td>
                <td><?= $class['course_name'] ?></td>
                <td><?= date('h:i:s a ', strtotime($class['time'] . ' 06/13/2013')); ?></td>
                <td>
                  <select name="<?= $class['id'] ?>" id="pa" class="form-control">
                    <option value="">Select</option>
                    <option value="1">Present</option>
                    <option value="0">Absent</option>
                  </select>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div class="form-group">
            <input type="submit" name="update_attendance" value="Update" class="btn btn-success btn-sm">
          </div>
        </form>
      </div>
    </div>
    <?php elseif($daynum == 0 || $daynum == 6): ?>
    <div class="row">
      <div class="col-md-5">
        <div class="alert alert-primary alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Note:</strong> Today is off!
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="row">
      <div class="col-md-5">
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Error!</strong> No class added for <?= getTextDay(date('w', strtotime('today'))) ?>!
        </div>
      </div>
    </div>
    <?php endif; ?>
  <?php elseif (!isset($_SESSION['message'])): ?><!-- IF USER HAS ALREADY SUBMITTED ATTENDANCE -->
  <div class="row">
    <div class="col-md-10 col-sm-8">
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> You have already submitted attendance for today.
      </div>
    </div>
  <?php endif; ?>
  <?php unset($_SESSION['message']); ?>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>