<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Default action = approve (keeps your current system working)
$action = isset($_GET['action']) ? $_GET['action'] : 'approve';

if ($action == "approve") {

    $conn->query("
        UPDATE tblUser 
        SET status='approved' 
        WHERE user_id='$id'
    ");

} elseif ($action == "reject") {

    $conn->query("
        UPDATE tblUser 
        SET status='rejected' 
        WHERE user_id='$id'
    ");
}

// return back to dashboard after action
header("Location: dashboard.php");
exit();
?>