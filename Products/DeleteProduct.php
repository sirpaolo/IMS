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

// Get product ID from URL
$id = $_GET['id'];

// Delete product
$sql = "DELETE FROM PRODUCTS WHERE PRODUCT_ID = ?";
$params = [$id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Redirect back
header("Location: /IMS/Pages/products.php");
exit;
?>