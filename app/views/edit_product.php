<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Izmeni Proizvod</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Izmeni Proizvod</h1>

    <form method="post" action="" class="form-edit">
        <label for="name">Naziv:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>

        <label for="id_category">Kategorija:</label>
        <select name="id_category" id="id_category" required>
            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['id_category'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label for="quantity">Količina:</label>
        <input type="number" name="quantity" id="quantity" min="0" value="<?= (int)$product['quantity'] ?>" required><br>

        <label for="unit_price">Cena/kom:</label>
        <input type="number" step="0.01" name="unit_price" id="unit_price" min="0" value="<?= (float)$product['unit_price'] ?>" required><br>

        <label for="price_per_box">Cena/kutija:</label>
        <input type="number" step="0.01" name="price_per_box" id="price_per_box" min="0" value="<?= (float)$product['price_per_box'] ?>" required><br>

        <label for="description">Opis:</label>
        <textarea name="description" id="description"><?= htmlspecialchars($product['description']) ?></textarea><br>

        <label for="available">Dostupno (količina):</label>
        <input type="number" name="available" id="available" min="0" value="<?= (int)$product['available'] ?>" required><br>

        <button type="submit">Sačuvaj izmene</button>
    </form>

    <p><a href="/admin/manageProducts">Nazad na upravljanje proizvodima</a></p>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/scripts.js"></script>


</body>
</html>
