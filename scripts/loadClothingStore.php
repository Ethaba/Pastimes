<?php
include("../config/DBConn.php");

// This script loads the exported SQL file and rebuilds the ClothingStore tables.
$sql = file_get_contents("myClothingStore.sql");

if ($conn->multi_query($sql)) {
    echo "Database Loaded Successfully";
} else {
    echo "Database Load Failed: " . $conn->error;
}
?>
