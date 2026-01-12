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


// Get product ID
$id = $_POST['product_id'];

// Get form data
$name = $_POST['name'];
$category = $_POST['category'];
$description = $_POST['description'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];


// Fetch product
$sql = "UPDATE PRODUCTS
        SET 
            NAME = ?, 
            CATEGORY = ?, 
            DESCRIPTION = ?, 
            QUANTITY = ?, 
            PRICE = ?
        WHERE PRODUCT_ID = ?
        ";

$params = [
    $name,
    $category,
    $description,
    $quantity,
    $price,
    $id
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

header("Location: /IMS/Pages/products.php");
exit();
?>