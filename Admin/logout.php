<?php
session_name("ADMIN_SESSION");
session_start();
include('../connection.php');
$id = $_SESSION['id'];
$role = 'admin';
//set admin status to inactive when he logout 
$query = "UPDATE user SET status='inactive' WHERE id='$id' && role='$role'";
mysqli_query($con, $query);

session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

//remove to token from cookies
setcookie('access_token', '', time() - 3600, '/');
header('Location: ../admin-login.php');
exit();
