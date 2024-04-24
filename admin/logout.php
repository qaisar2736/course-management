<?php 

require 'includes/config.php';

session_unset();
session_destroy();

header('Location: '. ADMIN_URL . 'login.php');
exit;

?>