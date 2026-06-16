<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Approving a user changes the pending status to approved so the user can log in.
$conn->query("UPDATE tblUser SET status='approved' WHERE user_id='$id'");

header("Location: dashboard.php");
exit();
?>
