<?php
include("../config/DBConn.php");

$conn->query("DROP TABLE IF EXISTS tblMessage");
$conn->query("DROP TABLE IF EXISTS tblSellRequest");
$conn->query("DROP TABLE IF EXISTS tblOrderLine");
$conn->query("DROP TABLE IF EXISTS tblAorder");
$conn->query("DROP TABLE IF EXISTS tblClothes");
$conn->query("DROP TABLE IF EXISTS tblAdmin");
$conn->query("DROP TABLE IF EXISTS tblUser");

$conn->query("CREATE TABLE tblUser (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50),
    lname VARCHAR(50),
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    address VARCHAR(255),
    status VARCHAR(20)
)");

$conn->query("CREATE TABLE tblAdmin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
)");

$conn->query("CREATE TABLE tblClothes (
    cloth_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    brand VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    size VARCHAR(10),
    image VARCHAR(255),
    quantity INT,
    status VARCHAR(20)
)");

$conn->query("CREATE TABLE tblAorder (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date DATETIME,
    reference_number VARCHAR(50),
    delivery_address VARCHAR(255),
    total_amount DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES tblUser(user_id)
)");

$conn->query("CREATE TABLE tblOrderLine (
    order_line_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    cloth_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES tblAorder(order_id),
    FOREIGN KEY (cloth_id) REFERENCES tblClothes(cloth_id)
)");

$conn->query("CREATE TABLE tblSellRequest (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    clothing_name VARCHAR(100),
    brand VARCHAR(100),
    description TEXT,
    image VARCHAR(255),
    request_status VARCHAR(20),
    request_date DATETIME,
    admin_note TEXT,
    FOREIGN KEY (user_id) REFERENCES tblUser(user_id)
)");

$conn->query("CREATE TABLE tblMessage (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    admin_id INT,
    subject VARCHAR(150),
    message TEXT,
    sender VARCHAR(20),
    message_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES tblUser(user_id)
)");

$userDataFile = file("../data/userData.txt");
foreach ($userDataFile as $line) {
    $line = trim($line);
    if (!empty($line)) {
        $userData = explode(",", $line);

        // Each user line must have seven fields for the Part 3 user table.
        if (count($userData) < 7) {
            continue;
        }

        $fname = mysqli_real_escape_string($conn, $userData[0]);
        $lname = mysqli_real_escape_string($conn, $userData[1]);
        $username = mysqli_real_escape_string($conn, $userData[2]);
        $email = mysqli_real_escape_string($conn, $userData[3]);
        $password = mysqli_real_escape_string($conn, $userData[4]);
        $address = mysqli_real_escape_string($conn, $userData[5]);
        $status = mysqli_real_escape_string($conn, $userData[6]);
        
        $conn->query("INSERT INTO tblUser (fname,lname,username,email,password,address,status) VALUES 
        ('$fname','$lname','$username','$email','$password','$address','$status')");
    }
}

$conn->query("INSERT INTO tblAdmin (username,password) VALUES 
('admin', md5('admin123')),
('Ndumiso', md5('Ndumiso2309'))
");

$conn->query("INSERT INTO tblClothes (name,brand,description,price,size,image,quantity,status) VALUES
('Vintage T-Shirt','Nike','Soft second-hand branded shirt in good condition.',199.99,'M','images/Nike.jpg',5,'available'),
('Oversized Hoodie','Adidas','Warm hoodie suitable for casual streetwear outfits.',399.99,'L','images/AdidasH.jpg',4,'available'),
('Blue Denim Jeans','Levi''s','Classic straight-leg jeans with a neat second-hand finish.',499.99,'32','images/LevisJean.jpg',3,'available'),
('Black Jacket','Puma','Light jacket for cool weather and everyday wear.',799.99,'XL','images/PumaJacket.jpg',2,'available'),
('White Sneakers','Converse','Clean pre-owned sneakers with comfortable soles.',999.99,'42','images/ChuckTaylor.jpg',3,'available'),
('Summit Jacket','The North Face','Technical outerwear designed for cold weather.',1899.99,'M,L,XL','images/NorthFaceSummit.jpg',9,'available'),
('Distressed Jeans','Amiri','High-end denim with a modern distressed finish.',1499.99,'S,M,L,XL','images/AmiriJeans.jpg',7,'available'),
('Silk Statement Shirt','Gucci','Luxury silk shirt with a bold pattern and fine cut.',2499.00,'XS,S,M,L','images/GucciShirt.jpg',8,'available'),
('Monogram Vest','Louis Vuitton','Premium monogram vest with elegant detailing.',2899.99,'S,M,L','images/LVVest.jpg',6,'available'),
('High-Top Sneakers','Giuseppe Zanotti','Designer sneakers with metallic accents and premium stitching.',2399.99,'8,9,10,11','images/ZanottiSneakers.jpg',5,'available'),
('Vintage Suit','Ermenegildo Zegna','Tailored Italian suit with a classic vintage cut.',2999.00,'M,L,XL','images/ZegnaSuit.jpg',4,'available'),
('Leather Trench','Ermenglio Zignalli','Statement leather trench coat with dramatic style.',2199.99,'M,L','images/ZignalliTrench.jpg',2,'available'),
('Satin Slip Dress','Valli','Elegant evening dress with a silky sheen.',1799.00,'XS,S,M','images/ValliDress.jpg',10,'available'),
('Classic White Tee','Everyday','A clean, premium cotton t-shirt for every wardrobe.',249.99,'S,M,L,XL,XXL','images/WhiteTee.jpg',15,'available')
");

echo "All tables created successfully!";
?>
