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

if (isset($_POST['update_profile'])) {
	$username = ucfirst(clean_input($_POST['username']));
	$roll_number = clean_input($_POST['roll_number']);
	$password = clean_input($_POST['password']);
	$hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

	if ($db->update(
				'users', 
				['username' => $username, 'password' => $hashed_password, 'roll_number' => $roll_number], 
				['id' => $_SESSION['user_id']])
			)
	{
		$_SESSION['username'] = $username;
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Profile updated successfully.
    </div>';
    header('Location: ' . URL . 'profile.php');
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
    <h1 class="h3 mb-0 text-gray-800">Profile</h1>
    <!-- <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>

  <?php 
  	$profile = $db->single_row("SELECT * FROM users WHERE id = '{$_SESSION['user_id']}'");
  ?>

  <div class="row">
    <div class="col-md-6 col-sm-8">
      <p>Update profile info:</p>
      <?php if (isset($_SESSION['message'])): ?>
      <?= $_SESSION['message']; ?>
      <?php unset($_SESSION['message']); ?>
      <?php endif; ?>
      <form action="" name="update_profile_form" method="POST">
      	<div class="form-group">
      		<label for="username">Username:</label>
      		<input type="text" name="username" class="form-control" value="<?= $profile['username'] ?>">
      	</div>

      	<div class="form-group">
      		<label for="roll_number">Rollnumber:</label>
      		<input type="text" name="roll_number" class="form-control" value="<?= $profile['roll_number'] ?>">
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
      		<input type="submit" value="Update" name="update_profile" class="btn btn-success btn-sm">
      	</div>
      </form>
		</div>
	</div>



</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>