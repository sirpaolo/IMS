<?php
$conn = sqlsrv_connect("HELIOS", ["Database" => "IMS", "Uid" => "", "PWD" => ""]);
$id = $_GET['id'];

sqlsrv_query($conn, "DELETE FROM ORDERS WHERE ORDER_ID = ?", [$id]);

header("Location: /IMS/Pages/orders.php");
exit;
