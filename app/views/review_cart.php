<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pregled Korpe</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <a class="back-btn" href="/user/products">Nazad</a>

    <h1>Pregled Porudžbine</h1>

    <form method="POST" action="/user/confirmOrder">
        <table>
            <thead>
                <tr>
                    <th>Proizvod</th>
                    <th>Cena</th>
                    <th>Količina</th>
                    <th>Ukupno</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= number_format($product['unit_price'], 2) ?> RSD</td>
                        <td><?= $product['quantity'] ?></td>
                        <td><?= number_format($product['subtotal'], 2) ?> RSD</td>
                    </tr>
                    <!-- Hidden input to carry product ID and quantity -->
                    <input type="hidden" name="products[<?= $product['id'] ?>][quantity]" value="<?= $product['quantity'] ?>">
                <?php endforeach; ?>

            </tbody>
        </table>

        <h3>Ukupna Cena: <?= number_format($total, 2) ?> RSD</h3>

        <div class="form-actions">
            <button type="submit">Potvrdi Porudžbinu</button>
            <a href="/user/products" class="delete" style="margin-left: 10px;">Otkaži Porudžbinu</a>
        </div>
    
    </form>
</body>
</html>
