<?php
session_start();
include("../config/DBConn.php");
include("includes/adminLayout.php");

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

<div class="page-wrapper">

    <div class="page-header">
        <h1>Manage Users</h1>
        <p>View, edit, approve or remove system users.</p>
    </div>

    <p class="success"><?php echo $message ?? ''; ?></p>

    <?php if (isset($editUser)) { ?>
        <div class="card wide-card">
            <h2>Edit User</h2>

            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $editUser['user_id']; ?>">

                <label>First Name</label>
                <input type="text" name="fname" value="<?php echo htmlspecialchars($editUser['fname']); ?>">

                <label>Last Name</label>
                <input type="text" name="lname" value="<?php echo htmlspecialchars($editUser['lname']); ?>">

                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($editUser['username']); ?>">

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($editUser['email']); ?>">

                <label>Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($editUser['address']); ?>">

                <label>Status</label>
                <select name="status">
                    <option value="pending" <?php if($editUser['status']=="pending") echo "selected"; ?>>pending</option>
                    <option value="approved" <?php if($editUser['status']=="approved") echo "selected"; ?>>approved</option>
                    <option value="rejected" <?php if($editUser['status']=="rejected") echo "selected"; ?>>rejected</option>
                </select>

                <button type="submit">Update User</button>
            </form>
        </div>
    <?php } ?>

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
                <td><?php echo $row['fname']." ".$row['lname']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>

                <td>
                    <?php
                        if ($row['status']=="approved") {
                            echo "<span class='badge approved'>Approved</span>";
                        } elseif ($row['status']=="rejected") {
                            echo "<span class='badge rejected'>Rejected</span>";
                        } else {
                            echo "<span class='badge pending'>Pending</span>";
                        }
                    ?>
                </td>

                <td>
                    <a class="btn small btn-edit"
                       href="manageUsers.php?edit=<?php echo $row['user_id']; ?>">
                       Edit
                    </a>

                    <a class="btn small btn-delete"
                       href="manageUsers.php?delete=<?php echo $row['user_id']; ?>"
                       onclick="return confirm('Delete this user?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>

</div>

<?php include("includes/adminFooter.php"); ?>
