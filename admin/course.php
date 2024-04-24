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

if (isset($_POST['upload_file'])) {
  $course_id = clean_input($_POST['course_id']);
  $description = clean_input($_POST['file_description']);
  
  if (isset($_FILES['course_file']['name'])) {
    $file_name = preg_replace('/\s+/', '_', basename($_FILES["course_file"]["name"]));
    $file_name = preg_replace('/-+/', '_', $file_name);
    $file_name = time() . '_' . strtolower($file_name);
    $target_dir = "../files/$course_id/";
    $target_file = $target_dir . $file_name;
    if (!file_exists("../files/$course_id/")) {
      mkdir("../files/$course_id/", 0777, true);
    }
    if (move_uploaded_file($_FILES["course_file"]["tmp_name"], $target_file)) {
      $data = array(
        'course_id' => $course_id,
        'description' => ucfirst($description),
        'file' => $file_name,
        'uploaded_date' => date("Y-m-d"),
        'uploaded_by' => $_SESSION['user_id']
      );

      if ($db->insert('files', $data)) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Success!</strong> File uploaded successfully.
        </div>';
        header('Location: ' . ADMIN_URL . 'course.php?' . $_SERVER['QUERY_STRING']);
        exit;
      }
    }
  }
}

if (isset($_GET['file_delete'])) {
  $id = clean_input($_GET['file_id']);
  $course_id = clean_input($_GET['course']);
  $file = $db->single_row("SELECT * FROM files WHERE id = $id");

  if (count($file) > 0) {
    if ($db->delete('files', ['id' => $id])) { 
      // DELETE FILE FROM SERVER
      $path = "../files/$course_id/{$file['file']}";
      
      if (file_exists($path)) {
        unlink($path);
      }

      unset($_GET['file_delete']);
      unset($_GET['file_id']);

      $_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Success!</strong> File deleted successfully.
      </div>';
      $query_string = '';
      foreach ($_GET as $name => $value) {
        $query_string .= "$name=$value&";
      }
      $query_string = rtrim($query_string, '&');
      header('Location: ' . ADMIN_URL . 'marks.php?' . $query_string);
      exit;
    }
  }
}

if (isset($_GET['attendance_delete'])) {
  $attendance_id = clean_input($_GET['attendance_delete']);

  if ($db->delete('attendance', ['id' => $attendance_id])) {
    unset($_GET['attendance_delete']);
    $_SESSION['message2'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Record deleted successfully!
    </div>';
    $query_string = '';
    foreach ($_GET as $name => $value) {
      $query_string .= "$name=$value&";
    }
    $query_string = rtrim($query_string, '&');
    header('Location: ' . ADMIN_URL . 'course.php?' . $query_string);
    exit;
  }
}

if (isset($_POST['add_class'])) {
  $semester = clean_input($_GET['semester']);
  $day = clean_input($_POST['day']);
  $time = clean_input($_POST['time']);
  $course_id = clean_input($_GET['course']);

  $data = array(
    'course_id' => $course_id,
    'added_by' => $_SESSION['user_id'],
    'day' => $day,
    'time' => $time,
    'semester' => $semester
  );

  if ($db->insert('timetable', $data)) {
    $_SESSION['message2'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> New class added successfully!
    </div>';
    header('Location: ' . ADMIN_URL . 'course.php?' . $_SERVER['QUERY_STRING']);
    exit;
  }
}

if (isset($_POST['upload_new_assignment'])) {
  $total_marks = clean_input($_POST['total_marks']);
  $deadline_date = clean_input($_POST['deadline_date']);
  $course_id = clean_input($_GET['course']);
  $semester = clean_input($_GET['semester']);
  
  if (isset($_FILES['assignment_file']['name'])) {
    $file_name = preg_replace('/\s+/', '_', basename($_FILES["assignment_file"]["name"]));
    $file_name = preg_replace('/-+/', '_', $file_name);
    $file_name = time() . '_' . strtolower($file_name);
    $target_dir = "../files/assignments/$course_id/";
    $target_file = $target_dir . $file_name;
    if (!file_exists("../files/assignments/$course_id/")) {
      mkdir("../files/assignments/$course_id/", 0777, true);
    }
    if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
      $data = array(
        'name' => $file_name,
        'total_marks' => $total_marks,
        'deadline_date' => $deadline_date,
        'uploaded_date' => date('Y-m-d'),
        'uploaded_by' => $_SESSION['user_id'],
        'course_id' => $course_id,
        'semester' => $semester
      );

      if ($db->insert('assignments', $data)) {
        $_SESSION['message2'] = '<div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Success!</strong> Assignment uploaded successfully!
        </div>';
        header('Location: ' . ADMIN_URL . 'course.php?' . $_SERVER['QUERY_STRING']);
        exit;
      }
    }
  }
}

if (isset($_POST['save_assignment_marks'])) {
  $total_marks = clean_input($_POST['total_marks']);
  $assignment_id = clean_input($_POST['assignment_id']);
  $student_id = clean_input($_POST['student_id']);
  $marks_obtained = clean_input($_POST['marks']);
  $course_id = clean_input($_GET['course']);
  $semester = clean_input($_GET['semester']);

  $data = array(
    'student_id' => $student_id,
    'course_id' => $course_id,
    'semester' => $semester,
    'assignment_id' => $assignment_id,
    'total_marks' => $total_marks,
    'marks_obtained' => $marks_obtained,
    'date' => date('Y-m-d')
  );

  if ($db->insert('marks', $data)) {
    $_SESSION['message2'] = '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> Marks entered successfully!
    </div>';
    header('Location: ' . ADMIN_URL . 'course.php?' . $_SERVER['QUERY_STRING']);
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
      <form action="<?= ADMIN_URL ?>course.php" method="GET" name="specific_course_form2">
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
      <a class="nav-link active" data-toggle="tab" href="#course_material">Course material</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#student_attendance">Student attendance</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#course_schedule">Course schedule</a>
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
    <div class="tab-pane active" id="course_material">
      <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-7 col-sm-8">
          <?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
          <?php unset($_SESSION['message']); ?>
          <form action="" method="POST" name="course_file_upload_form" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <div class="form-group">
              <label for="file_description">File description:</label>
              <input type="text" name="file_description" class="form-control">
            </div>

            <div class="form-group">
              <label for="file">Select file:</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="course_file" name="course_file">
                <label class="custom-file-label" for="course_file">Select file upload</label>
              </div>
            </div>

            <div class="form-group">
              <input type="submit" name="upload_file" class="btn btn-success btn-sm" value="Upload file">
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="student_attendance">
      <p>Attendance record:</p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Student</th>
            <th>Date</th>
            <th>Class time</th>
            <th>Present/Absent</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        $query = "SELECT attendance.id, attendance.user_id, attendance.day, attendance.date, attendance.present_absent, timetable.time, users.username
        FROM attendance 
        INNER JOIN users ON attendance.user_id = users.id 
        INNER JOIN timetable ON attendance.class_id = timetable.id
        ORDER BY attendance.date DESC";
        $rows = $db->multiple_row($query);
        ?>
          <?php if (count($rows) > 0): ?>
            <?php foreach($rows as $row): ?>
            <tr>
              <td><?= ucfirst($row['username']) ?></td>
              <td><?= date_format(date_create($row['date']), 'd-m-Y') ?></td>
              <td><?= $row['time'] ?></td>
              <td>
                <div class="form-group">
                  <select name="present_absent" id="present_absent" class="form-control">
                    <option value="1" <?= ($row['present_absent'] == 1) ? 'Selected': ''; ?>>Present</option>
                    <option value="0" <?= ($row['present_absent'] == 0) ? 'Selected': ''; ?>>Absent</option>
                  </select>
                </div>
              </td>
              <td>
              <a data-attendance-id="<?= $row['id'] ?>" href="" class="btn btn-success btn-circle btn-sm update_attendance">
                <i class="fas fa-sync-alt"></i>
              </a>
                <a href="<?= ADMIN_URL ?>course.php?<?= $_SERVER['QUERY_STRING'] . '&attendance_delete=' . $row['id'] ?>" class="btn btn-danger btn-circle btn-sm delete">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
          <tr><td colspan="5" class="text-center"><i>No record found</i></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div><!-- ../ #student_attendance -->

    <div class="tab-pane fade" id="course_schedule">
      <p>Manage classes for this course:
        <a href="#" data-toggle="modal" data-target="#add_new_class_modal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm float-right">
          <i class="fas fa-plus"></i> 
          Add new class
        </a>
      </p>

<!-- Add new class in timetable Modal -->
<div class="modal" id="add_new_class_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add new class:</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <form action="" method="POST" name="add_class_form">

          <div class="form-group">
            <label for="day">Day:</label>
            <select name="day" id="day" class="form-control col-5 col-sm-6">
              <option value="">Select day</option>
              <option value="1">Monday</option>
              <option value="2">Tuesday</option>
              <option value="3">Wednesday</option>
              <option value="4">Thursday</option>
              <option value="5">Friday</option>
            </select>
          </div>

          <div class="form-group">
            <label for="time">Time:</label>
            <input type="time" name="time" class="form-control col-5 col-sm-6">
          </div>

          <div class="form-group">
            <input type="submit" class="btn btn-success btn-sm" name="add_class" value="Add">
          </div>
        </form>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Day</th>
            <th>Time</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php 
          $classes = $db->multiple_row("SELECT * FROM timetable WHERE course_id = $selected_course ORDER BY day ASC");
          if (count($classes) > 0):
            foreach ($classes as $class):
        ?>
          <tr>
            <td><?= getTextDay($class['day']) ?></td>
            <td><?= $class['time'] ?></td>
            <td>
              <a href="" data-timetable-id="<?= $class['id'] ?>" class="btn btn-danger btn-circle btn-sm delete_class">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="text-center"><i>No class added for this course.</i></td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div><!-- ../ #course_schedule -->

    <div class="tab-pane fade" id="assignment">
      <p>Assignments uploaded for this course:
        <a href="#" data-toggle="modal" data-target="#upload_new_assignment_modal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm float-right">
          <i class="fas fa-plus"></i> 
          Upload new
        </a>
      </p>

<!-- Upload new assignment in timetable Modal -->
<div class="modal" id="upload_new_assignment_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Upload new assignment:</h4>
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
              <label for="total_marks">Total marks:</label>
              <input type="number" class="form-control" name="total_marks">
            </div>

            <div class="form-group">
              <label for="deadline">Deadline date:</label>
              <input type="date" name="deadline_date" class="form-control">
            </div>

            <div class="form-group">
              <input type="submit" class="btn btn-success btn-sm" name="upload_new_assignment" value="Upload">
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
      ?>
        <tr>
          <td><?= $assignment['total_marks'] ?></td>
          <td>
            <?= date_format(date_create($assignment['deadline_date']), 'd-m-Y') ?> 
            (<?= getTextDay(date('w', strtotime($assignment['deadline_date']))) ?>)
          </td>
          <td><?= date_format(date_create($assignment['uploaded_date']), 'd-m-Y') ?></td>
          <td>
            <a href="" data-assignment-id="<?= $assignment['id'] ?>" data-course-id="<?= $selected_course ?>" class="btn btn-danger btn-circle btn-sm delete_assignment">
              <i class="fas fa-trash"></i>
            </a>
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

    <?php 
      $query = "SELECT assignments.total_marks, student_assignment_submissions.id, student_assignment_submissions.assignment_id, student_assignment_submissions.file, student_assignment_submissions.student_id, student_assignment_submissions.student_id, student_assignment_submissions.student_id, student_assignment_submissions.date_uploaded, student_assignment_submissions.course_id, student_assignment_submissions.semester,users.username 
      FROM assignments
      INNER JOIN student_assignment_submissions 
      ON student_assignment_submissions.assignment_id = assignments.id
      INNER JOIN users 
      ON student_assignment_submissions.student_id = users.id 
      WHERE users.id = 8
      AND student_assignment_submissions.course_id = $selected_course 
      AND student_assignment_submissions.semester = $semester";
      $submitted_assignments = $db->multiple_row($query);
      if (count($submitted_assignments) > 0):
    ?>
      <p>Assignments submitted by students:</p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Total marks</th>
            <th>Student</th>
            <th>Date uploaded</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($submitted_assignments as $assignment): ?>
          <tr>
            <td><?= $assignment['total_marks'] ?></td>
            <td><?= ucfirst($assignment['username']); ?></td>
            <td><?= date_format(date_create($assignment['date_uploaded']), 'd-m-Y'); ?></td>
            <td>
              <a download="<?= $assignment['file'] ?>" href="<?= URL ?>files/assignments/<?= $assignment['course_id'] . '/student_submissions/' . $assignment['file'] ?>" class="btn btn-primary btn-circle btn-sm">
                <i class="fas fa-file-download"></i>
              </a>
              <a href="" data-toggle="modal" data-target="#enter_assignment_marks_modal" data-submitted-assignment-id="<?= $assignment['id'] ?>" 
              data-assignment-id="<?= $assignment['assignment_id'] ?>"
              data-student-id="<?= $assignment['student_id'] ?>"
              data-semester="<?= $assignment['semester'] ?>"
              data-course-id="<?= $selected_course ?>" 
              data-total-marks="<?= $assignment['total_marks'] ?>"
              class="btn btn-success btn-circle btn-sm enter_assignment_marks">
                <i class="fas fa-check"></i>
              </a>
              <a href="" data-submitted-assignment-id="<?= $assignment['id'] ?>" data-course-id="<?= $selected_course ?>" class="btn btn-danger btn-circle btn-sm delete_submitted_assignment">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

  <!-- Upload new assignment in timetable Modal -->
<div class="modal" id="enter_assignment_marks_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Enter marks for assignment:</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <div class="col-6">
          <form action="" method="POST" name="assignment_marks_form" enctype="multipart/form-data">
            <input type="hidden" name="total_marks" id="assignment_total_marks" value="">
            <input type="hidden" name="assignment_id" id="assignment_id" value="">
            <input type="hidden" name="student_id" id="student_id" value="">

            <div class="form-group">
              <label for="marks">Obtained marks:</label>
              <input type="number" class="form-control" name="marks">
            </div>

            <div class="form-group">
              <input type="submit" class="btn btn-success btn-sm" name="save_assignment_marks" value="Save">
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
              <a href="<?= ADMIN_URL . 'course.php?' . $_SERVER['QUERY_STRING'] . "&file_id={$file['id']}" ?>&file_delete" class="btn btn-danger btn-circle btn-sm delete">
                <i class="fas fa-trash"></i>
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