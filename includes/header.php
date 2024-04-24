<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Student - Content Management System</title>
  <!-- Custom fonts for this template-->
  <link href="<?= URL ?>assets/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link rel="icon" href="<?= URL ?>assets/images/icons/admin.png">
  <style>
  /* latin */
  @font-face {
    font-family: 'Nunito';
    font-style: normal;
    font-weight: 700;
    src: local('Nunito Bold'), local('Nunito-Bold'), url(<?= URL ?>assets/fonts/XRXW3I6Li01BKofAjsOUYevI.woff2) format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /* latin */
  @font-face {
    font-family: 'Nunito';
    font-style: normal;
    font-weight: 800;
    src: local('Nunito ExtraBold'), local('Nunito-ExtraBold'), url(<?= URL ?>assets/fonts/XRXW3I6Li01BKofAksCUYevI.woff2) format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  body {
    min-height:100vh;
  }
  </style>
  <!-- Custom styles for this template-->
  <link href="<?= URL ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <a class="navbar-brand" href="<?= URL ?>"><img src="<?= URL ?>assets/images/vu.png"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarColor03">
        <ul class="navbar-nav mr-auto">
          <!-- Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
              Courses
            </a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="<?= URL ?>courses.php">My courses</a>
              <a class="dropdown-item" href="<?= URL ?>course.php">Course area</a>
            </div>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="<?= URL ?>attendance.php">Attendance <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= URL ?>timetable.php">Timetable</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= URL ?>marks.php">Marks</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= URL ?>leave.php">Leave</a>
          </li>
          <div class="nav-item">
            <a class="nav-link" href="<?= URL ?>profile.php">Profile</a>
          </div>
        </ul>

        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= URL ?>logout.php">Logout</a>
          </li>
        </ul>
        <!-- <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search">
          <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
        </form> -->
      </div>
    </div>
  </nav>

<div class="container pt-4">
