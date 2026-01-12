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

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];



// Check if email exists
$sql_check = "SELECT * FROM ACCOUNTS WHERE EMAIL = ?";
$result_check = sqlsrv_query($conn, $sql_check, [$email]);
$row = sqlsrv_fetch_array($result_check, SQLSRV_FETCH_ASSOC);
if ($row) {
    echo "User email already exists";
    sqlsrv_close($conn);
    die();
}

// Validation of password
if ($password == $confirm_password) {
    // Insert new user to database
    $date = date('Y-m-d H:i:s'); // format: 2025-01-30 14:23:10
    $sql_user = "INSERT INTO ACCOUNTS (NAME, EMAIL, PASSWORD, DATE_TIME) VALUES (?, ?, ?, ?)";
    $params = [$name, $email, $password, $date];
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


    header("Location: /IMS/Pages/index.php");
    exit();

} else {
    echo 'Password does not match';
    sqlsrv_close($conn);
    exit();
}
?>