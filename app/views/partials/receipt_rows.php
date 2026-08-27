<?php foreach ($receipts as $receipt): ?>
<tr>
    <td><?= $receipt['id_receipt'] ?></td>
    <td><?= date('d.m.Y', strtotime($receipt['date_created'])) ?></td>
    <td><?= htmlspecialchars($receipt['school_name']) ?></td>
    <td><?= $receipt['receipt_number'] ?></td>
    <td class="action-buttons">
        <a class="edit" href="/admin/viewReceipt/<?= $receipt['id_receipt'] ?>">Prikaži</a>
    </td>
</tr>
<?php endforeach; ?>
