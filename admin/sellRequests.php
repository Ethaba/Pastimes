<?php
session_start();
include("../config/DBConn.php");
include("includes/adminLayout.php");

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

$updateMessage = "";
$updatedRequestId = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['request_id']);
    $status = mysqli_real_escape_string($conn, $_POST['request_status']);
    $note = mysqli_real_escape_string($conn, trim($_POST['admin_note']));

    $conn->query("UPDATE tblSellRequest SET request_status='$status', admin_note='$note' WHERE request_id='$id'");

    if ($status == "approved") {
        $row = $conn->query("SELECT * FROM tblSellRequest WHERE request_id='$id'")->fetch_assoc();
        if ($row) {
            $conn->query("INSERT INTO tblClothes (name, brand, description, price, size, image, quantity, status) VALUES ('{$row['clothing_name']}', '{$row['brand']}', '{$row['description']}', 0, 'M', '{$row['image']}', 1, 'available')");
        }
    }

    $updateMessage = "Update sent successfully.";
    $updatedRequestId = $id;
}

$requests = $conn->query("SELECT tblSellRequest.*, tblUser.fname, tblUser.lname, tblUser.email FROM tblSellRequest INNER JOIN tblUser ON tblSellRequest.user_id = tblUser.user_id ORDER BY request_date DESC");
?>

<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1>Seller Requests</h1>
            <p>Review clothing items submitted by users and respond with approval or rejection directly from the dashboard.</p>
        </div>
    </div>

    <?php if ($updateMessage) { ?>
        <div class="message-bar"><?php echo htmlspecialchars($updateMessage); ?></div>
    <?php } ?>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Active Requests</h2>
                <p class="info-text"><?php echo $requests->num_rows; ?> requests loaded.</p>
            </div>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Item</th>
                    <th>Brand</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Admin Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $requests->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></strong>
                            <br>
                            <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                        </td>
                        <td><?php echo htmlspecialchars($row['clothing_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <span class="status <?php echo htmlspecialchars($row['request_status']); ?>">
                                <?php echo htmlspecialchars($row['request_status']); ?>
                            </span>
                        </td>
                        <td>
                            <form class="request-form" method="POST">
                                <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                <select name="request_status">
                                    <option value="pending" <?php echo $row['request_status'] === 'pending' ? 'selected' : ''; ?>>pending</option>
                                    <option value="approved" <?php echo $row['request_status'] === 'approved' ? 'selected' : ''; ?>>approved</option>
                                    <option value="rejected" <?php echo $row['request_status'] === 'rejected' ? 'selected' : ''; ?>>rejected</option>
                                </select>
                                <textarea name="admin_note" placeholder="Message to seller"><?php echo $updatedRequestId !== null && $updatedRequestId == $row['request_id'] ? '' : htmlspecialchars($row['admin_note']); ?></textarea>
                                <button class="btn small btn-edit" type="submit">Send Update</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/adminFooter.php"); ?>