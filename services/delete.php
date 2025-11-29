<?php
require "../middlewares/auth_check.php";
require "../config/connect.php";

$id=$_GET['id'];

$stmt=$conn->prepare("DELETE FROM services WHERE id=? AND user_id=?");
$stmt->bind_param("ii",$id,$_SESSION['user_id']);
$stmt->execute();

echo "Serviço excluído!";
?>