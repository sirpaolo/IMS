<?php
// ============================
// DATABASE CONNECTION
// ============================
$conn = new mysqli("localhost", "root", "", "IMS");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ============================
// GET ORDER ID
// ============================
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$order_id) {
    die("Invalid order ID.");
}

// ============================
// DELETE ORDER
// ============================
$stmt = $conn->prepare("DELETE FROM ORDERS WHERE ORDER_ID = ?");
$stmt->bind_param("i", $order_id);

if (!$stmt->execute()) {
    die("Failed to delete order.");
}

// ============================
// REDIRECT
// ============================
header("Location: /IMS/Pages/orders.php");
exit;
