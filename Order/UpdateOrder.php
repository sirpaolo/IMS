<?php
// ============================
// DATABASE CONNECTION
// ============================
$conn = new mysqli("localhost", "root", "", "IMS");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ============================
// GET FORM DATA
// ============================
$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$customer_name = $_POST['customer_name'];
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$status = $_POST['status'];

if (!$order_id || !$product_id || !$quantity) {
    die("Invalid input.");
}

/* ==========================
   LOCK COMPLETED ORDERS
========================== */
$checkStmt = $conn->prepare(
    "SELECT STATUS FROM ORDERS WHERE ORDER_ID = ?"
);
$checkStmt->bind_param("i", $order_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    die("Order not found.");
}

$order = $checkResult->fetch_assoc();

if ($order['STATUS'] === 'Completed') {
    header("Location: /IMS/Pages/order.php?locked=1");
    exit();
}

/* ==========================
   GET PRODUCT PRICE
========================== */
$priceStmt = $conn->prepare(
    "SELECT PRICE FROM PRODUCTS WHERE PRODUCT_ID = ?"
);
$priceStmt->bind_param("i", $product_id);
$priceStmt->execute();
$priceResult = $priceStmt->get_result();

if ($priceResult->num_rows === 0) {
    die("Product not found.");
}

$product = $priceResult->fetch_assoc();
$price = $product['PRICE'];

$total = $price * $quantity;

/* ==========================
   UPDATE ORDER
========================== */
$updateStmt = $conn->prepare("
    UPDATE ORDERS
    SET 
        CUSTOMER_NAME = ?, 
        PRODUCT_ID = ?, 
        QUANTITY = ?, 
        TOTAL_AMOUNT = ?, 
        STATUS = ?
    WHERE ORDER_ID = ?
");

$updateStmt->bind_param(
    "siidsi",
    $customer_name,
    $product_id,
    $quantity,
    $total,
    $status,
    $order_id
);

if (!$updateStmt->execute()) {
    die("Failed to update order.");
}

// ============================
// REDIRECT
// ============================
header("Location: /IMS/Pages/orders.php");
exit();
