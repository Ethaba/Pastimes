<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

$notice = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = mysqli_real_escape_string($conn, $_POST['user_id']);
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $body = mysqli_real_escape_string($conn, trim($_POST['message']));
    $adminId = $_SESSION['admin']['admin_id'];

    $conn->query("INSERT INTO tblMessage (user_id, admin_id, subject, message, sender, message_date)
        VALUES ('$userId', '$adminId', '$subject', '$body', 'admin', NOW())");

    $notice = "Message sent.";
}

$users = $conn->query("SELECT * FROM tblUser WHERE status='approved' ORDER BY fname");
$messages = $conn->query("SELECT tblMessage.*, tblUser.fname, tblUser.lname
    FROM tblMessage
    INNER JOIN tblUser ON tblMessage.user_id = tblUser.user_id
    ORDER BY message_date DESC");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Admin Panel</h3>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manageUsers.php">Users</a>
        <a href="sellRequests.php">Seller Requests</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="container">
<div class="card wide-card">
    <h2>Message Buyer or Seller</h2>
    <p class="success"><?php echo $notice; ?></p>

    <form method="POST">
        <label>User</label>
        <select name="user_id" required>
            <?php while ($user = $users->fetch_assoc()) { ?>
                <option value="<?php echo $user['user_id']; ?>">
                    <?php echo htmlspecialchars($user['fname'] . " " . $user['lname']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Subject</label>
        <input type="text" name="subject" required>

        <label>Message</label>
        <textarea name="message" required></textarea>

        <button type="submit">Send Message</button>
    </form>
</div>
</div>

<h2 class="section-title">Message History</h2>
<table>
    <tr>
        <th>Date</th>
        <th>User</th>
        <th>From</th>
        <th>Subject</th>
        <th>Message</th>
    </tr>

    <?php while ($row = $messages->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['message_date']); ?></td>
            <td><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></td>
            <td><?php echo htmlspecialchars($row['sender']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo htmlspecialchars($row['message']); ?></td>
        </tr>
    <?php } ?>
</table>
