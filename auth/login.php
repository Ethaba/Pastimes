<?php
session_start();
include("../config/DBConn.php");

$email = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM tblUser WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if ($user['password'] == $password) {

            if ($user['status'] == "approved") {

                $_SESSION['user'] = $user;
                header("Location: ../user/dashboard.php");

            } else {
                $error = "⏳ Waiting for admin approval.";
            }

        } else {
            $error = "❌ Incorrect password";
        }

    } else {
        $error = "❌ User not found";
    }
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="container">
<div class="card">

<h2>Login</h2>

<form method="POST">
    <label>Email</label>
    <input type="email" name="email" required value="<?php echo $email; ?>">

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