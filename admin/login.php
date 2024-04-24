<?php 

require 'includes/config.php';

if (isset($_POST['admin_login'])) {
	$email = clean_input($_POST['email']);
	$password = clean_input($_POST['password']);

  if (empty($email) || empty($password)) {
    $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
      <strong>Error!</strong> Enter email and password.
    </div>';
    header('Location: login.php');
    exit;
  } else {
    $user = $db->single_row("SELECT * FROM users WHERE email = '$email'");

    if (count($user) > 0) {
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] == 'administrator') {
          header('Location: index.php');
          exit;
        }
      } else {
        $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
          <strong>Error!</strong> Incorrect password.
        </div>';
        header('Location: login.php');
        exit;
      }
    } else {
      $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
        <strong>Error!</strong> Incorrect login info.
      </div>';
      header('Location: login.php');
      exit;
    }
  }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ADMIN - LOGIN</title>
	<link rel="icon" href="<?= URL ?>assets/images/icons/cm_icon.png">
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<style>
		body{
		  margin: 0;
		  padding: 0;
		  font-family: sans-serif;
		  background: #191919;
		}
		.box{
		  max-width: 300px;
		  padding: 40px;
		  position: absolute;
		  top: 50%;
		  left: 50%;
		  transform: translate(-50%,-50%);
		  background: #191919;
		  text-align: center;
		}
		.box h1{
		  color: white;
		  text-transform: uppercase;
		  font-weight: 500;
		}
		.box input[type = "text"], .box input[type = "email"],.box input[type = "password"]{
		  border:0;
		  background: none;
		  display: block;
		  margin: 20px auto;
		  text-align: center;
		  border: 2px solid #3498db;
		  padding: 14px 10px;
		  width: 200px;
		  outline: none;
		  color: white;
		  border-radius: 24px;
		  transition: 0.25s;
		}
		/*.box input[type = "text"]:focus, .box input[type = "email"]:focus,.box input[type = "password"]:focus{
		  width: 280px;
		  border-color: #2ecc71;
		}*/
		.box input[type = "submit"]{
		  border:0;
		  background: none;
		  display: block;
		  margin: 20px auto;
		  text-align: center;
		  border: 2px solid #2ecc71;
		  padding: 14px 40px;
		  outline: none;
		  color: white;
		  border-radius: 24px;
		  transition: 0.25s;
		  cursor: pointer;
		}
		.box input[type = "submit"]:hover{
		  background: #2ecc71;
		}

		.alert-danger {
		  color: #721c24;
		  background-color: #f8d7da;
		  border-color: #f5c6cb;
		}

		.alert-success {
			color: #721c24;
		  background-color: #f8d7da;
		  border-color: #f5c6cb;
		}

		.alert {
		  position: relative;
		  padding: .75rem 1.25rem;
		  margin-bottom: 1rem;
		  border: 1px solid transparent;
		  border-radius: .25rem;
		}

		.alert-error {
			display: none;
		}

		* {
			font-family: Verdana, sans-serif;;
		}
	</style>
</head>
<body>

<form class="box" action="" name="admin_login" method="post">
	<h1>Login</h1>
	<div class="alert alert-success">
		email: admin@gmail.com
		password: admin
	</div>
	<div class="alert alert-danger alert-error">
    You should read this message.
  </div>

  <?php if(isset($_SESSION['message'])): ?>
	<?= $_SESSION['message']; ?>
	<?php unset($_SESSION['message']); ?>
	<?php endif; ?>
  
  <input type="text" name="email" placeholder="Email">
  <input type="password" name="password" placeholder="Password">
  <input type="submit" name="admin_login" value="Login">
</form>

<!-- Bootstrap core JavaScript-->
<script src="<?= URL ?>assets/js/jquery-3.4.1.min.js"></script>
<script src="<?= URL ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= URL ?>assets/js/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= URL ?>assets/js/sb-admin-2.min.js"></script>

<script src="<?= URL ?>assets/js/sweet-alert.min.js"></script>

<script>
$(document).ready(function() {

	function validateEmail(email) {
	  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  return re.test(String(email).toLowerCase());
	}

	$('form[name="admin_login"]').submit(function(e) {
		var email = ($('input[name="email"]').val()).trim();
		var password = ($('input[name="password"]').val()).trim();

		if (email == '' || password == '') {
			e.preventDefault();
			$('.alert-error').html('<strong>Error</strong> All fields are required!');
			$('.alert-error').show();
		} else if (!validateEmail(email)) {
			e.preventDefault();
			$('.alert-error').html('<strong>Error</strong> Enter valid email');
			$('.alert-error').show();
		}
	});
});
</script>
</body>
</html>