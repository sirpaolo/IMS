<?php
// ============================
// DATABASE CONNECTION (MySQL)
// ============================
$conn = new mysqli("localhost", "root", "", "IMS");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/* ==========================
   GET FORM DATA
========================== */
$customerName = $_POST['customer_name'];
$orderDate = $_POST['order_date'] ?? date('Y-m-d');
$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$status = $_POST['status'];

if (!$productId || !$quantity) {
    die("Invalid product or quantity.");
}

/* ==========================
   CHECK AVAILABLE STOCK
========================== */
$stockSql = "SELECT QUANTITY, PRICE FROM PRODUCTS WHERE PRODUCT_ID = ?";
$stockStmt = $conn->prepare($stockSql);
$stockStmt->bind_param("i", $productId);
$stockStmt->execute();
$stockResult = $stockStmt->get_result();

if ($stockResult->num_rows === 0) {
    die("Product not found.");
}

$product = $stockResult->fetch_assoc();

if ($quantity > $product['QUANTITY']) {
    header("Location: /IMS/Pages/orders.php?error=insufficient_stock");
    exit;
}

/* ==========================
   CALCULATE TOTAL
========================== */
$totalAmount = $product['PRICE'] * $quantity;

/* ==========================
   START TRANSACTION
========================== */
$conn->begin_transaction();

/* ==========================
   INSERT ORDER
========================== */
$insertSql = "
    INSERT INTO ORDERS 
        (CUSTOMER_NAME, ORDER_DATE, PRODUCT_ID, QUANTITY, TOTAL_AMOUNT, STATUS)
    VALUES (?, ?, ?, ?, ?, ?)
";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param(
    "ssidis",
    $customerName,
    $orderDate,
    $productId,
    $quantity,
    $totalAmount,
    $status
);

if (!$insertStmt->execute()) {
    $conn->rollback();
    die("Failed to insert order.");
}

/* ==========================
   REDUCE PRODUCT STOCK
========================== */
$updateStockSql = "
    UPDATE PRODUCTS
    SET QUANTITY = QUANTITY - ?
    WHERE PRODUCT_ID = ?
";
$updateStmt = $conn->prepare($updateStockSql);
$updateStmt->bind_param("ii", $quantity, $productId);

if (!$updateStmt->execute()) {
    $conn->rollback();
    die("Failed to update stock.");
}

/* ==========================
   COMMIT TRANSACTION
========================== */
$conn->commit();

/* ==========================
   REDIRECT
========================== */
header("Location: /IMS/Pages/orders.php");
exit;
