<?php
session_start();
require_once "../config/connectDb.php";
require_once "../partials/navbar.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login/login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$role = $_SESSION["role"];

if ($role !== "professional") {
    header("Location: ../index.php");
    exit;
}

$successMessage = isset($_GET["success"]) && $_GET["success"] == "1" ? "Serviço criado com sucesso!" : "";

$sqlProfessional = "SELECT id FROM professionals WHERE user_id = ?";
$stmt = $conn->prepare($sqlProfessional);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultProfessional = $stmt->get_result();

if ($resultProfessional->num_rows === 0) {
    $errorMessage = "Você ainda não possui um perfil profissional cadastrado. Entre em contato com o administrador.";
} else {
    $professional = $resultProfessional->fetch_assoc();
    $professionalId = $professional["id"];

    $sqlServices = "
        SELECT s.id, s.title, s.description, s.price,
               c.name AS category_name
        FROM services s
        JOIN service_categories c ON s.category_id = c.id
        WHERE s.professional_id = ?
        ORDER BY s.title ASC
    ";
    $stmtServices = $conn->prepare($sqlServices);
    $stmtServices->bind_param("i", $professionalId);
    $stmtServices->execute();
    $resultServices = $stmtServices->get_result();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Meus Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Meus Serviços</h3>
            <?php if (isset($professionalId)): ?>
                <a href="create.php" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Criar Novo Serviço
                </a>
            <?php endif; ?>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($successMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-warning">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php elseif (!isset($resultServices) || $resultServices->num_rows === 0): ?>
            <div class="alert alert-info">
                <p>Você ainda não possui serviços cadastrados.</p>
                <a href="create.php" class="btn btn-success">Criar Primeiro Serviço</a>
            </div>
        <?php else: ?>

            <div class="row">
                <?php while ($row = $resultServices->fetch_assoc()): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row["title"]) ?></h5>

                                <h6 class="text-muted">
                                    Categoria: <?= htmlspecialchars($row["category_name"]) ?>
                                </h6>

                                <p class="mt-2">
                                    <?= nl2br(htmlspecialchars($row["description"])) ?>
                                </p>

                                <p class="mb-3">
                                    <strong>Preço:</strong> R$ <?= number_format($row["price"], 2, ',', '.') ?>
                                </p>

                                <div class="d-flex gap-2">
                                    <a href="edit.php?id=<?= $row["id"] ?>" class="btn btn-primary btn-sm">
                                        Editar
                                    </a>
                                    <a href="delete.php?id=<?= $row["id"] ?>" class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Tem certeza que deseja excluir este serviço?');">
                                        Excluir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php endif; ?>

        <div class="mt-3">
            <a href="../index.php" class="btn btn-secondary">Voltar ao Painel</a>
        </div>

    </div>

</body>

</html>

