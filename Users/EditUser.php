<?php

$conn = new mysqli("localhost", "root", "", "IMS");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// GET FORM DATA
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$username = $_POST['username'];
$email = $_POST['email'];
$role = $_POST['role'];


// BASIC VALIDATION
if (!$user_id || empty($username) || empty($email) || empty($role)) {
    die("All fields are required.");
}

// UPDATE USER
$sql = "
    UPDATE USERS
    SET USERNAME = ?, EMAIL = ?, ROLE = ?
    WHERE USER_ID = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "sssi",
    $username,
    $email,
    $role,
    $user_id
);

if (!$stmt->execute()) {
    die("Update failed: " . $stmt->error);
}


// REDIRECT
header("Location: /IMS/Pages/users.php?updated=1");
exit;
