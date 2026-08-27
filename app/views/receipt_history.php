<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Istorija Porudžbina</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <a class="back-btn" href="/admin">Početna</a>
    
    <h1>Istorija porudžbina</h1>

    <form method="GET" action="/admin/receiptHistory">
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
                <th>ID</th>
                <th>Datum</th>
                <th>Škola</th>
                <th>Broj otpremnice</th>
                <th>Status</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody id="receipts-table-body">
            <?php foreach ($receipts as $receipt): ?>
                <tr>
                    <td><?= $receipt['id_receipt'] ?></td>
                    <td><?= date('d.m.Y', strtotime($receipt['date_created'])) ?></td>
                    <td><?= htmlspecialchars($receipt['school_name']) ?></td>
                    <td><?= $receipt['receipt_number'] ?></td>
                    
                    <!-- STATUS -->
                    <td>
                        <?php if ($receipt['status'] === 'na cekanju'): ?>
                            <?php
                                // Build query suffix from current GET parameters to keep filters and pagination
                                $queryParams = $_GET;
                                unset($queryParams['url']);  // REMOVE url=admin/receiptHistory from query string
                                $query = http_build_query($queryParams);
                                $suffix = $query ? '?' . $query : '';
                            ?>
                            <a href="/admin/approveReceipt/<?= $receipt['id_receipt'] . $suffix ?>" class="approve-btn">Odobri</a>
                            <a href="/admin/rejectReceipt/<?= $receipt['id_receipt'] . $suffix ?>" class="reject-btn">Odbij</a>
                        <?php else: ?>
                            <?php
                                $statusLabel = [
                                    'na cekanju' => 'Na čekanju',
                                    'odobreno' => 'Odobreno',
                                    'odbijeno' => 'Odbijeno'
                                ];
                                echo $statusLabel[$receipt['status']] ?? htmlspecialchars($receipt['status']);
                            ?>
                        <?php endif; ?>
                    </td>

                    <!-- ACTION -->
                    <td class="action-buttons">
                        <a class="edit" href="/admin/viewReceipt/<?= $receipt['id_receipt'] ?>">Prikaži</a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <?php if ($totalPages > 1): ?>
        <div class="pagination" id="receipt-pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php
                    $queryParams = ['page' => $p];
                    if (!empty($_GET['month'])) $queryParams['month'] = (int)$_GET['month'];
                    if (!empty($_GET['year'])) $queryParams['year'] = (int)$_GET['year'];
                    $queryStr = http_build_query($queryParams);
                ?>
                <?php if ($p == $currentPage): ?>
                    <strong><?= $p ?></strong>
                <?php else: ?>
                    <a href="/admin/receiptHistory?<?= $queryStr ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>


</body>
</html>
