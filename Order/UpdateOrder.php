<?php
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

$order_id = $_POST['order_id'];
$customer_name = $_POST['customer_name'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];
$status = $_POST['status'];


/* ==========================
   Lock Completed Orders
========================== */
$check = sqlsrv_query(
    $conn,
    "SELECT STATUS FROM ORDERS WHERE ORDER_ID = ?",
    [$order_id]
);
$order = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);

if ($order['STATUS'] === 'Completed') {
    header("Location: /IMS/Pages/order.php?locked=1");
    exit();
}


/* ==========================
   Get Product Price
========================== */
$priceStmt = sqlsrv_query(
    $conn,
    "SELECT PRICE FROM PRODUCTS WHERE PRODUCT_ID = ?",
    [$product_id]
);
$product = sqlsrv_fetch_array($priceStmt, SQLSRV_FETCH_ASSOC);
$price = $product['PRICE'];

$total = $price * $quantity;

/* ==========================
   Update Order
========================== */
$sql = "UPDATE ORDERS
        SET CUSTOMER_NAME = ?, PRODUCT_ID = ?, QUANTITY = ?, TOTAL_AMOUNT = ?, STATUS = ?
        WHERE ORDER_ID = ?";

$params = [$customer_name, $product_id, $quantity, $total, $status, $order_id];

sqlsrv_query($conn, $sql, $params);

header("Location: /IMS/Pages/orders.php");
exit();
?>