
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Course Management System <?= date("Y"); ?></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="<?= URL ?>logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="<?= URL ?>assets/js/jquery-3.4.1.min.js"></script>
  <script src="<?= URL ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?= URL ?>assets/js/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?= URL ?>assets/js/sb-admin-2.min.js"></script>

  <script src="<?= URL ?>assets/js/sweet-alert.min.js"></script>

  <!-- Page level plugins -->
  <!-- <script src="<?= URL ?>assets/js/Chart.min.js"></script> -->

  <!-- Page level custom scripts -->
  <!-- <script src="js/demo/chart-area-demo.js"></script> -->
  <!-- <script src="js/demo/chart-pie-demo.js"></script> -->

<script>
$(document).ready(function(){
  $('form[name="add_course_form"], form[name="update_course_form"]').submit(function(e) {
    $('.alert-error').remove();

    var course_name = ($('input[name="course_name"]').val()).trim();
    var semester_number = $('#semester').find(':selected').val();

    if (course_name == '' || semester_number == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Both fields are required.
      </div>`);
      var form = $('form[name="add_course_form"]').length > 0 ? $('form[name="add_course_form"]') : $('form[name="update_course_form"]');
      $(form).prepend(message);
      $(message).fadeIn();
    } 
  });

  $('.delete').click(function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      var response = (typeof result.value !== 'undefined') ? result.value : result.dismiss;
      if (response === true) {
        window.location.href = url;
      }
    });
  });

  $('select#course_semester').change(function() {
    var form = $(this).closest('form');
    var value = ($(this).val()).trim();

    if (!value.length == 0) {
      $(form).submit();
    }
  });

  $('form[name="specific_course_form2"] select[name="course"]').change(function() {
    var form = $(this).closest('form');
    var value = ($(this).val()).trim();

    if (!value.length == 0) {
      $(form).submit();
    }
  });

  $('form[name="course_file_upload_form"]').submit(function(e) {
    $('.alert-error').remove();
    var description = ($('input[name="file_description"]').val()).trim();
    var file = ($('#course_file').val()).trim();

    if (description == '' || file == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Both fields are required.
      </div>`);
      $('form[name="course_file_upload_form"]').prepend(message);
      $(message).fadeIn();
    }
  });

  $('form[name="timetable_day_form"] select[name="day"]').change(function() {
    var form = $(this).closest('form');
    var value = ($(this).val()).trim();

    if (value.length > 0) {
      $(form).submit();
    }
  });

  // TIMETABLE ADD COURSE FORM
  $('form[name="tt_add_course_form"] select[name="semester"]').change(function() {
    var form = $(this).closest('form');
    var semester = ($(this).val()).trim();
    var select = $(form).find('select[name="course"]');

    if (semester.length > 0) {
      $.ajax({
        url: '<?= ADMIN_URL ?>ajax.php',
        method: 'GET',
        data: {semester: semester, get_semester_courses: true},
        success: function(response) {
          $('.alert-error').remove();
          var options = `<option value=''>Select course</option>`;
          if (typeof response == 'object') {
            if (response.length == 0) {
              var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error!</strong> No course added in this semester.
              </div>`);
              $(form).prepend(message);
              $(message).fadeIn();
            } else {
              response.forEach(function(item, index) {
                options += `<option value="${item.id}">${item.course_name}</option>`;
              });
              $(select).html(options);
            }
          }
        },
        error: function(error) {
          console.log(error);
        }
      });
    }
  });

  $('form[name="tt_add_course_form"]').submit(function(e) {
    $('.alert-error').remove();
    var semester = ($('select[name="semester"]').val()).trim();
    var course = ($('select[name="course"]').val()).trim();
    var course_time = ($('#course_time').val()).trim();

    if (semester == '' || course == '' || course_time == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> All fields are required.
      </div>`);
      $('form[name="tt_add_course_form"]').prepend(message);
      $(message).fadeIn();
    }
  });

  $('.update_attendance').click(function(e) {
    e.preventDefault();
    var select_el = $(this).parent().prev().find('select[name="present_absent"]');
    var present_absent_value = $(select_el).val();
    var attendance_id = $(this).attr('data-attendance-id');

    $.ajax({
      url: '<?= ADMIN_URL ?>ajax.php',
      method: 'POST',
      data: {update_attendance: attendance_id, new_value: present_absent_value},
      success: function(response) {
        $('#student_attendance').find('.alert-success').remove();
        if (typeof response['updated'] != 'undefined') {
          var message = $(`<div class="alert alert-success alert-dismissible" style="display: none">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> Record updated successfully.
          </div>`);
          $('#student_attendance').prepend(message);
          $(message).fadeIn();
        }
      },
      error: function(error) {
        console.log(error);
      }
    });
  });

  $('.delete_class').click(function(e) {
    e.preventDefault();
    var timetable_id = $(this).attr('data-timetable-id');
    var tr = $(this).closest('tr');
    var tbody = $(tr).closest('tbody');
    
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      var response = (typeof result.value !== 'undefined') ? result.value : result.dismiss;
      if (response === true) {
        $.ajax({
          url: '<?= ADMIN_URL ?>ajax.php',
          method: 'POST',
          data: {delete_class: timetable_id},
          success: function(response) {
            $('#course_schedule').find('.alert-success').remove();
            $(tr).remove();
            if ($(tbody).find('tr').length == 0) {
              $(tbody).html(`<tr><td colspan="3" class="text-center"><i>No class added for this course.</i></td></tr>`);
            }
            if (typeof response['deleted'] != 'undefined') {
              var message = $(`<div class="alert alert-success alert-dismissible" style="display: none">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Success!</strong> Record deleted successfully.
              </div>`);
              $('#course_schedule').prepend(message);
              $(message).fadeIn();
            }
          },
          error: function(error) {
            console.log(error);
          }
        });
      }
    });
  });

  $('form[name="add_class_form"]').submit(function(e) {
    $('.alert-error').remove();
    var day = $('select[name="day"]').val();
    var time = $('input[name="time"]').val();

    if (day == '' || time == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> All fields are required.
      </div>`);
      $('form[name="add_class_form"]').prepend(message);
      $(message).fadeIn();
    }
  });

  $('form[name="upload_new_assignment_form"]').submit(function(e) {
    $('.alert-error').remove();
    
    var assignment = ($('input[name="assignment"]').val());
    var total_marks = ($('input[name="total_marks"]').val()).trim();
    var deadline_date = ($('input[name="deadline_date"]').val()).trim();

    if (assignment == '' || total_marks == '' || deadline_date == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> All fields are required.
      </div>`);
      $('form[name="upload_new_assignment_form"]').prepend(message);
      $(message).fadeIn();
    }
  });

  $('.delete_assignment').click(function(e) {
    e.preventDefault();
    var assignment_id = ($(this).attr('data-assignment-id')).trim();
    var course_id = ($(this).attr('data-course-id')).trim();
    var tr = $(this).closest('tr');
    var tbody = $(this).closest('tbody');

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      var response = (typeof result.value !== 'undefined') ? result.value : result.dismiss;
      if (response === true) {
        $.ajax({
          url: '<?= ADMIN_URL ?>ajax.php',
          method: 'POST',
          data: {delete_assignment: assignment_id, course_id: course_id},
          success: function(response) {
            if (typeof response['deleted'] != 'undefined') {
              $('#assignment').find('.alert-success').remove();
                $(tr).remove();
                if ($(tbody).find('tr').length == 0) {
                  $(tbody).html(`<tr><td colspan="4" class="text-center"><i>No assignment added for this course.</i></td></tr>`);
                }
                if (typeof response['deleted'] != 'undefined') {
                  var message = $(`<div class="alert alert-success alert-dismissible" style="display: none">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> Assignment deleted successfully.
                  </div>`);
                  $('#assignment').prepend(message);
                  $(message).fadeIn();
                }
            }
          },
          error: function(error) {
            console.log(error);
          }
        });
      }
    });
  });

  $('.delete_submitted_assignment').click(function(e) {
    e.preventDefault();
    var submitted_assignment_id = ($(this).attr('data-submitted-assignment-id')).trim();
    var course_id = ($(this).attr('data-course-id')).trim();
    var tr = $(this).closest('tr');
    var tbody = $(this).closest('tbody');

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      var response = (typeof result.value !== 'undefined') ? result.value : result.dismiss;
      if (response === true) {
        $.ajax({
          url: '<?= ADMIN_URL ?>ajax.php',
          method: 'POST',
          data: {delete_submitted_assignment: submitted_assignment_id, course_id: course_id},
          success: function(response) {
            if (typeof response['deleted'] != 'undefined') {
              $('#assignment').find('.alert-success').remove();
                $(tr).remove();
                if ($(tbody).find('tr').length == 0) {
                  $(tbody).html(`<tr><td colspan="4" class="text-center"><i>No assignment submitted for this course.</i></td></tr>`);
                }
                if (typeof response['deleted'] != 'undefined') {
                  var message = $(`<div class="alert alert-success alert-dismissible" style="display: none">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> Student assignment deleted successfully.
                  </div>`);
                  $('#assignment').prepend(message);
                  $(message).fadeIn();
                }
            }
          },
          error: function(error) {
            console.log(error);
          }
        });
      }
    });
  });

  $('.enter_assignment_marks').click(function(e) {
    e.preventDefault();
    var total_marks = $(this).attr('data-total-marks');
    var assignment_id = $(this).attr('data-assignment-id');
    var student_id = $(this).attr('data-student-id');
    
    $('form[name="assignment_marks_form"]').find('input[name="total_marks"]').val(total_marks);
    $('form[name="assignment_marks_form"]').find('input[name="assignment_id"]').val(assignment_id);
    $('form[name="assignment_marks_form"]').find('input[name="student_id"]').val(student_id);
  });

  $('form[name="assignment_marks_form"]').submit(function(e) {
    var form = $(this);
    var marks = parseInt($(form).find('input[name="marks"]').val());
    var total_marks = parseInt($(form).find('input[name="total_marks"]').val());
    $(form).find('.alert-danger').remove();
    
    if (isNaN(marks)) {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Enter marks.
      </div>`);
      $(form).parent().before(message);
      $(message).fadeIn();
    } else if (marks > total_marks) {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Obtained marks should not be more then total marks.
      </div>`);
      $(form).parent().before(message);
      $(message).fadeIn();
    }
  });

  $('.display-leave').click(function() {
    var leave_id = $(this).attr('data-leave-id');

    $.ajax({
      url: '<?= ADMIN_URL ?>ajax.php',
      method: 'GET',
      data: {get_leave_details: leave_id},
      success: function(response) {
        if (typeof response['body'] != 'undefined') {
          $('input[name="leave_id"]').val(response['id']);
          $('#leave_title').text(response['title']);
          $('#leave_body').text(response['body']);
        }
      },
      error: function(error) {
        console.log(error);
      }
    });
  });

  $('form[name="add_students_form"] select[name="semester"]').change(function() {
    var form = $(this).closest('form');
    var semester = ($(this).val()).trim();
    var select = $(form).find('select[name="course"]');

    if (semester.length > 0) {
      $.ajax({
        url: '<?= ADMIN_URL ?>ajax.php',
        method: 'GET',
        data: {semester: semester, get_semester_courses: true},
        success: function(response) {
          $('.alert-error').remove();
          var options = `<option value=''>Select course</option>`;
          if (typeof response == 'object') {
            if (response.length == 0) {
              var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error!</strong> No course added in this semester.
              </div>`);
              $(form).prepend(message);
              $(message).fadeIn();
            } else {
              response.forEach(function(item, index) {
                options += `<option value="${item.id}">${item.course_name}</option>`;
              });
              $(select).html(options);
            }
          }
        },
        error: function(error) {
          console.log(error);
        }
      });
    }
  });

  $('form[name="add_students_form"]').submit(function(e) {
    $('.alert-error').remove();
    var form = $(this);
    var username = ($('input[name="username"]').val()).trim();
    var password = ($('input[name="password"]').val()).trim();
    var confirm_password = ($('input[name="confirm_password"]').val()).trim();
    var email = ($('input[name="email"]').val()).trim();
    var semester = ($('select[name="semester"]').val()).trim();
    var course = ($('select[name="course"]').val()).trim();

    if (username == '' || password == '' || confirm_password == '' || email == '' || semester == '' || course == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> All fields are required.
      </div>`);
      $(form).prepend(message);
      $(message).fadeIn();
    } else if (password != confirm_password) {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Confirm password must be same.
      </div>`);
      $(form).prepend(message);
      $(message).fadeIn();
    }
  });

  $('form[name="add_marks_form"]').submit(function(e) {
    $('.alert-error').remove();
    var form = $(this);
    var student = ($('select[name="student"]').val());
    var total = parseInt($('input[name="total"]').val());
    var marks_obtained = parseInt($('input[name="marks_obtaind"]').val());
    var exam_type = ($('select[name="exam_type"]').val()).trim();

    if (student == '' || total == '' || marks_obtained == '' || exam_type == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> All fields are required!
      </div>`);
      $(form).prepend(message);
      $(message).fadeIn();
    } else if (marks_obtained > total) {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Obtained marks should be more then total marks.
      </div>`);
      $(form).prepend(message);
      $(message).fadeIn();
    }
  });
});
</script>
</body>
</html>