<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['username'] == "admin" && $_POST['password'] == "admin123") {

        $_SESSION['admin'] = true;
        header("Location: dashboard.php");

    } else {
        $error = "Invalid admin login";
    }
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="container">
<div class="card">

<h2>Admin Login</h2>

<form method="POST">
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>

    <p class="error"><?php echo $error; ?></p>
</form>

</div>
</div>