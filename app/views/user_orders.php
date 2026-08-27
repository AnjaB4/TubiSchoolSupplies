<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moje Porudžbine</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<!-- if order confirmed and sent to admin-->
    <?php if (!empty($_GET['success'])): ?>
        <script>
            alert("Uspešno ste poslali porudžbinu. Administrator će je uskoro pregledati.");
        </script>
    <?php endif; ?>

    <a class="back-btn" href="/user">Početna</a>
    <h1>Moje Porudžbine</h1>

    <?php if (empty($receipts)): ?>
        <p>Nemate nijednu porudžbinu.</p>
    <?php else: ?>

        <form method="GET" action="/user/orders">
            <label>Mesec:
                <select name="month">
                    <option value="">Svi</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '' ?>>
                            <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </label>

            <label>Godina:
                <input type="number" name="year" value="<?= $_GET['year'] ?? date('Y') ?>" min="2000" max="<?= date('Y') ?>">
            </label>

            <button type="submit">Filtriraj</button>
        </form>

        <table class="products-table">
        <thead>
            <tr>
                <th>Broj računa</th>
                <th>Datum</th>
                <th>Status</th>
                <th>Ukupno</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($receipts as $receipt): ?>
                <tr>
                    <td><?= htmlspecialchars($receipt['receipt_number']) ?></td>
                    <td><?= date('d.m.Y', strtotime($receipt['date_created'])) ?></td>
                    <td><?= htmlspecialchars($receipt['status']) ?></td>
                    <td><?= number_format($receipt['total_price'], 2) ?> RSD</td>
                    <td><a class="edit" href="/user/viewReceipt/<?= $receipt['id_receipt'] ?>">Prikaži</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        </table>
    <?php endif; ?>
</body>
</html>
