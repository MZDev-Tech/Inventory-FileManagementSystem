<?php
session_start();
include('../connection.php');
if(isset($_SESSION['id'])){
    $userId=$_SESSION['id'];
    $query="UPDATE user SET status='inactive' WHERE id='$userId'";
    mysqli_query($con,$query);
}
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
//remove to token from cookies
setcookie('access_token','',time()-3600,'/');
header('Location: ../user-login.php'); // Redirect to the login page
exit();
?>
