<?php
session_start();
require_once "config/connectDb.php";
require_once "partials/navbar.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit;
}

$username = $_SESSION["name"];
$role = $_SESSION["role"];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel - Gestão de Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>


    <div class="container mt-4">

        <h3>Painel de Controle</h3>
        <p>Bem-vindo ao sistema de gestão de serviços.</p>

        <?php if ($role === "admin"): ?>
            <div class="alert alert-info">
                <strong>Painel do Administrador:</strong> Você pode gerenciar usuários, profissionais, categorias e serviços.
            </div>

            <a href="users/list.php" class="btn btn-secondary">Gerenciar Usuários</a>
            <a href="professionals/list.php" class="btn btn-secondary">Profissionais</a>
            <a href="services/list.php" class="btn btn-secondary">Serviços</a>
            <a href="categories/list.php" class="btn btn-secondary">Categorias</a>

        <?php elseif ($role === "professional"): ?>
            <div class="alert alert-success">
                <strong>Área do Profissional:</strong> Aqui você pode gerenciar seus serviços.
            </div>

            <a href="services/my_services.php" class="btn btn-secondary">Meus Serviços</a>

        <?php else: ?>
            <div class="alert alert-warning">
                <strong>Área do Cliente:</strong> Você pode agendar serviços com profissionais.
            </div>

            <a href="services/list.php" class="btn btn-secondary">Ver Serviços</a>
        <?php endif; ?>

    </div>

</body>

</html>