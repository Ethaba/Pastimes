<?php
session_start();
include("../config/DBConn.php");
include("includes/adminLayout.php");

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
$userCount = $users->num_rows;
$messageCount = $messages->num_rows;
?>

<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1>Admin Messages</h1>
            <p>Send a direct message to approved users and review your message history in one place.</p>
        </div>
    </div>

    <?php if ($notice) { ?>
        <div class="message-bar"><?php echo htmlspecialchars($notice); ?></div>
    <?php } ?>

    <div class="panel">
        <div class="panel-header panel-header--split">
            <div>
                <h2>Send Message</h2>
                <p class="info-text">Choose a user and compose a message for the selected buyer or seller.</p>
            </div>
            <div class="stat-group">
                <div class="stat-card stat-card--mini">
                    <span>Approved Users</span>
                    <strong><?php echo $userCount; ?></strong>
                </div>
                <div class="stat-card stat-card--mini">
                    <span>Messages Recorded</span>
                    <strong><?php echo $messageCount; ?></strong>
                </div>
            </div>
        </div>

        <form method="POST" class="form-grid">
            <div>
                <label>User</label>
                <select name="user_id" required>
                    <?php while ($user = $users->fetch_assoc()) { ?>
                        <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['fname'] . " " . $user['lname']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="full-width">
                <label>Subject</label>
                <input type="text" name="subject" required>
            </div>

            <div class="full-width">
                <label>Message</label>
                <textarea name="message" required></textarea>
            </div>

            <div class="full-width">
                <button type="submit">Send Message</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Message History</h2>
                <p class="info-text"><?php echo $messageCount; ?> messages recorded.</p>
            </div>
        </div>

        <?php if ($messageCount === 0) { ?>
            <div class="empty-state">No messages yet. Use the form above to send your first message.</div>
        <?php } else { ?>
            <div class="message-list">
                <?php while ($row = $messages->fetch_assoc()) { ?>
                    <article class="message-card">
                        <div class="message-card__top">
                            <div>
                                <p class="message-user"><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></p>
                                <span class="message-badge <?php echo htmlspecialchars($row['sender']); ?>"><?php echo htmlspecialchars(ucfirst($row['sender'])); ?></span>
                            </div>
                            <span class="message-date"><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($row['message_date']))); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($row['subject']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    </article>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php include("includes/adminFooter.php"); ?>