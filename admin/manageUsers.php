<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

$message = "";
$editUser = null;

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $conn->query("DELETE FROM tblUser WHERE user_id='$id'");
    $message = "User deleted.";
}

if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $editResult = $conn->query("SELECT * FROM tblUser WHERE user_id='$id'");
    $editUser = $editResult->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $fname = mysqli_real_escape_string($conn, trim($_POST['fname']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['lname']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $status = mysqli_real_escape_string($conn, trim($_POST['status']));

    $conn->query("UPDATE tblUser SET
        fname='$fname',
        lname='$lname',
        username='$username',
        email='$email',
        address='$address',
        status='$status'
        WHERE user_id='$id'");

    $message = "User updated.";
    $editUser = null;
}

$users = $conn->query("SELECT * FROM tblUser ORDER BY user_id DESC");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Admin Panel</h3>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manageClothes.php">Clothes</a>
        <a href="sellRequests.php">Seller Requests</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<?php if ($editUser) { ?>
<div class="container">
<div class="card wide-card">
    <h2>Edit User</h2>
    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $editUser['user_id']; ?>">

        <label>First Name</label>
        <input type="text" name="fname" required value="<?php echo htmlspecialchars($editUser['fname']); ?>">

        <label>Last Name</label>
        <input type="text" name="lname" required value="<?php echo htmlspecialchars($editUser['lname']); ?>">

        <label>Username</label>
        <input type="text" name="username" required value="<?php echo htmlspecialchars($editUser['username']); ?>">

        <label>Email</label>
        <input type="email" name="email" required value="<?php echo htmlspecialchars($editUser['email']); ?>">

        <label>Address</label>
        <input type="text" name="address" required value="<?php echo htmlspecialchars($editUser['address']); ?>">

        <label>Status</label>
        <select name="status">
            <option value="pending" <?php echo $editUser['status'] == 'pending' ? 'selected' : ''; ?>>pending</option>
            <option value="approved" <?php echo $editUser['status'] == 'approved' ? 'selected' : ''; ?>>approved</option>
        </select>

        <button type="submit">Update User</button>
    </form>
</div>
</div>
<?php } ?>

<h2 class="section-title">Manage Users</h2>
<p class="success"><?php echo $message; ?></p>

<table>
    <tr>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $users->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <a href="manageUsers.php?edit=<?php echo $row['user_id']; ?>">Edit</a>
                |
                <a href="manageUsers.php?delete=<?php echo $row['user_id']; ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>
