<?php
session_start();
include("../config/DBConn.php");

function normalizeImagePath($imagePath) {
    $imagePath = trim(str_replace('\\', '/', $imagePath));
    if ($imagePath === '') {
        return 'images/OIP.webp';
    }
    if (preg_match('#^(https?://|/|[A-Za-z]:)#i', $imagePath)) {
        return $imagePath;
    }
    $imagePath = preg_replace('#^assets/images/#i', 'images/', $imagePath);
    if (!preg_match('#^(images/|assets/)#i', $imagePath)) {
        $imagePath = 'images/' . ltrim($imagePath, '/');
    }
    return $imagePath;
}

if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

$message = "";
$editItem = null;

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);

    // If the item already appears in an order, it is marked as sold instead of breaking the order history.
    if ($conn->query("DELETE FROM tblClothes WHERE cloth_id='$id'")) {
        $message = "Clothing item deleted.";
    } else {
        $conn->query("UPDATE tblClothes SET status='sold', quantity=0 WHERE cloth_id='$id'");
        $message = "Clothing item was used in an order, so it was marked as sold.";
    }
}

if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $editResult = $conn->query("SELECT * FROM tblClothes WHERE cloth_id='$id'");
    $editItem = $editResult->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $brand = mysqli_real_escape_string($conn, trim($_POST['brand']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $price = mysqli_real_escape_string($conn, trim($_POST['price']));
    $size = mysqli_real_escape_string($conn, trim($_POST['size']));
    $image = mysqli_real_escape_string($conn, normalizeImagePath($_POST['image']));
    $quantity = mysqli_real_escape_string($conn, trim($_POST['quantity']));
    $status = mysqli_real_escape_string($conn, trim($_POST['status']));

    if ($_POST['cloth_id'] == "") {
        // A blank cloth_id means a new item must be inserted.
        $conn->query("INSERT INTO tblClothes (name, brand, description, price, size, image, quantity, status)
            VALUES ('$name', '$brand', '$description', '$price', '$size', '$image', '$quantity', '$status')");
        $message = "Clothing item added.";
    } else {
        $id = mysqli_real_escape_string($conn, $_POST['cloth_id']);
        $conn->query("UPDATE tblClothes SET
            name='$name',
            brand='$brand',
            description='$description',
            price='$price',
            size='$size',
            image='$image',
            quantity='$quantity',
            status='$status'
            WHERE cloth_id='$id'");
        $message = "Clothing item updated.";
        $editItem = null;
    }
}

$clothes = $conn->query("SELECT * FROM tblClothes ORDER BY cloth_id DESC");
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
    <h2><?php echo $editItem ? "Edit Clothing" : "Add Clothing"; ?></h2>
    <p class="success"><?php echo $message; ?></p>

    <form method="POST">
        <input type="hidden" name="cloth_id" value="<?php echo $editItem ? $editItem['cloth_id'] : ''; ?>">

        <label>Name</label>
        <input type="text" name="name" required value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>">

        <label>Brand</label>
        <input type="text" name="brand" required value="<?php echo $editItem ? htmlspecialchars($editItem['brand']) : ''; ?>">

        <label>Description</label>
        <textarea name="description" required><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>

        <label>Price</label>
        <input type="number" name="price" step="0.01" required value="<?php echo $editItem ? $editItem['price'] : ''; ?>">

        <label>Size</label>
        <input type="text" name="size" required value="<?php echo $editItem ? htmlspecialchars($editItem['size']) : ''; ?>">

        <label>Image Path</label>
        <input type="text" name="image" required value="<?php echo $editItem ? htmlspecialchars($editItem['image']) : 'images/OIP.webp'; ?>">
        <small>Use a filename like <code>JF2454_21_model.avif</code> or a path like <code>images/your-image.avif</code>.</small>

        <label>Quantity</label>
        <input type="number" name="quantity" min="0" required value="<?php echo $editItem ? $editItem['quantity'] : '1'; ?>">

        <label>Status</label>
        <select name="status">
            <option value="available" <?php echo $editItem && $editItem['status'] == 'available' ? 'selected' : ''; ?>>available</option>
            <option value="sold" <?php echo $editItem && $editItem['status'] == 'sold' ? 'selected' : ''; ?>>sold</option>
        </select>

        <button type="submit"><?php echo $editItem ? "Update Clothing" : "Add Clothing"; ?></button>
    </form>
</div>
</div>

<h2 class="section-title">Clothing Items</h2>
<table>
    <tr>
        <th>Name</th>
        <th>Brand</th>
        <th>Price</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $clothes->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['brand']); ?></td>
            <td>R<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['size']); ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <a href="manageClothes.php?edit=<?php echo $row['cloth_id']; ?>">Edit</a>
                |
                <a href="manageClothes.php?delete=<?php echo $row['cloth_id']; ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>
