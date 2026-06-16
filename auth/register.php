<?php
include("../config/DBConn.php");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "INSERT INTO tblUser (fname,lname,email,password,status)
            VALUES ('$fname','$lname','$email','$password','pending')";

    if ($conn->query($sql)) {
        $msg = "✅ Registered! Await admin approval.";
    }
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="container">
<div class="card">

<h2>Register</h2>

<form method="POST">
    <label>First Name</label>
    <input type="text" name="fname" required>

    <label>Last Name</label>
    <input type="text" name="lname" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Register</button>

    <p class="success"><?php echo $msg; ?></p>

    <div class="link">
        <a href="login.php">Back to Login</a>
    </div>
</form>

</div>
</div>