<?php
$conn = sqlsrv_connect("HELIOS", ["Database" => "IMS", "Uid" => "", "PWD" => ""]);
$sql = "DELETE FROM CATEGORIES WHERE CATEGORY_ID = ?";
sqlsrv_query($conn, $sql, [$_GET['id']]);
header("Location: /IMS/Pages/category.php");
exit;
