<?php
// DB CONNECTION
$serverName = "HELIOS";
$connectionOptions = [
    "Database" => "IMS",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}



$user_id = $_POST['user_id'];
$username = $_POST['username'];
$email = $_POST['email'];
$role = $_POST['role'];

// BASIC VALIDATION
if (empty($user_id) || empty($username) || empty($email) || empty($role)) {
    die("All fields are required.");
}

// UPDATE QUERY
$sql = "UPDATE USERS
            SET USERNAME = ?, EMAIL = ?, ROLE = ?
            WHERE USER_ID = ?";

$params = [$username, $email, $role, $user_id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// SUCCESS → REDIRECT
header("Location: /IMS/Pages/users.php?updated=1");
exit;
