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

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Check if email already exists
$sql_check = "SELECT * FROM USERS WHERE EMAIL = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "User email already exists";
    $stmt_check->close();
    $conn->close();
    exit();
}

// Validate password
if ($password === $confirm_password) {

    // Insert new user
    $date = date('Y-m-d H:i:s');

    $sql_user = "INSERT INTO USERS (USERNAME, EMAIL, PASSWORD, ROLE, CREATED_AT)
                 VALUES (?, ?, ?, ?, ?)";
    $stmt_user = $conn->prepare($sql_user);

    $role = "user";
    $stmt_user->bind_param("sssss", $name, $email, $password, $role, $date);

    if ($stmt_user->execute()) {
        header("Location: /IMS/index.html");
        exit();
    } else {
        echo "Error inserting user";
    }

    $stmt_user->close();

} else {
    echo "Password does not match";
}


$stmt_check->close();
$conn->close();
?>