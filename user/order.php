<?php
session_start();
include("../config/DBConn.php");

$user_id = $_SESSION['user']['user_id'];
$cloth_id = $_GET['id'];

$conn->query("INSERT INTO tblAorder (user_id, cloth_id)
VALUES ('$user_id','$cloth_id')");

echo "Order placed successfully!";
?>