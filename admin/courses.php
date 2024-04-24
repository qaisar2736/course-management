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

if (isset($_POST['add_course'])) {
  $course_name = clean_input($_POST['course_name']);
  $semester = clean_input($_POST['semester']);

  $course = $db->single_row("SELECT * FROM courses WHERE course_name = '$course_name' AND semester = $semester");
  if (count($course) > 0) {
    $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> Course with same name already exist in semester ' . $semester . '.
    </div>';
    header('Location: ' . ADMIN_URL . 'courses.php?add_new');
    exit;
  } else {
    $data = array(
      'course_name' => ucfirst($course_name),
      'semester' => $semester
    );

    if ($db->insert('courses', $data)) {
      $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success!</strong> Course added successfully.
      </div>';
      header('Location: ' . ADMIN_URL . 'courses.php?add_new');
      exit;
    }
  }
}

if (isset($_GET['delete_course'])) {
  $course_id = clean_input($_GET['delete_course']);

  if ($db->delete('courses', ['id' => $course_id])) {
    $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Course deleted successfully.
    </div>';
    header('Location: ' . ADMIN_URL . 'courses.php');
    exit;
  }
}

if (isset($_POST['update_course'])) {
  $course_id = clean_input($_POST['course_id']);
  $course_name = clean_input($_POST['course_name']);
  $semester = clean_input($_POST['semester']);

  $course = $db->single_row("SELECT * FROM courses WHERE id = '$course_id'");
  if (count($course) > 0) {
    // CHECK IF COURSE WITH SAME NAME ALREADY EXIST OR NOT
    $course = $db->single_row("SELECT * FROM courses WHERE course_name = '$course_name' AND semester = $semester AND id <>$course_id");
    
    if (count($course) > 0) {
      $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Course with same name already exist in semester '. $semester .'.
      </div>';
      header('Location:' . ADMIN_URL . 'courses.php?' . $_SERVER['QUERY_STRING']);
      exit;
    } else {
      $data = array(
        'course_name' => ucfirst($course_name),
        'semester' => $semester
      );
      $condition = ['id' => $course_id];

      if ($db->update('courses', $data, $condition)) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Success!</strong> Course updated successfully.
        </div>';
        header('Location:' . ADMIN_URL . 'courses.php');
        exit;
      }
    }
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
      <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a>
    </div>

    <?php if (isset($_GET['edit_course'])):
      $course_id = clean_input($_GET['edit_course']);
      $course = $db->single_row("SELECT * FROM courses WHERE id = '$course_id'");
      if (count($course) > 0):
    ?>
      <div class="row">
        <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8">
          <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
          <?php unset($_SESSION['message']); ?>
          <form action="" name="update_course_form" method="POST">
            <input type="hidden" name="course_id" value="<?= $course_id; ?>">
            <div class="form-group">
              <label for="course_name">Course name:</label>
              <input type="text" name="course_name" class="form-control" value="<?= $course['course_name'] ?>">
            </div>

            <div class="form-group">
              <label for="semester">Select semester:</label>
              <select name="semester" id="semester" class="form-control">
                <option value="">Select</option>
                <option value="1" <?= ($course['semester'] == 1) ? "selected": false; ?>>1</option>
                <option value="2" <?= ($course['semester'] == 2) ? "selected": false; ?>>2</option>
                <option value="3" <?= ($course['semester'] == 3) ? "selected": false; ?>>3</option>
                <option value="4" <?= ($course['semester'] == 4) ? "selected": false; ?>>4</option>
                <option value="5" <?= ($course['semester'] == 5) ? "selected": false; ?>>5</option>
                <option value="6" <?= ($course['semester'] == 6) ? "selected": false; ?>>6</option>
                <option value="7" <?= ($course['semester'] == 6) ? "selected": false; ?>>7</option>
                <option value="8" <?= ($course['semester'] == 7) ? "selected": false; ?>>8</option>
              </select>
            </div>

            <div class="form-group">
              <input type="submit" name="update_course" class="btn btn-success btn-sm" value="Update">
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['add_new'])): ?>
    <div class="row">
      <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8">
        <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
        <?php unset($_SESSION['message']); ?>
        <form action="" name="add_course_form" method="POST">
          <div class="form-group">
            <label for="course_name">Course name:</label>
            <input type="text" name="course_name" class="form-control">
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
            <input type="submit" name="add_course" class="btn btn-primary btn-sm" value="Add">
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-xl-8 col-lg-7 col-md-10">
        <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
        <?php unset($_SESSION['message']); ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Semester</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php 
            $courses = $db->multiple_row("SELECT * FROM courses ORDER BY id DESC");
            if (count($courses) > 0):
            foreach ($courses as $course):
          ?>
            <tr role="row">
              <td><?= $course['course_name'] ?></td>
              <td><?= $course['semester'] ?></td>
              <td>
                <a href="<?= ADMIN_URL ?>courses.php?edit_course=<?= $course['id'] ?>" class="btn btn-success btn-circle btn-sm">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="<?= ADMIN_URL ?>courses.php?delete_course=<?= $course['id'] ?>" class="btn btn-danger btn-circle btn-sm delete">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="text-center"><i>No course added yet!</i></td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
  <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>