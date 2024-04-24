<?php 

require 'admin/includes/config.php';

if (isset($_SESSION['user_role'])) {
  if ($_SESSION['user_role'] == 'student') {
    header('Location: index.php');
    exit;
  } else if ($_SESSION['user_role'] == 'administrator') {
    header('Location: admin/');
    exit;
  }
}

if (isset($_POST['sign_in'])) {
  $roll_number = clean_input($_POST['roll_number']);
  $password = clean_input($_POST['password']);

  if (empty($roll_number) || empty($password)) {
    $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Error!</strong> Enter rollnumber and password.
    </div>';
    header('Location: login.php');
    exit;
  } else {
    $user = $db->single_row("SELECT * FROM users WHERE roll_number = '$roll_number' OR email = '$roll_number'");

    if (count($user) > 0) {
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] == 'student') {
          header('Location: index.php');
          exit;
        } else if ($user['role'] == 'administrator') {
          header('Location: admin/');
          exit;
        }
      } else {
        $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Error!</strong> Incorrect password.
        </div>';
        header('Location: login.php');
        exit;
      }
    } else {
      $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Incorrect login info.
      </div>';
      header('Location: login.php');
      exit;
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
    <title>Login - Course Management System</title>

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
    </style>
    <!-- Custom styles for this template -->
    <!-- <link href="assets/bootstrap/css/sign-in.css" rel="stylesheet"> -->
    <link rel="icon" href="assets/images/icons/cm_icon.png">
  </head>
<body class="">
  <div class="container pt-4">
    <div class="row">
      <div class="col-sm-8 col-md-6 col-lg-4">
        <form method="POST" action="" name="sign_in_form">
          <div class="text-center">
            <img class="mb-4" src="assets/images/online-education.png" alt="" width="82" height="82">
          </div>
          <h1 class="h3 mb-3 font-weight-normal text-center">Please sign in</h1>
          
          <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
          <?php unset($_SESSION['message']); ?>

          <div class="form-group">
            <label for="roll_number">Rollnumber:</label>
            <input type="text" name="roll_number" class="form-control" autocomplete="true" placeholder="Roll number" required autofocus>
          </div>

          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="inputPassword" autocomplete="current-password" class="form-control" placeholder="Password" required>
          </div>

          <div class="checkbox mb-3">
            <!-- <label>
              <input type="checkbox" value="remember-me"> Remember me
            </label> -->
          </div>

          <button class="btn btn-lg btn-primary btn-block" type="submit" name="sign_in">Sign in</button><br>
          <div class="text-center">
            <p class="mt-5 mb-3 text-muted">&copy; 2019</p>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
