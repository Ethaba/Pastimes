<?php
session_start();
include("../config/DBConn.php");
include("includes/adminLayout.php");

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


<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1>Manage Clothing Inventory</h1>
            <p>Quickly add, update, or remove items from the catalog with a clean admin management view.</p>
        </div>
    </div>

    <?php if ($message) { ?>
        <div class="success message-bar"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <div class="card panel wide-card">
        <h2><?php echo $editItem ? "Edit Clothing Item" : "Add Clothing Item"; ?></h2>

        <form method="POST">
            <input type="hidden" name="cloth_id" value="<?php echo $editItem ? $editItem['cloth_id'] : ''; ?>">

            <div class="form-grid">
                <div>
                    <label>Name</label>
                    <input type="text" name="name" required value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>">
                </div>

                <div>
                    <label>Brand</label>
                    <input type="text" name="brand" required value="<?php echo $editItem ? htmlspecialchars($editItem['brand']) : ''; ?>">
                </div>

                <div class="full-width">
                    <label>Description</label>
                    <textarea name="description" required><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                </div>

                <div>
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" required value="<?php echo $editItem ? $editItem['price'] : ''; ?>">
                </div>

                <div>
                    <label>Size</label>
                    <input type="text" name="size" required value="<?php echo $editItem ? htmlspecialchars($editItem['size']) : ''; ?>">
                </div>

                <div>
                    <label>Image Path</label>
                    <input type="text" name="image" required value="<?php echo $editItem ? htmlspecialchars($editItem['image']) : 'images/OIP.webp'; ?>">
                    <small>Use a filename like <code>JF2454_21_model.avif</code> or a path like <code>images/your-image.avif</code>.</small>
                </div>

                <div>
                    <label>Quantity</label>
                    <input type="number" name="quantity" min="0" required value="<?php echo $editItem ? $editItem['quantity'] : '1'; ?>">
                </div>

                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="available" <?php echo $editItem && $editItem['status'] == 'available' ? 'selected' : ''; ?>>available</option>
                        <option value="sold" <?php echo $editItem && $editItem['status'] == 'sold' ? 'selected' : ''; ?>>sold</option>
                    </select>
                </div>
            </div>

            <button type="submit"><?php echo $editItem ? "Update Clothing" : "Add Clothing"; ?></button>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Clothing Items</h2>
                <p class="info-text"><?php echo $clothes->num_rows; ?> items currently in inventory.</p>
            </div>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $clothes->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                    <td>R<?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['size']); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td>
                        <span class="badge <?php echo $row['status'] == 'available' ? 'available' : 'sold'; ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a class="btn small btn-edit" href="manageClothes.php?edit=<?php echo $row['cloth_id']; ?>">Edit</a>
                        <a class="btn small btn-delete" href="manageClothes.php?delete=<?php echo $row['cloth_id']; ?>" onclick="return confirm('Delete this clothing item?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/adminFooter.php"); ?>
