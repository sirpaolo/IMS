<?php
$conn = sqlsrv_connect("HELIOS", ["Database" => "IMS", "Uid" => "", "PWD" => ""]);
$sql = "INSERT INTO CATEGORIES (CATEGORY_NAME) VALUES (?)";
sqlsrv_query($conn, $sql, [$_POST['category_name']]);
header("Location: /IMS/Pages/category.php");
exit;
