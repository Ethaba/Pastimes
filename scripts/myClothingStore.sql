DROP TABLE IF EXISTS tblMessage;
DROP TABLE IF EXISTS tblSellRequest;
DROP TABLE IF EXISTS tblOrderLine;
DROP TABLE IF EXISTS tblAorder;
DROP TABLE IF EXISTS tblClothes;
DROP TABLE IF EXISTS tblAdmin;
DROP TABLE IF EXISTS tblUser;

CREATE TABLE tblUser (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50),
    lname VARCHAR(50),
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    address VARCHAR(255),
    status VARCHAR(20)
);

CREATE TABLE tblAdmin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);

CREATE TABLE tblClothes (
    cloth_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    brand VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    size VARCHAR(10),
    image VARCHAR(255),
    quantity INT,
    status VARCHAR(20)
);

CREATE TABLE tblAorder (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date DATETIME,
    reference_number VARCHAR(50),
    delivery_address VARCHAR(255),
    total_amount DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES tblUser(user_id)
);

CREATE TABLE tblOrderLine (
    order_line_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    cloth_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES tblAorder(order_id),
    FOREIGN KEY (cloth_id) REFERENCES tblClothes(cloth_id)
);

CREATE TABLE tblSellRequest (
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
);

CREATE TABLE tblMessage (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    admin_id INT,
    subject VARCHAR(150),
    message TEXT,
    sender VARCHAR(20),
    message_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES tblUser(user_id)
);

INSERT INTO tblUser (fname,lname,username,email,password,address,status) VALUES
('John','Doe','jdoe','j.doe@abc.co.za',MD5('12345678'),'12 Market Street','approved'),
('Lerato','Mokoena','lmokoena','lerato@example.com',MD5('12345678'),'45 Rose Avenue','pending'),
('Sipho','Dlamini','sdlamini','sipho@example.com',MD5('12345678'),'8 Main Road','approved'),
('Amy','Smith','asmith','amy@example.com',MD5('12345678'),'19 Oak Lane','pending'),
('Thabo','Naidoo','tnaidoo','thabo@example.com',MD5('12345678'),'77 Long Street','approved');

INSERT INTO tblAdmin (username,password) VALUES
('admin', MD5('admin123')),
('Ndumiso', MD5('Ndumiso2309'));

INSERT INTO tblClothes (name,brand,description,price,size,image,quantity,status) VALUES
('Vintage T-Shirt','Nike','Soft second-hand branded shirt in good condition.',199.99,'M','images/Nike.jpg',5,'available'),
('Oversized Hoodie','Adidas','Warm hoodie suitable for casual streetwear outfits.',399.99,'L','images/AdidasH.jpg',4,'available'),
('Blue Denim Jeans','Levi''s','Classic straight-leg jeans with a neat second-hand finish.',499.99,'32','images/LevisJean.jpg',3,'available'),
('Black Jacket','Puma','Light jacket for cool weather and everyday wear.',799.99,'XL','images/PumaJacket.jpg',2,'available'),
('White Sneakers','Converse','Clean pre-owned sneakers with comfortable soles.',999.99,'42','images/ChuckTaylor.jpg',3,'available');
