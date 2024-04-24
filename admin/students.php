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

if (isset($_POST['add_student'])) {
  $username = clean_input($_POST['username']);
  $password = clean_input($_POST['password']);
  $email = clean_input($_POST['email']);
  $semester = clean_input($_POST['semester']);
  $course = clean_input($_POST['course']);
  $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

  // CHECK IF USER ALREAD EXIST OR NOT
  $already_user = $db->single_row("SELECT * FROM users WHERE email = '$email'");
  if (count($already_user) > 0) {
    $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> User with same email already exist.
    </div>';
    header('Location: ' . ADMIN_URL . 'students.php');
    exit;
  } else {
    $data = ['email' => $email, 'username' => $username, 'password' => $hashed_password, 'role' => 'student'];
    $db->insert('users', $data);
    $user_id = $db->single_row("SELECT id FROM users WHERE email = '$email'")['id'];
    $data2 = ['course_id' => $course, 'student_id' => $user_id, 'semester' => $semester];
    if ($db->insert('course_students', $data2)) {
      $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success!</strong> User added to the course successfully.
      </div>';
      header('Location: ' . ADMIN_URL . 'students.php');
      exit;
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
    <h1 class="h3 mb-0 text-gray-800">Students</h1>
    <!-- <a href="<?= ADMIN_URL ?>students.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>

  <div class="row">
    <div class="col-sm-10 col-md-8 col-lg-5 col-xl-4">
      <?php if (isset($_SESSION['message'])): ?>
      <?= $_SESSION['message'] ?>
      <?php unset($_SESSION['message']); ?>
      <?php endif; ?>
      <p>Add students to the course:</p>
      <form action="" method="POST" name="add_students_form">
        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" name="username" class="form-control">
        </div>

        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" name="password" class="form-control">
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm password:</label>
          <input type="password" name="confirm_password" class="form-control">
        </div>

        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" name="email" class="form-control">
        </div>

        <div class="form-group">
          <label for="semester">Semester:</label>
          <select name="semester" id="semester" class="form-control">
            <option value="">Select semester</option>
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
          <label for="course">Course:</label>
          <select name="course" id="course" class="form-control">
            <option value="">Select course</option>
          </select>
        </div>
        
        <div class="form-group">
          <input type="submit" name="add_student" value="Add" class="btn btn-success btn-sm">
        </div>
      </form>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
    </div>
  </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>