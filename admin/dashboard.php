<?php
include("../config/DBConn.php");

$result = $conn->query("SELECT * FROM tblUser WHERE status='pending'");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Admin Panel</h3>
    <div>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<h2 style="text-align:center; margin-top:20px;">Pending Users</h2>

<table>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['fname']} {$row['lname']}</td>
        <td>{$row['email']}</td>
        <td>
            <a href='verifyUsers.php?id={$row['user_id']}'>
                <button>Approve</button>
            </a>
        </td>
    </tr>";
}
?>
</table>