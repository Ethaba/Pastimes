<?php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "ClothingStore";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function initializeAllowedClothes($conn)
{
    $allowedItems = [
        [
            'name' => 'Vintage T-Shirt',
            'brand' => 'Nike',
            'description' => 'Soft second-hand branded shirt in good condition.',
            'price' => 199.99,
            'size' => 'M',
            'image' => 'images/Nike.jpg',
            'quantity' => 5,
            'status' => 'available'
        ],
        [
            'name' => 'Oversized Hoodie',
            'brand' => 'Adidas',
            'description' => 'Warm hoodie suitable for casual streetwear outfits.',
            'price' => 399.99,
            'size' => 'L',
            'image' => 'images/AdidasH.jpg',
            'quantity' => 4,
            'status' => 'available'
        ],
        [
            'name' => 'Blue Denim Jeans',
            'brand' => 'Levi\'s',
            'description' => 'Classic straight-leg jeans with a neat second-hand finish.',
            'price' => 499.99,
            'size' => '32',
            'image' => 'images/LevisJean.jpg',
            'quantity' => 3,
            'status' => 'available'
        ],
        [
            'name' => 'Black Jacket',
            'brand' => 'Puma',
            'description' => 'Light jacket for cool weather and everyday wear.',
            'price' => 799.99,
            'size' => 'XL',
            'image' => 'images/PumaJacket.jpg',
            'quantity' => 2,
            'status' => 'available'
        ],
        [
            'name' => 'White Sneakers',
            'brand' => 'Converse',
            'description' => 'Clean pre-owned sneakers with comfortable soles.',
            'price' => 999.99,
            'size' => '42',
            'image' => 'images/ChuckTaylor.jpg',
            'quantity' => 3,
            'status' => 'available'
        ]
    ];

    $allowedNames = array_map(function ($item) {
        return $item['name'];
    }, $allowedItems);

    $quotedNames = implode(', ', array_map(function ($name) use ($conn) {
        return "'" . $conn->real_escape_string($name) . "'";
    }, $allowedNames));

    $existing = $conn->query("SELECT cloth_id, name FROM tblClothes");
    if (!$existing) {
        return;
    }

    $present = [];
    while ($row = $existing->fetch_assoc()) {
        $present[$row['name']] = $row['cloth_id'];
    }

    if (count($present) > 0) {
        $conn->query("DELETE FROM tblClothes WHERE name NOT IN ($quotedNames)");
    }

    foreach ($allowedItems as $item) {
        $name = $conn->real_escape_string($item['name']);
        $brand = $conn->real_escape_string($item['brand']);
        $description = $conn->real_escape_string($item['description']);
        $price = $conn->real_escape_string($item['price']);
        $size = $conn->real_escape_string($item['size']);
        $image = $conn->real_escape_string($item['image']);
        $quantity = $conn->real_escape_string($item['quantity']);
        $status = $conn->real_escape_string($item['status']);

        if (isset($present[$item['name']])) {
            $id = $present[$item['name']];
            $conn->query("UPDATE tblClothes SET brand='$brand', description='$description', price='$price', size='$size', image='$image', quantity='$quantity', status='$status' WHERE cloth_id='$id'");
        } else {
            $conn->query("INSERT INTO tblClothes (name, brand, description, price, size, image, quantity, status) VALUES ('$name', '$brand', '$description', '$price', '$size', '$image', '$quantity', '$status')");
        }
    }
}

$clothesTableCheck = $conn->query("SHOW TABLES LIKE 'tblClothes'");
if ($clothesTableCheck && $clothesTableCheck->num_rows > 0) {
    initializeAllowedClothes($conn);
}
?>