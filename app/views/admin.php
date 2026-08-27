<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: protect this page
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: /auth/adminLogin');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Tubi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="admin-header">
        <img src="TubiLogo.png" alt="Tubi Logo" class="logo" />
        <h1>Dobrodošli na Admin pocetnu stranicu</h1>
    </header>

    <main class="admin-content">
        <p>Izaberite jednu od opcija.</p>
    </main>

    <nav class="admin-nav">
        <ul>
            <li><a href="/admin/manageProducts">Upravljaj Proizvodima</a></li>
            <li><a href="/admin/createReceipt">Kreiraj Porudzbinu</a></li>
            <li><a href="/admin/receiptHistory">Istorija Porudzbina</a></li>
            <li><a href="/admin/manageUsers">Upravljaj Korisnicima</a></li>
            <li id="logout"><a href="/admin/logout" >Logout</a></li>
        </ul>
    </nav>

</body>
</html>
