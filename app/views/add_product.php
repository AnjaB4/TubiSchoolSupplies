<!-- app/views/add_product.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dodaj Novi Proizvod</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Dodaj Novi Proizvod</h1>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="" class="form-edit">
        <label for="name">Naziv:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="id_category">Kategorija:</label>
        <select name="id_category" id="id_category" required>
            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
            <?php endwhile; ?>
        </select><br>

        <label for="quantity">Količina:</label>
        <input type="number" name="quantity" id="quantity" min="0" required><br>

        <label for="unit_price">Cena/kom:</label>
        <input type="number" step="0.01" name="unit_price" id="unit_price" min="0" required><br>

        <label for="price_per_box">Cena/kutija:</label>
        <input type="number" step="0.01" name="price_per_box" id="price_per_box" readonly><br>

        <label for="description">Opis:</label>
        <textarea name="description" id="description"></textarea><br>

        <label for="available">Dostupno:</label>
        <input type="number" name="available" id="available" min="0" value="0" required><br>

        <button type="submit">Dodaj proizvod</button>
    </form>

    <p><a href="/admin/manageProducts">Nazad na upravljanje proizvodima</a></p>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/scripts.js"></script>

</body>
</html>
