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

// Prepare insert query
$sql = "INSERT INTO CATEGORIES (CATEGORY_NAME) VALUES (?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameter
$stmt->bind_param("s", $_POST['category_name']);

// Execute
if (!$stmt->execute()) {
    die("Insert failed: " . $stmt->error);
}

// Redirect
header("Location: /IMS/Pages/category.php");
exit();

// Close
$stmt->close();
$conn->close();
?>