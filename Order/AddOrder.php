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

/* ==========================
   GET FORM DATA
========================== */
$customerName = $_POST['customer_name'];
$orderDate = $_POST['order_date'] ?? date('Y-m-d');
$productId = $_POST['product_id'];
$quantity = (int) $_POST['quantity'];
$status = $_POST['status'];

/* ==========================
   CHECK AVAILABLE STOCK
========================== */
$stockSql = "SELECT QUANTITY, PRICE FROM PRODUCTS WHERE PRODUCT_ID = ?";
$stockStmt = sqlsrv_query($conn, $stockSql, [$productId]);

if ($stockStmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$product = sqlsrv_fetch_array($stockStmt, SQLSRV_FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

if ($quantity > $product['QUANTITY']) {
    header("Location: /IMS/Pages/orders.php?error=insufficient_stock");
    exit;
}


/* ==========================
   CALCULATE TOTAL (SERVER-SIDE)
========================== */
$totalAmount = $product['PRICE'] * $quantity;

/* ==========================
   INSERT ORDER
========================== */
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

/* ==========================
   REDUCE PRODUCT STOCK
========================== */
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

/* ==========================
   REDIRECT
========================== */
header("Location: /IMS/Pages/orders.php");
exit;
