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

if (isset($_POST['submit_assignment'])) {
  $semester = clean_input($_GET['semester']);
  $course_id = clean_input($_GET['course']);
  $student_id = clean_input($_SESSION['user_id']);
  $assignment_id = clean_input($_POST['assignment_id']);

  if (isset($_FILES['assignment_file']['name'])) {
    $file_name = preg_replace('/\s+/', '_', basename($_FILES["assignment_file"]["name"]));
    $file_name = preg_replace('/-+/', '_', $file_name);
    $file_name = time() . '_' . strtolower($file_name);
    $target_dir = "files/assignments/$course_id/student_submissions/";
    $target_file = $target_dir . $file_name;
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }
    if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
      $data = array(
        'assignment_id' => $assignment_id,
        'file' => $file_name,
        'student_id' => $student_id,
        'date_uploaded' => date('Y-m-d'),
        'course_id' => $course_id,
        'semester' => $semester
      );
      if ($db->insert('student_assignment_submissions', $data)) {
        $_SESSION['message2'] = '<div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Success!</strong> Assignment submitted successfully.
        </div>';
        header('Location: ' . URL . 'course.php?' . $_SERVER['QUERY_STRING']);
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
    <h1 class="h3 mb-0 text-gray-800">Course</h1>
    <!-- <a href="<?= ADMIN_URL ?>courses.php?add_new" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus"></i> Add new</a> -->
  </div>

  <?php 
  $semester = 0;$courses = [];
  if (isset($_GET['semester'])) {
    $semester = clean_input($_GET['semester']);

    $courses = $db->multiple_row("SELECT * FROM courses WHERE semester = $semester");
  }
  ?>

  <?php if($semester != 0 && count($courses) == 0): ?>
  <div class="row">
    <div class="col-xl-5 col-lg-6 col-md-8 col-sm-8">
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> No course added in semester <?= $semester ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php 
  $selected_course = 0;
  if (isset($_GET['course'])) {
    $selected_course = clean_input($_GET['course']);
  }
  ?>

  <div class="row">
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-5">
      <form action="" method="GET" name="specific_course_form">
        <div class="form-group">
          <label for="semester">Semester:</label>
          <select name="semester" id="course_semester" class="form-control">
            <option value="">Select semester</option>
            <option value="1" <?= ($semester == 1) ? "selected": false; ?>>1</option>
            <option value="2" <?= ($semester == 2) ? "selected": false; ?>>2</option>
            <option value="3" <?= ($semester == 3) ? "selected": false; ?>>3</option>
            <option value="4" <?= ($semester == 4) ? "selected": false; ?>>4</option>
            <option value="5" <?= ($semester == 5) ? "selected": false; ?>>5</option>
            <option value="6" <?= ($semester == 6) ? "selected": false; ?>>6</option>
            <option value="7" <?= ($semester == 7) ? "selected": false; ?>>7</option>
            <option value="8" <?= ($semester == 8) ? "selected": false; ?>>8</option>
          </select>
        </div>
      </form>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-5">
      <?php if(count($courses) > 0): ?>
      <form action="<?= URL ?>course.php" method="GET" name="specific_course_form2">
        <input type="hidden" name="semester" value="<?= (isset($_GET['semester'])) ? $_GET['semester']: false; ?>">
        <div class="form-group">
          <label for="courses">Courses:</label>
          <select name="course" id="course" class="form-control">
            <option value="">Select course</option>
            <?php foreach($courses as $course): ?>
            <option value="<?= $course['id'] ?>" <?= ($selected_course == $course['id']) ? "selected": false; ?>><?= $course['course_name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <?php if (isset($_SESSION['message2'])): ?>
  <div class="row">
    <div class="col-md-5">
      <?= $_SESSION['message2'] ?>
    </div>
  </div>
  <?php endif; ?><?php unset($_SESSION['message2']); ?>

  <?php if (count($courses) > 0 && isset($_GET['course'])): ?>
  <?php $course_id = clean_input($_GET['course']); ?>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#student_attendance">Student attendance</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#assignment">Assignment</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#download_section">Download section</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content pt-4">
    <div class="tab-pane active" id="student_attendance">
      <p>Attendance record:</p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>Class time</th>
            <th>Present/Absent</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        $query = "SELECT attendance.id, attendance.user_id, attendance.class_id, attendance.day, attendance.present_absent, attendance.date, timetable.time 
        FROM attendance 
        INNER JOIN timetable 
        ON attendance.class_id = timetable.id
        AND attendance.user_id = {$_SESSION['user_id']}
        ORDER BY attendance.date DESC";
        $rows = $db->multiple_row($query);
        ?>
          <?php if (count($rows) > 0): ?>
            <?php foreach($rows as $row): ?>
            <tr>
              <td><?= date_format(date_create($row['date']), 'd-m-Y') ?></td>
              <td><?= $row['time'] ?></td>
              <td>
                <?= ($row['present_absent'] == 1) ? '<span class="text-success">Present</span>'
                : '<span class="text-danger">Absent</span>'; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
          <tr><td colspan="3" class="text-center"><i>No record found</i></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div><!-- ../ #student_attendance -->

    <div class="tab-pane fade" id="assignment">
      <p>Assignments uploaded for this course:
      </p>

      <div class="alert alert-warning">
        <strong>Note:</strong> You can only submit your assignment once!
      </div>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Total marks</th>
          <th>Deadline</th>
          <th>Uploaded date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php 
      $query = "SELECT * FROM assignments WHERE course_id = $selected_course ORDER BY uploaded_date DESC";
      $assignments = $db->multiple_row($query);
      if (count($assignments) > 0):
        foreach ($assignments as $assignment):
          $student_id = $_SESSION['user_id'];
          $a_id = $assignment['id']; // assignment id
          $query = "SELECT * FROM student_assignment_submissions WHERE student_id = $student_id AND assignment_id = $a_id";
          $submitted_assignment = $db->single_row($query);
      ?>
        <tr>
          <td><?= $assignment['total_marks'] ?></td>
          <td>
            <?= date_format(date_create($assignment['deadline_date']), 'd-m-Y') ?> 
            (<?= getTextDay(date('w', strtotime($assignment['deadline_date']))) ?>)
          </td>
          <td><?= date_format(date_create($assignment['uploaded_date']), 'd-m-Y') ?></td>
          <td>
            <?php if (count($submitted_assignment) == 0): ?>
            <a href="#" data-assignment-id="<?= $assignment['id'] ?>" data-course-id="<?= $selected_course ?>" class="btn btn-success btn-circle btn-sm upload_assignment"
            data-toggle="modal" data-target="#submit_assignment_modal">
              <i class="fas fa-upload"></i>
            </a>
            <a href="<?= URL ?>files/assignments/<?= $selected_course . '/' . $assignment['name']; ?>" class="btn btn-primary btn-circle btn-sm">
              <i class="fas fa-download"></i>
            </a>
            <?php else: ?>
              <p>Already submitted!</p>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" class="text-center"><i>No assignment added for this course!</i></td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>

<!-- Upload new assignment in timetable Modal -->
<div class="modal" id="submit_assignment_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Submit assignment:</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <div class="col-6">
          <form action="" method="POST" name="upload_new_assignment_form" enctype="multipart/form-data">
            <div class="form-group">
              <label for="file">Select file:</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile" name="assignment_file">
                <label class="custom-file-label" for="customFile">File</label>
              </div>
            </div>

            <div class="form-group">
              <input type="submit" class="btn btn-success btn-sm" name="submit_assignment" value="Submit">
            </div>
          </form>
        </div>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    </div><!-- ../ assignment -->

    <div class="tab-pane fade" id="download_section">
      <table class="table table-bordered">
        <thead>
          <th>File description</th>
          <th>Uploaded date</th>
          <th>Action</th>
        </thead>
        <tbody>
        <?php 
          $files = $db->multiple_row("SELECT * FROM files WHERE course_id = $course_id ORDER BY id DESC");
          if (count($files) > 0):
            foreach ($files as $file):
        ?>
          <tr>
            <td><?= $file['description'] ?></td>
            <td><?= date_format(date_create($file['uploaded_date']), "d-m-Y"); ?></td>
            <td>
              <a href="<?= URL ?>files/<?= $file['course_id'] . '/' . $file['file'] ?>" class="btn btn-success btn-circle btn-sm">
                <i class="fas fa-download"></i>
              </a>
            </td>
          </tr>
        <?php 
            endforeach;
          else:
        ?>
        <tr>
          <td colspan="3" class="text-center"><i>No file uploaded in this course!</i></td>
        </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require 'includes/footer.php'; ?>