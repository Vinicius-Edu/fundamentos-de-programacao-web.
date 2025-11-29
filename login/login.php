<?php
session_start();
require_once "../config/connectDb.php";


if (isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit;
}

$erro = "";
$passHash = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($email === "" || $password === "") {
        $erro = "Preencha todos os campos.";
    } else {
        $sql = "SELECT id, name, email, password_hash, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $emailDb, $passHash, $role);
            $stmt->fetch();

            if (password_verify($password, $passHash)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["name"] = $name;
                $_SESSION["email"] = $emailDb;
                $_SESSION["role"] = $role;

                header("Location: ../index.php");
                exit;
            } else {
                $erro = "Email ou senha incorretos.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Gestão de Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="width: 380px;">
            <h4 class="text-center mb-3">Acessar Sistema</h4>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Digite seu email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" placeholder="Digite sua senha">
                </div>

                <button class="btn btn-primary w-100">Entrar</button>
                <a href="../users/create.php" class="btn btn-link mt-2 w-100 text-center">Criar Conta</a>
            </form>
        </div>
    </div>

</body>

</html>