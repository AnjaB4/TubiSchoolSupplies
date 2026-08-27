<!-- app/views/user.php -->
 <!-- MY DASHBOARD -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Dashboard - Tubi</title>
    <link rel="stylesheet" href="style.css" /> <!-- adjust path if needed -->
</head>
<body>
    <header>
        <h1>Dobrodošli OŠ <?= htmlspecialchars($user['school_name']) ?>!</h1>
    </header>

    <nav>
        <ul>
           <!-- <li><a href="/user/profile">Profil</a></li>-->
            <li><a href="/user/orders">Moje Porudžbine</a></li>
            <li><a href="/user/products">Naruci Proizvode</a></li>
            <li id="logout"><a href="/user/logout" >Logout</a></li>
        </ul>
    </nav>

    <main>
        <p>Ovde možete da upravljate svojim nalogom, naručujete proizvode i pregledate istoriju porudžbina.</p>
        <!-- Add more user-specific info or links here -->
    </main>

    <footer>
        <p>&copy; 2025 Busa Co</p>
    </footer>
</body>
</html>
