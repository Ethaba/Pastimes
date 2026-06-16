<?php
session_start();
include("../config/DBConn.php");

$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Each value is escaped before it is saved so apostrophes do not break the SQL statement.
    $fname = mysqli_real_escape_string($conn, trim($_POST['fname']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['lname']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $passwordText = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (strlen($passwordText) != 8) {
        $error = "Password must be exactly 8 characters.";
    } elseif ($passwordText != $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $password = md5($passwordText);
        $checkUser = $conn->query("SELECT * FROM tblUser WHERE username='$username' OR email='$email'");

        if ($checkUser->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $sql = "INSERT INTO tblUser (fname,lname,username,email,password,address,status)
                    VALUES ('$fname','$lname','$username','$email','$password','$address','pending')";

            if ($conn->query($sql)) {
                $msg = "Registered successfully. Please wait for admin approval.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="container">
<div class="card">

<h2>Register</h2>

<form method="POST">
    <label>First Name</label>
    <input type="text" name="fname" required value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : ''; ?>">

    <label>Last Name</label>
    <input type="text" name="lname" required value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : ''; ?>">

    <label>Username</label>
    <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

    <label>Email</label>
    <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

    <label>Delivery Address</label>
    <input type="text" name="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">

    <label>Password - exactly 8 characters</label>
    <input type="password" name="password" minlength="8" maxlength="8" required>

    <label>Confirm Password</label>
    <input type="password" name="confirm_password" minlength="8" maxlength="8" required>

    <button type="submit">Register</button>

    <p class="success"><?php echo $msg; ?></p>
    <p class="error"><?php echo $error; ?></p>

    <div class="link">
        <a href="login.php">Back to Login</a>
    </div>
</form>

</div>
</div>
