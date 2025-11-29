<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: /login/login.php");
    exit;
}

$username = $_SESSION["name"] ?? "Usuário";
$role = $_SESSION["role"] ?? "client";

$logoutPath = "/login/logout.php";
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">Serviços Locais</a>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">
                <?php if ($role === "admin"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/users/list.php">Usuários</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/professionals/list.php">Profissionais</a>
                    </li>
                <?php endif; ?>
            </ul>

            <span class="navbar-text text-white me-3">
                Olá, <?= htmlspecialchars($username) ?>!
            </span>

            <a href="<?= $logoutPath ?>" class="btn btn-light btn-sm">Sair</a>
        </div>
    </div>
</nav>