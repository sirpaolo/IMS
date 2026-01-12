<?php
// Addproduct.php (MySQL version)

// MySQL connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ims"; // database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$category_id = $_POST['category_id'];
$description = $_POST['description'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];

// Prepare SQL
$sql = "
    INSERT INTO PRODUCTS (NAME, CATEGORY_ID, DESCRIPTION, QUANTITY, PRICE)
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param(
    "sisid",
    $name,        // s = string
    $category_id, // i = integer
    $description, // s = string
    $quantity,    // i = integer
    $price        // d = double
);

// Execute
if ($stmt->execute()) {
    header("Location: /IMS/Pages/products.php");
    exit();
} else {
    echo "Insert Error";
}

// Close connections
$stmt->close();
$conn->close();
?>