<?php
// DB CONNECTION
$serverName = "HELIOS";
$connectionOptions = [
    "Database" => "IMS",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}


$customerName = $_POST['customer_name'];
$orderDate = $_POST['order_date'] ?? date('Y-m-d');
$productId = $_POST['product_id'];
$quantity = (int) $_POST['quantity'];
$totalAmount = $_POST['total_amount'];
$status = $_POST['status'];

// 1️⃣ INSERT ORDER
$sql = "
        INSERT INTO ORDERS (CUSTOMER_NAME, ORDER_DATE, PRODUCT_ID, QUANTITY, TOTAL_AMOUNT, STATUS)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

$params = [
    $customerName,
    $orderDate,
    $productId,
    $quantity,
    $totalAmount,
    $status
];

$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// 2️⃣ REDUCE PRODUCT STOCK (IMPORTANT)
$updateStockSql = "
        UPDATE PRODUCTS
        SET QUANTITY = QUANTITY - ?
        WHERE PRODUCT_ID = ?
    ";

$stockParams = [$quantity, $productId];
$updateStmt = sqlsrv_query($conn, $updateStockSql, $stockParams);

if ($updateStmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// 3️⃣ REDIRECT
header("Location: /IMS/Pages/orders.php");
exit;

