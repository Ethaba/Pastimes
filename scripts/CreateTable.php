<?php
include("../config/DBConn.php");

$conn->query("DROP TABLE IF EXISTS tblAorder");
$conn->query("DROP TABLE IF EXISTS tblClothes");
$conn->query("DROP TABLE IF EXISTS tblAdmin");
$conn->query("DROP TABLE IF EXISTS tblUser");

$conn->query("CREATE TABLE tblUser (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50),
    lname VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
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
    price DECIMAL(10,2),
    size VARCHAR(10)
)");

$conn->query("CREATE TABLE tblAorder (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    cloth_id INT,
    FOREIGN KEY (user_id) REFERENCES tblUser(user_id),
    FOREIGN KEY (cloth_id) REFERENCES tblClothes(cloth_id)
)");

$userDataFile = file("../data/userData.txt");
foreach ($userDataFile as $line) {
    $line = trim($line);
    if (!empty($line)) {
        $userData = explode(",", $line);
        $fname = mysqli_real_escape_string($conn, $userData[0]);
        $lname = mysqli_real_escape_string($conn, $userData[1]);
        $email = mysqli_real_escape_string($conn, $userData[2]);
        $password = mysqli_real_escape_string($conn, $userData[3]);
        $status = mysqli_real_escape_string($conn, $userData[4]);
        
        $conn->query("INSERT INTO tblUser (fname,lname,email,password,status) VALUES 
        ('$fname','$lname','$email','$password','$status')");
    }
}

$conn->query("INSERT INTO tblAdmin (username,password) VALUES 
('admin', md5('admin123')),
('Ndumiso', md5('Ndumiso2309'))
");

$conn->query("INSERT INTO tblClothes (name,price,size) VALUES
('T-Shirt',199.99,'M'),
('Hoodie',399.99,'L'),
('Jeans',499.99,'32'),
('Jacket',799.99,'XL'),
('Sneakers',999.99,'42')
");

echo "All tables created successfully!";
?>