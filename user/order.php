<?php
session_start();

// The old Buy page now sends customers to the cart-based shopping process.
header("Location: products.php");
exit();
?>
