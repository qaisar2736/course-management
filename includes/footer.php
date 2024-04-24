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
<br>
      <!-- Footer -->
      <footer class="sticky-footer bg-white footer fixed-bottom">
        <div class="container my-auto">
          <div class="copyright text-center my-auto pb-4">
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
  <!-- <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a> -->

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

  $('form[name="student_add_course_form"] select[name="semester"]').change(function() {
    var form = $(this).closest('form');
    var value = ($(this).val()).trim();

    if (value.length > 0) {
      $(form).submit();
    }
  });

  $('form[name="student_attendance_form"]').submit(function(e) {
    $('.alert-error').remove();

    var form = $('form[name="student_attendance_form"]');
    var selects = $(form).find('select');
    var input_missing = false;
    var inputs_array = [];
    var stringified = '';

    $.each(selects, function( index, select ) {
      var value = $(select).val();
      if (value == '') {
        input_missing = true;
      }
      inputs_array.push({'id': select.name, 'value': value});
    });
    stringified = JSON.stringify(inputs_array);

    if (input_missing) {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> Submit attendance for all classes.
      </div>`);
      $(form).prepend(message);
      $(message).fadeIn();
    } else {
      $('input[name="object"]').val(stringified);
    }
  });

  $('.upload_assignment').click(function() {
    var form = $('form[name="upload_new_assignment_form"]');
    var assignment_id = $(this).attr('data-assignment-id');
    
    var input = $(`<input type="hidden" name="assignment_id" value="${assignment_id}">`);
    $(form).append(input);
  });

  $('form[name="leave_form"]').submit(function(e) {
    $('.alert-error').remove();
    var form = $(this);
    var title = ($('input[name="title"]').val()).trim();
    var body = ($('textarea[name="body"]').val()).trim();

    if (title == '' || body == '') {
      e.preventDefault();
      var message = $(`<div class="alert alert-danger alert-dismissible alert-error" style="display: none">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong> All fields are required.
      </div>`);
      $(form).prepend(message);
      $(message).fadeIn();
    }
  });

  $('form[name="update_profile_form"]').submit(function(e) {
    $('.alert-error').remove();
    var form = $(this);
    var username = ($("input[name='username']").val()).trim();
    var roll_number = ($("input[name='roll_number']").val()).trim();
    var password = ($("input[name='password']").val()).trim();
    var confirm_password = ($("input[name='confirm_password']").val()).trim();

    if (username == '' || roll_number == '' || password == '' || confirm_password == '') {
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

});
</script>
</body>
</html>