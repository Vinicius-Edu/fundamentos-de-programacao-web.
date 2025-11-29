<?php
require "../middlewares/auth_check.php";
require "../config/connect.php";

$id=$_POST['id'];
$titulo=$_POST['titulo'];
$descricao=$_POST['descricao'];
$preco=$_POST['preco'];

$stmt=$conn->prepare("UPDATE services SET titulo=?, descricao=?, preco=? WHERE id=? AND user_id=?");
$stmt->bind_param("ssdii",$titulo,$descricao,$preco,$id,$_SESSION['user_id']);
$stmt->execute();

echo "Serviço atualizado!";
?>