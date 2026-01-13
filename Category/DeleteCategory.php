<?php
// MySQL connection
$host = "localhost";
$user = "ims_user";
$pass = "12345Admin";
$db = "ims";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get category ID
$id = $_GET['id'];

// Prepare delete query
$sql = "DELETE FROM CATEGORIES WHERE CATEGORY_ID = ?";
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

// Redirect back
header("Location: /IMS/Pages/category.php");
exit();

// Close connections
$stmt->close();
$conn->close();
?>