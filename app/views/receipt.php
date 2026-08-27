<?php
$titleNumber = $receipt['id_receipt'];
$titleYear = date('y', strtotime($receipt['date_created']));
$receiptDate = date('d.m.Y', strtotime($receipt['date_created']));
$receiptCode = "{$titleNumber}/{$titleYear}";

$total = 0;
?>

<!--AKO IMA PROBBLEMA, a za nazad i a za kreiraj-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Otpremnica #<?= $receiptCode ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="receipt-body">
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="popup-message"><?= $_SESSION['message']; ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php
        $isAdmin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
    ?>
    <a class="back-btn" href="<?= $isAdmin ? '/admin/receiptHistory' : '/user/orders' ?>">Nazad</a>
    <!--
    <a class="back-btn" href="/admin/receiptHistory">Istorija</a>
    -->
    <div class="receipt-header">
        <div class="receipt-flex-header">
            <div>Naziv pošiljaoca: <strong>Zoki</strong></div>
            <div>Naziv primaoca: <strong><?= htmlspecialchars($receipt['school_name']) ?></strong></div>
        </div>
        <div>OTPREMNICA br. <?= $receiptCode ?> &nbsp;&nbsp;&nbsp; Dana: <?= $receiptDate ?></div>
    </div>

    <table class="receipt-table">
        <tr>
            <th>Redni broj</th>
            <th>Naziv dobra</th>
            <th>Količina</th>
            <th>Cena</th>
            <th>Iznos</th>
        </tr>
        <?php foreach ($products as $i => $product): 
            $isCokolada = isset($product['category_name']) && $product['category_name'] === 'Čokolade';
////
            $unitPrice = $isCokolada ? $product['price_per_box'] : $product['unit_price'];
            $amount = $unitPrice * $product['quantity'];
            $total += $amount;
        ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= $product['quantity'] ?></td>
            <td><?= number_format($unitPrice, 2) ?> RSD</td>
            <td><?= number_format($amount, 2) ?> RSD</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="receipt-total-box">
        Ukupan iznos: <strong><?= number_format($total, 2) ?> RSD</strong>
    </div>

    <div class="receipt-signature">
        Primio: _______________________________________
    </div>

    <div class="receipt-button-box">
        <button onclick="window.print()" class="add-product-btn">Štampaj</button>
        <?php if ($isAdmin): ?>
            <a href="/admin/createReceipt"><button class="back-btn">Kreiraj novu porudžbinu</button></a>
        <?php endif; ?>

        <!--
        <a href="/admin/createReceipt"><button class="back-btn">Kreiraj novu porudžbinu</button></a>
        -->
    </div>
</body>
</html>
