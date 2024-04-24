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

if (isset($_GET['delete'])) {
  $leave_id = clean_input($_GET['delete']);

  if ($db->delete('leaves', ['id' => $leave_id])) {
    $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Leave deleted successfully.
    </div>';
    header('Location: leave.php');
    exit;
  }
}

if (isset($_POST['submit_leave'])) {
  $title = clean_input($_POST['title']);
  $body = clean_input($_POST['body']);
  
  $data = array(
    'user_id' => $_SESSION['user_id'],
    'date' => date('Y-m-d'),
    'title' => ucfirst($title),
    'body' => $body,
    'status' => 'Pending'
  );

  if ($db->insert('leaves', $data)) {
    $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Leave submitted successfully.
    </div>';
    header('Location: ' . URL . 'leave.php');
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
    <h1 class="h3 mb-0 text-gray-800">Leave</h1>
    <!-- <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-8">
      <?php if (isset($_SESSION['message'])): ?>
      <?= $_SESSION['message']; ?>
      <?php unset($_SESSION['message']); ?>
      <?php endif; ?>
      <p>Submit new leave:</p>
      <form action="" method="POST" name="leave_form">
        <div class="form-group">
          <label for="title">Leave title:</label>
          <input type="text" name="title" class="form-control">
        </div>

        <div class="form-group">
          <label for="body">Leave body:</label>
          <textarea name="body" id="" cols="30" rows="10" class="form-control"></textarea>
        </div>

        <div class="form-group">
          <input type="submit" class="btn btn-success btn-sm" value="Submit" name="submit_leave">
        </div>
      </form>

      <hr>

      <p>Your previous leave applications:</p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        $leaves = $db->multiple_row("SELECT * FROM leaves WHERE user_id = {$_SESSION['user_id']}");
        if (count($leaves) > 0):
          foreach ($leaves as $leave):
        ?>
          <tr>
            <td><?= date_format(date_create($leave['date']), 'd-m-Y') ?></td>
            <td><?= $leave['title'] ?></td>
            <td><?= $leave['status'] ?></td>
            <td>
              <a href="<?= URL ?>leave.php?delete=<?= $leave['id'] ?>" class="btn btn-danger btn-circle btn-sm delete">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td class="text-center" colspan="3"><i>You have not submitted any leave yet!</i></td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
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