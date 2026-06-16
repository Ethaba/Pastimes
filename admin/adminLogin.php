<?php
session_start();
include("../config/DBConn.php");

$error = "";

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = md5($_POST['password']);

    // Admin details are checked against tblAdmin so the login uses database records.
    $result = $conn->query("SELECT * FROM tblAdmin WHERE username='$username' AND password='$password'");

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $result->fetch_assoc();
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid admin login.";
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
