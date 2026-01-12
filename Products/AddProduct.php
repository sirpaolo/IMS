<?php
// Addproduct.php 
$serverName = "HELIOS";
$connectionOptions = [
    "Database" => "IMS",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

$name = $_POST['name'];
$category = $_POST['category'];
$description = $_POST['description'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];



$sql = "INSERT INTO PRODUCTS(NAME, CATEGORY, DESCRIPTION, QUANTITY, PRICE) 
    VALUES('$name', '$category', '$description', '$quantity', '$price')";

$result = sqlsrv_query($conn, $sql);

if ($result) {
    header("Location: /IMS/Pages/products.php");
    exit();
} else {
    echo "Insert Error";
}

?>