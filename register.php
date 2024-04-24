<?php 

require 'admin/includes/config.php';

header('Location: index.php');
exit;

if (isset($_SESSION['user_role'])) {
  if ($_SESSION['user_role'] == 'student') {
    header('Location: index.php');
    exit;
  } else if ($_SESSION['user_role'] == 'administrator') {
    header('Location: admin/');
    exit;
  }
}

if (isset($_POST['register'])) {
  $email = clean_input($_POST['email']);
  $username = clean_input($_POST['username']);
  $password = clean_input($_POST['password']);
  $confirm_password = clean_input($_POST['confirm_password']);

  if (empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> All fields are required.
    </div>';
    header('Location: register.php');
    exit;
  } else if (strlen($username) < 5) {
    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> Username must be 5 characters long.
    </div>';
    header('Location: register.php');
    exit;
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> Enter a valid email.
    </div>';
    header('Location: register.php');
    exit;
  } else if (strlen($password) < 5) {
    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> Password must be 5 characters long.
    </div>';
    header('Location: register.php');
    exit;
  } else if ($password != $confirm_password) {
    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> Confirm password should be same as password.
    </div>';
    header('Location: register.php');
    exit;
  } else {
    // check if user with same username or email already exist or not
    $user = $db->single_row("SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    
    if (count($user) > 0) {
      $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> User with same username or email already exist.
      </div>';
      header('Location: register.php');
      exit;
    } else {
      $data = array(
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
        'role'  => 'student'
      );
      if ($db->insert('users', $data)) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Success!</strong> Account created successfully.Now you can login.
        </div>';
        header('Location: login.php');
        exit;
      } 
    }
  }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <title>Register - Course Management System</title>

  <!-- <link rel="canonical" href="https://getbootstrap.com/docs/4.3/examples/sign-in/"> -->

  <!-- Bootstrap core CSS -->
  <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script src="assets/js/jquery-3.4.1.min.js"></script>
  <script src="assets/bootstrap/js/bootstrap.min.js"></script>
  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }
    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }
    .register-form {
      max-width: 385px;
      margin: 0 auto;
    }
  </style>
  <!-- Custom styles for this template -->
  <!-- <link href="assets/bootstrap/css/sign-in.css" rel="stylesheet"> -->
  <link rel="icon" href="assets/images/icons/cm_icon.png">
</head>
<body>
  <div class="container">
    <div class="row pt-4">
      <div class="col-sm-8 col-md-6 col-lg-4">
        <form method="POST" action="" name="register_form">
          <div class="text-center">
            <img class="mb-4" src="assets/images/online-education.png" alt="" width="82" height="82">
            <h1 class="h3 mb-3 font-weight-normal">Register:</h1>
          </div>
          <?= isset($_SESSION['error']) ? $_SESSION['error']: false; ?>
          <?php unset($_SESSION['error']); ?>
          <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Username" class="form-control" autofocus>
          </div>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" autocomplete="true" placeholder="Email address" name="email">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" placeholder="Password" name="password" autocomplete="new-password">
          </div>
          <div class="form-group">
            <label for="confirm-password">Confirm password:</label>
            <input type="password" class="form-control" placeholder="Confirm password" autocomplete="new-password" name="confirm_password">
          </div>
          <div class="checkbox mb-3">
            <!-- <label>
              <input type="checkbox" value="remember-me"> Remember me
            </label> -->
          </div>
          <button class="btn btn-lg btn-primary btn-block" name="register" type="submit">Register</button><br>
          <div class="text-center">
            <a href="login.php">Already have an account? Login</a>
          </div>
          <p class="mt-5 mb-3 text-muted text-center">&copy; 2019</p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>