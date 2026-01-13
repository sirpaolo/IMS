<?php

$host = "localhost";
$user = "ims_user";
$pass = "12345Admin";
$db = "ims";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get product ID
$id = $_POST['product_id'];

// Get form data
$name = $_POST['name'];
$category_id = $_POST['category_id'];
$description = $_POST['description'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];

// Prepare update query
$sql = "
    UPDATE PRODUCTS
    SET 
        NAME = ?, 
        CATEGORY_ID = ?, 
        DESCRIPTION = ?, 
        QUANTITY = ?, 
        PRICE = ?
    WHERE PRODUCT_ID = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param(
    "sisidi",
    $name,        // s
    $category_id, // i
    $description, // s
    $quantity,    // i
    $price,       // d
    $id           // i
);

// Execute
if (!$stmt->execute()) {
    die("Update failed: " . $stmt->error);
}

// Redirect back
header("Location: /IMS/Pages/products.php");
exit();

// Close connections
$stmt->close();
$conn->close();
?>