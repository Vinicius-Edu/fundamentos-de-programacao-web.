<?php
$serverName = "localhost:3306";
$username   = "root";
$password   = "";
$database   = "local_service";

$conn = new mysqli($serverName, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
