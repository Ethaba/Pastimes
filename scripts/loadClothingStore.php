<?php
include("../config/DBConn.php");

$sql = file_get_contents("myClothingStore.sql");

if ($conn->multi_query($sql)) {
    echo "Database Loaded Successfully";
}
?>