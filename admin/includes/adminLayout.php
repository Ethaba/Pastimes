<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: ../adminLogin.php");
    exit();
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="brand">
            <h2>Pastimes</h2>
            <span>Admin Panel</span>
        </div>

        <nav class="admin-nav">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="manageUsers.php">👤 Users</a>
            <a href="manageClothes.php">👕 Clothes</a>
            <a href="sellRequests.php">📦 Seller Requests</a>
            <a href="messages.php">💬 Messages</a>
            <a href="../auth/logout.php">🚪 Logout</a>
        </nav>
    </aside>

    <main class="admin-main">

    <div class="admin-main">