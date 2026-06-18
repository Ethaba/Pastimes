<?php
session_start();
include("../config/DBConn.php");
include("includes/adminLayout.php");

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


        <div class="admin-topbar">
            <div>
                <h1>Dashboard Overview</h1>
                <p>Manage platform activity and approvals</p>
            </div>
        </div>

        <?php if (!$setupReady) { ?>
            <div class="warning-box">
                <p><?php echo $setupMessage; ?></p>
                <a class="btn primary" href="../scripts/CreateTable.php">Create Tables</a>
            </div>
        <?php } else { ?>

        <!-- STATS -->
        <section class="stats">
            <div class="stat">
                <h2><?php echo $clothingCount['total']; ?></h2>
                <p>Total Clothing</p>
            </div>

            <div class="stat">
                <h2><?php echo $requestCount['total']; ?></h2>
                <p>Pending Requests</p>
            </div>
        </section>

        <!-- USERS TABLE -->
        <section class="panel">
            <h2>Pending Users</h2>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $pendingUsers->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <a class="button-link table-button button-approve" href="verifyUsers.php?id=<?php echo $row['user_id']; ?>&action=approve">
                                Approve
                                </a>
                                <a class="button-link table-button button-reject" href="verifyUsers.php?id=<?php echo $row['user_id']; ?>&action=reject" onclick="return confirm('Reject this user?');">
                                Reject
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </section>

        <?php } ?>

    </main>
</div>
<?php include("includes/adminFooter.php"); ?>