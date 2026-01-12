<?php
$conn = sqlsrv_connect("HELIOS", ["Database" => "IMS", "Uid" => "", "PWD" => ""]);
$sql = "UPDATE CATEGORIES SET CATEGORY_NAME = ? WHERE CATEGORY_ID = ?";
sqlsrv_query($conn, $sql, [$_POST['category_name'], $_POST['category_id']]);
header("Location: /IMS/Pages/category.php");
exit;
