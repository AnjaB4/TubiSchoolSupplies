<!-- app/views/edit_user.php-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Izmeni korisnika</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <a class="back-btn" href="/admin/manageUsers">Nazad na listu korisnika</a>
    <h1>Izmena korisnika: <?= htmlspecialchars($user['username']) ?></h1>

    <form method="POST" action="/admin/editUser/<?= $user['id_user'] ?>" class="form-edit">
        <label>Korisničko ime:
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </label>

        <label>Email:
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </label>

        <label>Škola:
            <input type="text" name="school_name" value="<?= htmlspecialchars($user['school_name']) ?>">
        </label>

        <label>Adresa škole:
            <input type="text" name="school_address" value="<?= htmlspecialchars($user['school_address']) ?>">
        </label>

        <label>Okrug:
            <input type="text" name="district" value="<?= htmlspecialchars($user['district']) ?>">
        </label>

        <label>Grad:
            <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>">
        </label>

        <button class="add-product-btn" type="submit">Sačuvaj izmene</button>
    </form>
</body>
</html>
