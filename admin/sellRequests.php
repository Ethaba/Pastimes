<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['request_id']);
    $status = mysqli_real_escape_string($conn, $_POST['request_status']);
    $note = mysqli_real_escape_string($conn, trim($_POST['admin_note']));

    // The admin note lets the seller know whether the request is approved, rejected or needs more details.
    $conn->query("UPDATE tblSellRequest
        SET request_status='$status', admin_note='$note'
        WHERE request_id='$id'");
}

$requests = $conn->query("SELECT tblSellRequest.*, tblUser.fname, tblUser.lname, tblUser.email
    FROM tblSellRequest
    INNER JOIN tblUser ON tblSellRequest.user_id = tblUser.user_id
    ORDER BY request_date DESC");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Admin Panel</h3>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manageClothes.php">Clothes</a>
        <a href="messages.php">Messages</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="page-header">
    <h1>Seller Requests</h1>
    <p>Review clothing that users want to sell through Pastimes.</p>
</div>

<table>
    <tr>
        <th>Seller</th>
        <th>Item</th>
        <th>Brand</th>
        <th>Description</th>
        <th>Status</th>
        <th>Admin Reply</th>
    </tr>

    <?php while ($row = $requests->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?><br><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['clothing_name']); ?></td>
            <td><?php echo htmlspecialchars($row['brand']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['request_status']); ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                    <select name="request_status">
                        <option value="pending">pending</option>
                        <option value="approved">approved</option>
                        <option value="rejected">rejected</option>
                    </select>
                    <textarea name="admin_note" placeholder="Message to seller"><?php echo htmlspecialchars($row['admin_note']); ?></textarea>
                    <button type="submit">Send Update</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
