<?php
include("../config/DBConn.php");

$id = $_GET['id'];

$conn->query("UPDATE tblUser SET status='approved' WHERE user_id=$id");

header("Location: dashboard.php");
?>