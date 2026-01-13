<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "ims";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$id = $_GET['id'];

// Prepare delete query
$sql = "DELETE FROM PRODUCTS WHERE PRODUCT_ID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameter
$stmt->bind_param("i", $id);

// Execute
if (!$stmt->execute()) {
    die("Delete failed: " . $stmt->error);
}


header("Location: /IMS/Pages/products.php");
exit();


$stmt->close();
$conn->close();
?>