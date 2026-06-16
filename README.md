# Pastimes Clothing Store

Pastimes is a PHP and MySQL second-hand clothing marketplace. Customers can register, wait for admin approval, log in, browse available clothes, use a shopping cart, checkout, view purchase history, request to sell clothing, and send messages.

## Setup

1. Start Apache and MySQL in XAMPP.
2. Create a MySQL database named `ClothingStore`.
3. Open `http://localhost/ClothingStore/scripts/CreateTable.php` to create the tables and sample data.
4. Open `http://localhost/ClothingStore/index.php`.

## Test Accounts

Admin:

- Username: `admin`
- Password: `admin123`

Approved customer:

- Username: `jdoe`
- Email: `j.doe@abc.co.za`
- Password: `12345678`

## Part 3 Features

- Shopping cart with add item, update quantity, remove item, continue shopping, checkout, and empty cart.
- Checkout creates order records and order line records.
- Checkout displays a Pastimes reference number.
- Purchase history report displays order totals.
- Admin can approve users.
- Admin can add, update, and delete clothing records.
- Admin can edit and delete users.
- Customers can request to sell clothing with brand, description, and image.
- Admin can review seller requests and send notes.
- Buyers, sellers, and admin can send messages.
