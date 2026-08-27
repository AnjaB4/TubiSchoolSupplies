<?php
// app/controllers/UserController.php

class UserController {
    public function __construct() {
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION['id_user'])) {
            // Not logged in as user, redirect to login page
            header("Location: /auth/login");//!!!!!
            exit;
        }
    }

    public function index() {
        include_once '../config/db.php';
        require_once '../app/models/User.php';

        $userModel = new User($conn);
        $user = $userModel->getUserById($_SESSION['id_user']);

        $conn->close();

        // Pass user data to the view
        require_once '../app/views/user.php';
    }


    public function profile() {
        // User profile page logic here
        require_once '../app/views/profile.php';
    }

    public function products($page = 1) {
        require_once '../config/db.php';

        $productsPerPage = 20;
        $page = max((int)$page, 1);
        $offset = ($page - 1) * $productsPerPage;

        $stmt = $conn->prepare("
            SELECT p.*, c.category_name AS category
            FROM product p
            JOIN category c ON p.id_category = c.id
            WHERE p.available > 0
            ORDER BY p.name COLLATE utf8mb4_unicode_520_ci
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $productsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        // Total products for pagination
        $countRes = $conn->query("SELECT COUNT(*) AS total FROM product WHERE available > 0");
        $totalProducts = $countRes->fetch_assoc()['total'];
        $totalPages = ceil($totalProducts / $productsPerPage);

        require_once '../app/views/user_products.php';
    }

    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['products'])) {
            $selected = [];

            foreach ($_POST['products'] as $id_product => $data) {
                if (isset($data['selected']) && is_numeric($data['quantity']) && $data['quantity'] > 0) {
                    $selected[$id_product] = (int)$data['quantity'];
                }
            }

            if (empty($selected)) {
                header("Location: /user/products?error=no-selection");
                exit;
            }

            $_SESSION['cart'] = $selected;
            header("Location: /user/reviewCart");
            exit;
        } else {
            echo "Invalid request.";
        }
    }

    public function reviewCart() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: /user/products");
            exit;
        }

        require_once '../config/db.php';

        $productIds = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $types = str_repeat('i', count($productIds));

        $stmt = $conn->prepare("
            SELECT id_product, name, unit_price 
            FROM product 
            WHERE id_product IN ($placeholders)
        ");

        $stmt->bind_param($types, ...$productIds);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        $total = 0;

        while ($row = $result->fetch_assoc()) {
            $id = $row['id_product'];
            $qty = $_SESSION['cart'][$id];
            $subtotal = $qty * $row['unit_price'];
            $total += $subtotal;

            $products[] = [
                'id' => $id,
                'name' => $row['name'],
                'unit_price' => $row['unit_price'],
                'quantity' => $qty,
                'subtotal' => $subtotal,
            ];
        }

        require_once '../app/views/review_cart.php';
    }

    public function confirmOrder() {

        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: /user/products");
            exit;
        }

        require_once '../config/db.php';

        $id_user = $_SESSION['id_user'];
        $cart = $_SESSION['cart'];

       // 0. Calculate total price
        $totalPrice = 0;

        $productIds = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $types = str_repeat('i', count($productIds));
        $stmt = $conn->prepare("SELECT id_product, unit_price FROM product WHERE id_product IN ($placeholders)");
        $stmt->bind_param($types, ...$productIds);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $id = $row['id_product'];
            $qty = $cart[$id];
            $subtotal = $qty * $row['unit_price'];
            $totalPrice += $subtotal;
        }
        $stmt->close();

        // 1. Insert new receipt with total price
        $stmt = $conn->prepare("INSERT INTO receipt (id_user, total_price, date_created, status) VALUES (?, ?, NOW(), 'na cekanju')");
        $stmt->bind_param("id", $id_user, $totalPrice);
        $stmt->execute();
        $id_receipt = $stmt->insert_id;
        $stmt->close();


        // 2. Set receipt number (format: <id>/<year>)
        $receiptNumber = $id_receipt . '/' . date('y');
        $stmt = $conn->prepare("UPDATE receipt SET receipt_number = ? WHERE id_receipt = ?");
        $stmt->bind_param("si", $receiptNumber, $id_receipt);
        $stmt->execute();
        $stmt->close();

        // 3. Add products to receipt_product
        $stmt = $conn->prepare("INSERT INTO receipt_product (id_receipt, id_product, quantity) VALUES (?, ?, ?)");
        foreach ($cart as $id_product => $qty) {
            $stmt->bind_param("iii", $id_receipt, $id_product, $qty);
            $stmt->execute();
        }
        $stmt->close();

        // 4. Clear the cart session
        unset($_SESSION['cart']);

        // 5. Redirect to order history
        //header("Location: /user/orders");
        header("Location: /user/orders?success=1");
        exit;
    }

//if something breaks, change back date to just DATE not datetime
// or remove filters
    public function orders() {
        require_once '../config/db.php';

        $id_user = $_SESSION['id_user'];

        $month = isset($_GET['month']) ? (int)$_GET['month'] : null;
        $year = isset($_GET['year']) ? (int)$_GET['year'] : null;

        $query = "
            SELECT r.*, GROUP_CONCAT(p.name, ' (', rp.quantity, ')') AS products
            FROM receipt r
            JOIN receipt_product rp ON r.id_receipt = rp.id_receipt
            JOIN product p ON rp.id_product = p.id_product
            WHERE r.id_user = ?
        ";

        $params = [$id_user];
        $types = "i";

        if ($month) {
            $query .= " AND MONTH(r.date_created) = ?";
            $params[] = $month;
            $types .= "i";
        }

        if ($year) {
            $query .= " AND YEAR(r.date_created) = ?";
            $params[] = $year;
            $types .= "i";
        }

        $query .= " GROUP BY r.id_receipt ORDER BY r.date_created DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $receipts = [];
        while ($row = $result->fetch_assoc()) {
            $receipts[] = $row;
        }

        $stmt->close();
        $conn->close();

        require_once '../app/views/user_orders.php';
    }


    public function viewReceipt($id_receipt) {
        require_once '../config/db.php';

        $id_user = $_SESSION['id_user'];

        // Step 1: Verify the receipt belongs to the logged-in user
        $stmt = $conn->prepare("
            SELECT r.*, u.school_name
            FROM receipt r
            JOIN user u ON r.id_user = u.id_user
            WHERE r.id_receipt = ? AND r.id_user = ?
        ");
        $stmt->bind_param("ii", $id_receipt, $id_user);
        $stmt->execute();
        $receiptResult = $stmt->get_result();

        if ($receiptResult->num_rows === 0) {
            echo "Otpremnica nije pronađena.";
            exit;
        }

        $receipt = $receiptResult->fetch_assoc();
        $stmt->close();

        // Step 2: Fetch products for this receipt
        $stmtProd = $conn->prepare("
            SELECT p.name, rp.quantity, p.unit_price, (rp.quantity * p.unit_price) AS subtotal
            FROM receipt_product rp
            JOIN product p ON rp.id_product = p.id_product
            WHERE rp.id_receipt = ?
        ");
        $stmtProd->bind_param("i", $id_receipt);
        $stmtProd->execute();
        $productsResult = $stmtProd->get_result();

        $products = [];
        while ($row = $productsResult->fetch_assoc()) {
            $products[] = $row;
        }

        $stmtProd->close();
        $conn->close();

        require_once '../app/views/receipt.php'; // reuse the same view as admin
    }



    public function logout() {
        // Clear user session data
        $_SESSION = [];
        session_destroy();

        header("Location: /auth/login");//!!!!
        exit;
    }
}
