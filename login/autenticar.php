<?php
session_start();
require "../config/connection.php";

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($senha, $user['senha'])) {
        $_SESSION['id'] = $user['id_usuario'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../painel.php");
        exit;
    }
}

header("Location: login.php?erro=1");
exit;
