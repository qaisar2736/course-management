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

header('Location: course.php');
exit;

require 'includes/header.php';
require 'includes/sidebar.php';
require 'includes/topbar.php';

?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Course</h1>
    <!-- <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>


</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>
