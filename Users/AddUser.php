<?php
// regis.php 
$serverName = "HELIOS";
$connectionOptions = [
    "Database" => "IMS",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

$name = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];



// Check if email exists
$sql_check = "SELECT * FROM USERS WHERE EMAIL = ?";
$result_check = sqlsrv_query($conn, $sql_check, [$email]);
$row = sqlsrv_fetch_array($result_check, SQLSRV_FETCH_ASSOC);
if ($row) {
    echo "User email already exists";
    sqlsrv_close($conn);
    die();
}


// Insert new user to database
$date = date('Y-m-d H:i:s'); // format: 2025-01-30 14:23:10
$sql_user = "INSERT INTO USERS (USERNAME, EMAIL, PASSWORD, ROLE, CREATED_AT) VALUES (?, ?, ?, ?, ?)";
$params = [$name, $email, $password, $role, $date];
$result_user = sqlsrv_query($conn, $sql_user, $params);

if ($result_user === false) {
    echo "Error inserting user";
    sqlsrv_close($conn);
    die(print_r(sqlsrv_errors(), true));
}

// // Retrieve the newly inserted user's row (by email)
// $sql_get_user = "SELECT TOP 1 CUSTOMER_ID, NAME, EMAIL FROM ACCOUNTS WHERE EMAIL = ? ORDER BY CUSTOMER_ID DESC";
// $result_get_user = sqlsrv_query($conn, $sql_get_user, [$email]);
// if ($result_get_user === false) {
//     sqlsrv_close($conn);
//     die(print_r(sqlsrv_errors(), true));
// }
// $user_row = sqlsrv_fetch_array($result_get_user, SQLSRV_FETCH_ASSOC);

// if (!$user_row) {
//     sqlsrv_close($conn);
//     die("Failed to retrieve newly created user.");
// }

// sqlsrv_close($conn);


header("Location: /IMS/Pages/users.php");
exit();

?>