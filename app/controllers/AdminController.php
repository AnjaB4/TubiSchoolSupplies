<?php
// app/controllers/AdminController.php

class AdminController {

    public function __construct() {
        session_start();

        // Get current method from URL
        $url = $_GET['url'] ?? '';
        $parts = explode('/', trim($url, '/'));
        $method = $parts[1] ?? 'index';

        // If NOT trying to access the login method, do session check
        //if ($method !== 'login') {
         // Redirect unauthenticated admins
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            header("Location: /auth/adminLogin");
            exit;
        }
        //}
    }

    public function index() {
        //  admin dashboard page 
        require_once '../app/views/admin.php';
    }


    // --- PRODUCT STUFF ---

    public function manageProducts($page = 1) {
        require_once '../config/db.php';

        $productsPerPage = 20;
        $page = max((int)$page, 1);
        $offset = ($page - 1) * $productsPerPage;

        // Get total count for pagination
        $resultCount = $conn->query("SELECT COUNT(*) AS total FROM product");
        $totalProducts = $resultCount->fetch_assoc()['total'];
        $totalPages = ceil($totalProducts / $productsPerPage);

        // Fetch products for current page 
        $stmt = $conn->prepare("
            SELECT p.*, c.category_name AS category
            FROM product p
            JOIN category c ON p.id_category = c.id
            ORDER BY p.name COLLATE utf8mb4_unicode_520_ci
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $productsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        // Pass to the view
        require_once '../app/views/manage_products.php';
    }

    //dodavanje proizvoda
    public function addProduct() {
        require_once '../config/db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $name = $_POST['name'] ?? '';
            $id_category = $_POST['id_category'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            $unit_price = $_POST['unit_price'] ?? 0;
            $price_per_box = $_POST['price_per_box'] ?? 0;
            $description = $_POST['description'] ?? '';
            $available = isset($_POST['available']) ? (int)$_POST['available'] : 0;

            // Validate inputs

            // Prepare and execute insert query
            $stmt = $conn->prepare("INSERT INTO product (name, id_category, quantity, unit_price, price_per_box, description, available) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siiddsi", $name, $id_category, $quantity, $unit_price, $price_per_box, $description, $available);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header("Location: /admin/manageProducts"); // redirect after success
                exit;
            } else {
                $error = "Greška prilikom dodavanja proizvoda.";
            }
        }

        // categories for select dropdown
            $categories_result = $conn->query("SELECT id, category_name FROM category");

            require_once '../app/views/add_product.php';
    } 
    
    public function deleteProduct($id = null) {
        if (!$id) {
            echo "Product ID missing.";
            return;
        }

        require_once '../config/db.php';

        // Prepare and execute delete
        $stmt = $conn->prepare("DELETE FROM product WHERE id_product = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Redirect back to manage products page after deletion
        header("Location: /admin/manageProducts");
        exit;
    }

    public function editProduct($id = null) {
        if (!$id) {
            echo "Product ID missing.";
            return;
        }

        require_once '../config/db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          
            $name = $_POST['name'] ?? '';
            $id_category = $_POST['id_category'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            $unit_price = $_POST['unit_price'] ?? 0;
            $price_per_box = $_POST['price_per_box'] ?? 0;
            $description = $_POST['description'] ?? '';
            $available = isset($_POST['available']) ? (int)$_POST['available'] : 0;

            $stmt = $conn->prepare("UPDATE product SET name=?, id_category=?, quantity=?, unit_price=?, price_per_box=?, description=?, available=? WHERE id_product=?");
            $stmt->bind_param("siiddsii", $name, $id_category, $quantity, $unit_price, $price_per_box, $description, $available, $id);
            $stmt->execute();

            header("Location: /admin/manageProducts");
            exit;
        }

        //  product data and show form
        $stmt = $conn->prepare("SELECT * FROM product WHERE id_product = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            echo "Product not found.";
            return;
        }

        // categories for dropdown
        $categories_result = $conn->query("SELECT id, category_name FROM category");

        require_once '../app/views/edit_product.php';
    }

    public function productsPage($page = 1) {
        require_once '../config/db.php';

        $productsPerPage = 20;
        $page = max((int)$page, 1);
        $offset = ($page - 1) * $productsPerPage;

        $stmt = $conn->prepare("
            SELECT p.*, c.category_name AS category
            FROM product p
            JOIN category c ON p.id_category = c.id
            ORDER BY p.name COLLATE utf8mb4_unicode_520_ci
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $productsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        // Only output table rows 
        require '../app/views/partials/product_rows.php';
    }


    // --- RECEIPT STUFF ---

    public function createReceipt() {
        require_once '../config/db.php';

        // Re-query each time to get fresh results
        $users_result = $conn->query("SELECT id_user, username FROM user");

        $products_result = $conn->query("
            SELECT p.id_product, p.name, p.unit_price, p.price_per_box, c.category_name
            FROM product p
            JOIN category c ON p.id_category = c.id
            WHERE p.available > 0
        ");

        require_once '../app/views/create_receipt.php';
    }

    public function submitReceipt() {
        require_once '../config/db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_user']) && !empty($_POST['products'])) {
            $id_user = intval($_POST['id_user']);
            $products = $_POST['products'];

            $validProducts = [];
            foreach ($products as $id_product => $data) {
                if (isset($data['selected']) && !empty($data['quantity']) && is_numeric($data['quantity']) && intval($data['quantity']) > 0) {
                    $validProducts[intval($id_product)] = intval($data['quantity']);
                }
            }

            if (empty($validProducts)) {
                echo "No valid products selected.";
                return;
            }
            ////ako nesto ne valja, ovde je
            //Check if requested quantities exceed available stock
            $productIds = array_keys($validProducts);
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $types = str_repeat('i', count($productIds));

            $stmtCheck = $conn->prepare("SELECT id_product, available FROM product WHERE id_product IN ($placeholders)");
            $stmtCheck->bind_param($types, ...$productIds);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();

            while ($row = $resultCheck->fetch_assoc()) {
                $id = $row['id_product'];
                $available = (int)$row['available'];
                $requested = $validProducts[$id];

                if ($requested > $available) {
                    echo "Nema dovoljno na stanju za proizvod sa ID: $id. Dostupno: $available, traženo: $requested.";
                    return;
                }
            }
            $stmtCheck->close();
            ////

            // Step 0: Calculate total price
            $totalPrice = 0;
            $productIds = array_keys($validProducts);

            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $types = str_repeat('i', count($productIds));

            $stmtPrice = $conn->prepare("SELECT id_product, unit_price FROM product WHERE id_product IN ($placeholders)");
            $stmtPrice->bind_param($types, ...$productIds);
            $stmtPrice->execute();
            $priceResult = $stmtPrice->get_result();

            while ($row = $priceResult->fetch_assoc()) {
                $id = $row['id_product'];
                if (isset($validProducts[$id])) {
                    $qty = $validProducts[$id];
                    $subtotal = $qty * $row['unit_price'];
                    $totalPrice += $subtotal;
                }
            }
            $stmtPrice->close();

            // Step 1: Insert receipt WITH total_price
            $status = 'odobreno';
            $stmt = $conn->prepare("INSERT INTO receipt (id_user, total_price, date_created, status) VALUES (?, ?, NOW(), ?)");
            $stmt->bind_param("ids", $id_user, $totalPrice, $status);

            if ($stmt->execute()) {
                $id_receipt = $stmt->insert_id;
                $stmt->close();

                // Step 2: Generate receipt number and update the row
                $yearSuffix = date('y');
                $receiptNumber = $id_receipt . '/' . $yearSuffix;

                $stmtUpdate = $conn->prepare("UPDATE receipt SET receipt_number = ? WHERE id_receipt = ?");
                $stmtUpdate->bind_param("si", $receiptNumber, $id_receipt);
                $stmtUpdate->execute();
                $stmtUpdate->close();

                // Step 3: Insert products
                $stmtProd = $conn->prepare("INSERT INTO receipt_product (id_receipt, id_product, quantity) VALUES (?, ?, ?)");
                foreach ($validProducts as $productId => $quantity) {
                    $stmtProd->bind_param("iii", $id_receipt, $productId, $quantity);
                    $stmtProd->execute();
                }
                $stmtProd->close();

                ///ako nesto ne valja, ovde je
                // Step 3.5: Subtract availability (for admin orders)
                $stmtUpdateStock = $conn->prepare("UPDATE product SET available = available - ? WHERE id_product = ?");
                foreach ($validProducts as $productId => $quantity) {
                    $stmtUpdateStock->bind_param("ii", $quantity, $productId);
                    $stmtUpdateStock->execute();
                }
                $stmtUpdateStock->close();
                ///

                // Step 4: Redirect
                $_SESSION['message'] = "Porudžbina je uspešno kreirana!";
                header("Location: /admin/viewReceipt/$id_receipt");
                exit;
            } else {
                echo "Failed to create receipt.";
            }

        } else {
            echo "Invalid request.";
        }
    }

    public function receiptConfirmation() {
        session_start();
        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']); // Clear message after showing

        require_once '../app/views/receipt_confirmation.php';
    }

    public function viewReceipt($id_receipt) {
        require_once '../config/db.php';

        // Get receipt and user
        $stmt = $conn->prepare("
            SELECT r.id_receipt, r.date_created, u.username, u.school_name
            FROM receipt r
            JOIN user u ON r.id_user = u.id_user
            WHERE r.id_receipt = ?
        ");
        $stmt->bind_param("i", $id_receipt);
        $stmt->execute();
        $receipt = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$receipt) {
            echo "Receipt not found.";
            return;
        }

        // Get receipt products
        $products = [];
        $stmt = $conn->prepare("
            SELECT p.name, rp.quantity, p.unit_price, p.price_per_box, c.category_name
            FROM receipt_product rp
            JOIN product p ON rp.id_product = p.id_product
            JOIN category c ON p.id_category = c.id
            WHERE rp.id_receipt = ?
        ");
        $stmt->bind_param("i", $id_receipt);
        $stmt->execute();
        $products_result = $stmt->get_result();
        while ($row = $products_result->fetch_assoc()) {
            $products[] = $row;
        }

        require_once '../app/views/receipt.php';
    }

    public function receiptHistory() {
        require_once '../config/db.php';

        // Pagination setup
        $receiptsPerPage = 10;
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $offset = ($page - 1) * $receiptsPerPage;

        // Filters
        $month = $_GET['month'] ?? null;
        $year = $_GET['year'] ?? null;

        $query = "SELECT r.*, u.school_name 
                FROM receipt r 
                JOIN user u ON r.id_user = u.id_user";
        $countQuery = "SELECT COUNT(*) as total 
                    FROM receipt r 
                    JOIN user u ON r.id_user = u.id_user";

        $where = [];
        $params = [];
        $types = '';

        // filters
        if ($month && $year) {
            $where[] = "MONTH(r.date_created) = ?";
            $where[] = "YEAR(r.date_created) = ?";
            $params[] = (int)$month;
            $params[] = (int)$year;
            $types .= 'ii';
        } elseif ($year) {
            $where[] = "YEAR(r.date_created) = ?";
            $params[] = (int)$year;
            $types .= 'i';
        }

        if (!empty($where)) {
            $whereSql = " WHERE " . implode(" AND ", $where);
            $query .= $whereSql;
            $countQuery .= $whereSql;
        }

        $stmtCount = $conn->prepare($countQuery);
        if (!empty($types)) {
            $stmtCount->bind_param($types, ...$params);
        }
        $stmtCount->execute();
        $totalCount = $stmtCount->get_result()->fetch_assoc()['total'];
        $stmtCount->close();

        $totalPages = ceil($totalCount / $receiptsPerPage);

        $query .= " ORDER BY r.date_created DESC LIMIT ? OFFSET ?";
        $typesMain = $types . "ii";
        $paramsMain = $params;
        $paramsMain[] = $receiptsPerPage;
        $paramsMain[] = $offset;

        $stmt = $conn->prepare($query);
        $stmt->bind_param($typesMain, ...$paramsMain);
        $stmt->execute();
        $result = $stmt->get_result();
        $receipts = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $currentPage = $page; // Send to view

        require_once '../app/views/receipt_history.php';
    }

    public function updateReceiptStatus($id_receipt, $newStatus) {
        require_once '../config/db.php';

        // Step 0: Get current status
        $stmtCheck = $conn->prepare("SELECT status FROM receipt WHERE id_receipt = ?");
        $stmtCheck->bind_param("i", $id_receipt);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $currentStatus = $result->fetch_assoc()['status'] ?? null;
        $stmtCheck->close();

        // Step 1: If changing to 'odobreno' and it wasn't already 'odobreno', reduce stock
        if ($newStatus === 'odobreno' && $currentStatus !== 'odobreno') {
            $stmt = $conn->prepare("
                SELECT id_product, quantity 
                FROM receipt_product 
                WHERE id_receipt = ?
            ");
            $stmt->bind_param("i", $id_receipt);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $productId = $row['id_product'];
                $qty = $row['quantity'];

                $updateStmt = $conn->prepare("
                    UPDATE product 
                    SET available = available - ? 
                    WHERE id_product = ?
                ");
                $updateStmt->bind_param("ii", $qty, $productId);
                $updateStmt->execute();
                $updateStmt->close();
            }

            $stmt->close();
        }

        // Step 2:  update status
        $stmt = $conn->prepare("UPDATE receipt SET status = ? WHERE id_receipt = ?");
        $stmt->bind_param("si", $newStatus, $id_receipt);
        $stmt->execute();

        // Step 3: Preserve filters and redirect
        $queryParams = [];
        if (!empty($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $queryParams);
            unset($queryParams['url']);
        }

        $query = http_build_query($queryParams);
        $query = $query ? '?' . $query : '';

        header("Location: /admin/receiptHistory" . $query);
        exit;
    }


    public function approveReceipt($id_receipt) {
        $this->updateReceiptStatus($id_receipt, 'odobreno');
    }

    public function rejectReceipt($id_receipt) {
        $this->updateReceiptStatus($id_receipt, 'odbijeno');
    }

// --- USERS STUFF ---

    public function manageUsers() {
        require_once '../config/db.php';

        $result = $conn->query("SELECT * FROM user ORDER BY created_at DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $conn->close();

        require_once '../app/views/admin_manage_users.php';
    }

    public function editUser($id_user = null) {
        if (!$id_user) {
            echo "User ID missing.";
            return;
        }

        require_once '../config/db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $school_name = $_POST['school_name'] ?? '';
            $school_address = $_POST['school_address'] ?? '';
            $district = $_POST['district'] ?? '';
            $city = $_POST['city'] ?? '';

            $stmt = $conn->prepare("UPDATE user SET username=?, email=?, school_name=?, school_address=?, district=?, city=? WHERE id_user=?");
            $stmt->bind_param("ssssssi", $username, $email, $school_name, $school_address, $district, $city, $id_user);
            $stmt->execute();

            header("Location: /admin/manageUsers");
            exit;
        }

        // For GET request: fetch user data
        $stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            echo "User not found.";
            return;
        }

        require_once '../app/views/admin_edit_user.php';
    }

    public function deleteUser($id_user = null) {
        if (!$id_user) {
            echo "User ID missing.";
            return;
        }

        require_once '../config/db.php';

        $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();

        header("Location: /admin/manageUsers");
        exit;
    }

    // --- LOGOUT ---
    public function logout() {
        //session_start();
        $_SESSION = [];
        session_destroy();

        header("Location: /admin/login");
        exit;
    }
}
