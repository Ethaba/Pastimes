<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user']['user_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['clothing_name']));
    $brand = mysqli_real_escape_string($conn, trim($_POST['brand']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $imagePath = "";

    // Upload image correctly into /images folder (project root)
if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {

    $imageName = time() . "_" . basename($_FILES['image']['name']);

    // correct folder (NOT assets)
    $targetPath = "../images/" . $imageName;

    // ensure folder exists (safe)
    if (!file_exists("../images")) {
        mkdir("../images", 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        // store DB path
        $imagePath = "images/" . $imageName;
    } else {
        $imagePath = ""; // fallback if upload fails
    }
}

    $conn->query("INSERT INTO tblSellRequest (user_id, clothing_name, brand, description, image, request_status, request_date)
        VALUES ('$userId', '$name', '$brand', '$description', '$imagePath', 'pending', NOW())");

    $message = "Your selling request was sent to the administrator.";
}

$userId = $_SESSION['user']['user_id'];
$requests = $conn->query("SELECT * FROM tblSellRequest WHERE user_id='$userId' ORDER BY request_date DESC");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="products.php">Shop</a>
        <a href="messages.php">Messages</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="container">
<div class="card wide-card">
    <h2>Request to Sell Clothes</h2>
    <p class="success"><?php echo $message; ?></p>

    <form method="POST" enctype="multipart/form-data">
        <label>Clothing Name</label>
        <input type="text" name="clothing_name" required>

        <label>Brand</label>
        <input type="text" name="brand" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Clothing Image</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Send Request</button>
    </form>
</div>
</div>

<h2 class="section-title">My Selling Requests</h2>
<table>
    <tr>
        <th>Item</th>
        <th>Brand</th>
        <th>Status</th>
        <th>Admin Note</th>
    </tr>
    <?php while ($request = $requests->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($request['clothing_name']); ?></td>
            <td><?php echo htmlspecialchars($request['brand']); ?></td>
            <td><?php echo htmlspecialchars($request['request_status']); ?></td>
            <td><?php echo htmlspecialchars($request['admin_note']); ?></td>
        </tr>
    <?php } ?>
</table>
