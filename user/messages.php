<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user']['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $body = mysqli_real_escape_string($conn, trim($_POST['message']));

    $conn->query("INSERT INTO tblMessage (user_id, admin_id, subject, message, sender, message_date)
        VALUES ('$userId', 1, '$subject', '$body', 'user', NOW())");

    $message = "Message sent.";
}

$messages = $conn->query("SELECT * FROM tblMessage WHERE user_id='$userId' ORDER BY message_date DESC");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="products.php">Shop</a>
        <a href="sellRequest.php">Sell</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="container">
<div class="card wide-card">
    <h2>Messages</h2>
    <p class="success"><?php echo $message; ?></p>

    <form method="POST">
        <label>Subject</label>
        <input type="text" name="subject" required>

        <label>Message</label>
        <textarea name="message" required></textarea>

        <button type="submit">Send Message</button>
    </form>
</div>
</div>

<table>
    <tr>
        <th>Date</th>
        <th>From</th>
        <th>Subject</th>
        <th>Message</th>
    </tr>

    <?php while ($row = $messages->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['message_date']); ?></td>
            <td><?php echo htmlspecialchars($row['sender']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo htmlspecialchars($row['message']); ?></td>
        </tr>
    <?php } ?>
</table>
