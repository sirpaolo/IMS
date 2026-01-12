<?php

// Database connection
$conn = new mysqli("localhost", "root", "", "IMS");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];


// CHECK IF EMAIL EXISTS
$checkSql = "SELECT 1 FROM USERS WHERE EMAIL = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo "User email already exists";
    exit();
}


// INSERT NEW USER
$date = date('Y-m-d H:i:s');

$insertSql = "
    INSERT INTO USERS (USERNAME, EMAIL, PASSWORD, ROLE, CREATED_AT)
    VALUES (?, ?, ?, ?, ?)
";

$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param(
    "sssss",
    $name,
    $email,
    $password,
    $role,
    $date
);

if (!$insertStmt->execute()) {
    die("Error inserting user");
}

// Redirect
header("Location: /IMS/Pages/users.php");
exit();
