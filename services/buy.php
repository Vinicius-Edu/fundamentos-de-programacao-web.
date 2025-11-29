<?php
require "../middlewares/auth_check.php";
require "../config/connect.php";

$service_id=$_POST['service_id'];
$buyer=$_SESSION['user_id'];

$stmt=$conn->prepare("INSERT INTO purchases(service_id,buyer_id) VALUES(?,?)");
$stmt->bind_param("ii",$service_id,$buyer);
$stmt->execute();

echo "Compra registrada!";
?>