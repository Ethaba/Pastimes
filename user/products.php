<?php
session_start();
include("../config/DBConn.php");

$result = $conn->query("SELECT * FROM tblClothes");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="products">

<?php
while ($row = $result->fetch_assoc()) {
    echo "<div class='product'>
        <h4>{$row['name']}</h4>
        <p>R{$row['price']}</p>
        <p>Size: {$row['size']}</p>
        <a href='order.php?id={$row['cloth_id']}'>
            <button>Buy</button>
        </a>
    </div>";
}
?>

</div>