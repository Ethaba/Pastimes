<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
}

$user = $_SESSION['user'];
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">
    <h1>Welcome <?php echo $user['fname']; ?> 👋</h1>
    <p>You are successfully logged in.</p>
</div>
<a href="products.php">
    <button style="width:200px; margin-top:20px;">Shop Clothes</button>
</a>