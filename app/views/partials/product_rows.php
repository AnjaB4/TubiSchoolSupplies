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
