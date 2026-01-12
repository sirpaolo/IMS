<?php

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

$email = $_POST['email'];
$password = $_POST['password'];

// Find account by email only
$sql = "SELECT * FROM ACCOUNTS WHERE EMAIL = ?";
$result_check = sqlsrv_query($conn, $sql, [$email]);
if ($result_check === false) {
    sqlsrv_close($conn);
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($result_check, SQLSRV_FETCH_ASSOC);

if (!$row) {
    echo "Email not found";
    sqlsrv_close($conn);
    die();
}

// check password and set session
if ($password == $row['PASSWORD']) {

    header("Location: /IMS/Pages/dashboard.php");
    exit();

} else {
    echo "Incorrect password";
    sqlsrv_close($conn);
    exit();
}
?>