<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

// These checks prevent the page from crashing if the Part 3 database tables have not been created yet.
$setupReady = true;
$setupMessage = "";

$sellRequestTable = $conn->query("SHOW TABLES LIKE 'tblSellRequest'");
$orderLineTable = $conn->query("SHOW TABLES LIKE 'tblOrderLine'");
$usernameColumn = $conn->query("SHOW COLUMNS FROM tblUser LIKE 'username'");

if ($sellRequestTable->num_rows == 0 || $orderLineTable->num_rows == 0 || $usernameColumn->num_rows == 0) {
    $setupReady = false;
    $setupMessage = "The Part 3 database tables have not been created yet. Please run scripts/CreateTable.php once before using the final app.";
}

if ($setupReady) {
    $pendingUsers = $conn->query("SELECT * FROM tblUser WHERE status='pending'");
    $clothingCount = $conn->query("SELECT COUNT(*) AS total FROM tblClothes")->fetch_assoc();
    $requestCount = $conn->query("SELECT COUNT(*) AS total FROM tblSellRequest WHERE request_status='pending'")->fetch_assoc();
} else {
    $pendingUsers = false;
    $clothingCount = array('total' => 0);
    $requestCount = array('total' => 0);
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Admin Panel</h3>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manageClothes.php">Clothes</a>
        <a href="manageUsers.php">Users</a>
        <a href="sellRequests.php">Seller Requests</a>
        <a href="messages.php">Messages</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="page-header">
    <h1>Administrator Dashboard</h1>
    <p>Approve users, manage clothing records, review seller requests, and communicate about orders.</p>
</div>

<?php if (!$setupReady) { ?>
    <div class="setup-warning">
        <p><?php echo $setupMessage; ?></p>
        <a class="button-link" href="../scripts/CreateTable.php">Create Part 3 Tables</a>
    </div>
<?php } else { ?>

<div class="stats-grid">
    <div class="stat-box">
        <strong><?php echo $clothingCount['total']; ?></strong>
        <span>Clothing Items</span>
    </div>
    <div class="stat-box">
        <strong><?php echo $requestCount['total']; ?></strong>
        <span>Pending Seller Requests</span>
    </div>
</div>

<h2 class="section-title">Pending Users</h2>

<table>
<tr>
    <th>Name</th>
    <th>Username</th>
    <th>Email</th>
    <th>Address</th>
    <th>Action</th>
</tr>

<?php while ($row = $pendingUsers->fetch_assoc()) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['address']); ?></td>
        <td><a class="button-link table-button" href="verifyUsers.php?id=<?php echo $row['user_id']; ?>">Approve</a></td>
    </tr>
<?php } ?>
</table>

<?php } ?>
