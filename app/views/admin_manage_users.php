<!-- app/views/admin_manage_users.php-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upravljaj Korisnicima</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <a class="back-btn" href="/admin">Nazad</a>
    <h1>Lista Korisnika</h1>

    <table class="products-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Korisničko ime</th>
                <th>Email</th>
                <th>Škola</th>
                <th>Adresa škole</th>
                <th>Okrug</th>
                <th>Grad</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id_user'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['school_name']) ?></td>
                    <td><?= htmlspecialchars($user['school_address']) ?></td>
                    <td><?= htmlspecialchars($user['district']) ?></td>
                    <td><?= htmlspecialchars($user['city']) ?></td>
                    <td class="action-buttons">
                        <a class="edit" href="/admin/editUser/<?= $user['id_user'] ?>">Izmeni</a> 
                        <a class="delete" href="/admin/deleteUser/<?= $user['id_user'] ?>" onclick="return confirm('Da li ste sigurni da želite da obrišete ovog korisnika?');">Obriši</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>