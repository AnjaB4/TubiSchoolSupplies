<?php
// This file assumes $result, $page, and $totalPages come from the controller

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: admin check (you can also keep it in controller)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: /admin/login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Manage Products</title>
        <link rel="stylesheet" href="/style.css">
    </head>
    <body>
        <a class="back-btn" href="/admin">Početna</a>

        <h1>Upravljaj Proizvodima</h1>

        <a class="add-product-btn" href="/admin/addProduct">+ Dodaj Novi Proizvod</a>

        <table class="products-table">
            <thead>
                <tr>
                    <th>Naziv</th>
                    <th>Kategorija</th>
                    <th>Količina</th>
                    <th>Cena/kom</th>
                    <th>Cena/kutija</th>
                    <th>Opis</th>
                    <th>Dostupno</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody id="products-table-body">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= number_format($row['unit_price'], 2) ?> RSD</td>
                    <td><?= number_format($row['price_per_box']) ?> RSD</td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= (int)$row['available'] ?></td>
                    <td class="action-buttons">
                        <a class="edit" href="/admin/editProduct/<?= $row['id_product'] ?>">Izmeni</a>
                        <a class="delete" href="/admin/deleteProduct/<?= $row['id_product'] ?>" onclick="return confirm('Obriši proizvod?');">Obriši</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination controls -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a href="/admin/manageProducts/<?= $page - 1 ?>">&#8592; Prethodna</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p == $page): ?>
                    <strong><?= $p ?></strong>
                <?php else: ?>
                    <a href="/admin/manageProducts/<?= $p ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="/admin/manageProducts/<?= $page + 1 ?>">Sledeća &#8594;</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="/scripts.js"></script>

        
    </body>
</html>
