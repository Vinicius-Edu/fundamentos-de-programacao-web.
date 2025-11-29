<?php
session_start();
require_once "../config/connectDb.php";

$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"] ?? "client";

    if ($name === "" || $email === "" || $password === "") {
        $erro = "Preencha todos os campos.";
    } else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insertSql = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("ssss", $name, $email, $hash, $role);

            if ($stmt->execute()) {
                $sucesso = "Usuário criado com sucesso!";
                header("Location: ../login/login.php");
                $stmt->close();
            } else {
                $erro = "Erro ao criar usuário.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5" style="max-width: 500px;">

        <div class="card shadow p-4">
            <h4 class="mb-3">Criar Usuário</h4>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alert alert-success"><?= $sucesso ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="name" class="form-control" placeholder="Seu nome">
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" placeholder="seuemail@dominio.com">
                </div>

                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" placeholder="Digite uma senha">
                </div>

                <div class="mb-3">
                    <label class="form-label">Perfil</label>
                    <select name="role" class="form-select">
                        <option value="client">Cliente</option>
                        <option value="professional">Profissional</option>
                    </select>
                </div>

                <button class="btn btn-success w-100">Criar Usuário</button>
                <a href="../login/login.php" class="btn btn-link mt-2 w-100 text-center">Voltar ao Login</a>
            </form>
        </div>
    </div>
</body>

</html>