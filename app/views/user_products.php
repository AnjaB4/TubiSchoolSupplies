<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dostupni Proizvodi</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <a class="back-btn" href="/user">Početna</a>

    <?php if (!empty($_GET['error']) && $_GET['error'] === 'no-selection'): ?>
        <p class="error-message" style="color: red; font-weight: bold;">
            Molimo izaberite bar jedan proizvod i količinu.
        </p>
    <?php endif; ?>

    <h1>Dostupni Proizvodi</h1>

    <form method="POST" action="/user/addToCart">
        <table class="products-table">
            <thead>
                <tr>
                    <th>Naziv</th>
                    <th>Kategorija</th>
                    <th>Dostupno</th>
                    <th>Cena/kom</th>
                    <th>Cena/kutija</th>
                    <th>Opis</th>
                    <th>Dodaj u korpu</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= (int)$row['available'] ?></td>
                        <td><?= number_format($row['unit_price'], 2) ?> RSD</td>
                        <td><?= number_format($row['price_per_box']) ?> RSD</td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <input type="checkbox" name="products[<?= $row['id_product'] ?>][selected]">
                            <input type="number"
                                   name="products[<?= $row['id_product'] ?>][quantity]"
                                   min="1"
                                   max="<?= (int)$row['available'] ?>"
                                   placeholder="kolicina"
                                   style="width: 60px;">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <button class="add-product-btn" type="submit">Dodaj u korpu</button>
    </form>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a href="/user/products/<?= $page - 1 ?>">&#8592; Prethodna</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p == $page): ?>
                    <strong><?= $p ?></strong>
                <?php else: ?>
                    <a href="/user/products/<?= $p ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="/user/products/<?= $page + 1 ?>">Sledeća &#8594;</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</body>
</html>
