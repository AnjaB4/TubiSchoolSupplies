<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kreiraj Porudžbinu</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <a class="back-btn" href="/admin">Početna</a><br>
    <h1>Kreiraj Porudžbinu</h1>
    
    <form id="receipt-form" method="POST" action="/admin/submitReceipt">
        <label for="id_user">Izaberi korisnika:</label>
        <select name="id_user" required>
            <?php if (!$users_result): ?>
                <option disabled>Nema korisnika</option>
            <?php else: ?>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <option value="<?= $user['id_user'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <h2>Dodaj proizvode:</h2>
        <input type="text" id="product-search" placeholder="Pretraži proizvode..." style="margin-bottom: 10px; padding: 5px; width: 50%;">

        <div id="product-list">
            <?php if (!$products_result): ?>
                <p>Greška u učitavanju proizvoda.</p>
            <?php else: ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <?php
                    //var_dump($product['category_name']); // DEBUG
                        $isCokoladica = ($product['category_name'] === 'Čokolade');
                        $price = $isCokoladica ? $product['price_per_box'] : $product['unit_price'];
                        $unitLabel = $isCokoladica ? 'RSD/kutija' : 'RSD/kom';
                    ?>
                    <div class="product-row" data-price="<?= $price ?>" data-category="<?= $product['category_name'] ?>">
                        <label>
                            <input type="checkbox" name="products[<?= $product['id_product'] ?>][selected]" class="product-checkbox">
                            <?= htmlspecialchars($product['name']) ?> - <?= number_format($price, 2) ?> <?= $unitLabel ?>
                        </label>
                        <input type="number" name="products[<?= $product['id_product'] ?>][quantity]" placeholder="Količina" min="1" class="quantity-input" disabled>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <p class="total">Ukupna cena: <span id="total-price">0.00</span> RSD</p>

        <button type="submit">Potvrdi porudžbinu</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/scripts.js"></script>
    
</body>
</html>
