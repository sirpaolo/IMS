<?php
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
$category_name = $_POST['category_name'];
$category_id = $_POST['category_id'];

// Prepare update query
$sql = "UPDATE CATEGORIES SET CATEGORY_NAME = ? WHERE CATEGORY_ID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("si", $category_name, $category_id);

// Execute
if (!$stmt->execute()) {
    die("Update failed: " . $stmt->error);
}

// Redirect
header("Location: /IMS/Pages/category.php");
exit();

// Close
$stmt->close();
$conn->close();
?>