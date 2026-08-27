//NOT USING?

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Potvrda Porudžbine</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1>Potvrda Porudžbine</h1>

    <?php if ($message): ?>
        <p class="<?= htmlspecialchars($message['type']) ?>">
            <?= htmlspecialchars($message['text']) ?>
        </p>
    <?php else: ?>
        <p>Nema poruke.</p>
    <?php endif; ?>

    <a href="/admin/createReceipt">Kreiraj novu porudžbinu</a> |
    <a href="/admin/manageReceipts">Pregled porudžbina</a>
</body>
</html>
