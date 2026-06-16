<?php
session_start();
include("../config/DBConn.php");

$email = "";
$username = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = md5($_POST['password']);

    // The user must match both the username and email address entered on the form.
    $sql = "SELECT * FROM tblUser WHERE username='$username' AND email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['password'] == $password) {
            if ($user['status'] == "approved") {
                $_SESSION['user'] = $user;
                header("Location: ../user/dashboard.php");
                exit();
            } else {
                $error = "Waiting for admin approval.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="container">
<div class="card">

<h2>Login</h2>

<form method="POST">
    <label>Username</label>
    <input type="text" name="username" required value="<?php echo htmlspecialchars($username); ?>">

    <label>Email</label>
    <input type="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>

    <p class="error"><?php echo $error; ?></p>

    <div class="link">
        <a href="register.php">Create Account</a>
    </div>
</form>

</div>
</div>
