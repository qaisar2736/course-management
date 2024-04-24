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

if (isset($_POST['approve_leave'])) {
  $action = clean_input($_POST['approve_leave']);
  $id = clean_input($_POST['leave_id']);

  if ($action == 'Approve') {
    if ($db->update('leaves', ['status' => 'Approved'], ['id' => $id])) {
      $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success!</strong> Leave status updated successfully.
      </div>';
      header('Location: ' . ADMIN_URL . 'leave.php');
      exit;
    }
  } else if ($action == 'Disapprove') {
    if ($db->update('leaves', ['status' => 'Rejected'], ['id' => $id])) {
      $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success!</strong> Leave status updated successfully.
      </div>';
      header('Location: ' . ADMIN_URL . 'leave.php');
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
    <h1 class="h3 mb-0 text-gray-800">Leaves</h1>
    <!-- <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>

  <div class="row">
    <div class="col-md-8">
      <?php if (isset($_SESSION['message'])): ?>
      <?= $_SESSION['message']; ?>
      <?php unset($_SESSION['message']); ?>
      <?php endif; ?>
      <p>Leaves submitted by students:</p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Student</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php 
          $query = "SELECT leaves.*, users.username
          FROM leaves 
          INNER JOIN users 
          ON leaves.user_id = users.id";
          $leaves = $db->multiple_row($query);
          if (count($leaves) > 0):
            foreach ($leaves as $leave):
        ?>
          <tr>
            <td><?= date_format(date_create($leave['date']), 'd-m-Y') ?></td>
            <td><?= $leave['title']; ?></td>
            <td><?= ucfirst($leave['username']); ?></td>
            <td><?= $leave['status'] ?></td>
            <td>
              <a href="#" data-leave-id="<?= $leave['id'] ?>" data-toggle="modal" data-target="#apr_dapr_leave_modal" class="btn btn-primary btn-circle btn-sm display-leave">
                <i class="fas fa-eye"></i>
              </a>
            </td>
          </tr>
            <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="3" class="text-center"><i>No record found</i></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>

<!-- The Modal -->
<div class="modal" id="apr_dapr_leave_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Leave</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <h4>Title:</h4>
        <p id="leave_title"></p>
        <h4>Description:</h4>
        <p id="leave_body"></p>
        <form action="" name="approve_disapprove_leave" method="POST">
          <input type="hidden" name="leave_id" value="">
          <input type="submit" name="approve_leave" class="btn btn-success btn-sm" value="Approve">
          <input type="submit" name="approve_leave" class="btn btn-warning btn-sm" value="Disapprove">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    </div>
  </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>