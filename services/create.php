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

$erro = "";
$sucesso = "";

$sqlProfessional = "SELECT id FROM professionals WHERE user_id = ?";
$stmt = $conn->prepare($sqlProfessional);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultProfessional = $stmt->get_result();

if ($resultProfessional->num_rows === 0) {
    $erro = "Você ainda não possui um perfil profissional cadastrado. Entre em contato com o administrador.";
    $professionalId = null;
} else {
    $professional = $resultProfessional->fetch_assoc();
    $professionalId = $professional["id"];
}

$sqlCategories = "SELECT id, name FROM service_categories ORDER BY name ASC";
$resultCategories = $conn->query($sqlCategories);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $professionalId) {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $categoryId = trim($_POST["category_id"] ?? "");

    if (empty($title)) {
        $erro = "O título do serviço é obrigatório.";
    } elseif (empty($description)) {
        $erro = "A descrição do serviço é obrigatória.";
    } elseif (empty($price) || !is_numeric($price) || $price <= 0) {
        $erro = "Informe um preço válido maior que zero.";
    } elseif (empty($categoryId)) {
        $erro = "Selecione uma categoria.";
    } else {
        $sqlInsert = "INSERT INTO services (professional_id, category_id, title, description, price) VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iissd", $professionalId, $categoryId, $title, $description, $price);

        if ($stmtInsert->execute()) {
            $sucesso = "Serviço criado com sucesso!";
            header("Location: my_services.php?success=1");
            exit;
        } else {
            $erro = "Erro ao criar serviço. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <div class="container mt-4" style="max-width: 700px;">

        <div class="card shadow p-4">
            <h4 class="mb-3">Criar Novo Serviço</h4>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <?php if (!$professionalId): ?>
                <div class="alert alert-warning">
                    Você ainda não possui um perfil profissional cadastrado. Entre em contato com o administrador.
                </div>
                <a href="../index.php" class="btn btn-secondary">Voltar</a>
            <?php elseif ($resultCategories->num_rows === 0): ?>
                <div class="alert alert-warning">
                    Não há categorias disponíveis. Entre em contato com o administrador para cadastrar categorias.
                </div>
                <a href="my_services.php" class="btn btn-secondary">Voltar</a>
            <?php else: ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Título do Serviço <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" 
                               placeholder="Ex: Consultoria em Marketing Digital" 
                               value="<?= htmlspecialchars($_POST["title"] ?? "") ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Selecione uma categoria</option>
                            <?php
                            $resultCategories->data_seek(0); // Resetar ponteiro
                            while ($category = $resultCategories->fetch_assoc()):
                                $selected = (isset($_POST["category_id"]) && $_POST["category_id"] == $category["id"]) ? "selected" : "";
                            ?>
                                <option value="<?= $category["id"] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($category["name"]) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="5" 
                                  placeholder="Descreva detalhadamente o serviço oferecido..." required><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" 
                               step="0.01" min="0.01" 
                               placeholder="0.00" 
                               value="<?= htmlspecialchars($_POST["price"] ?? "") ?>" required>
                        <small class="form-text text-muted">Use ponto (.) como separador decimal</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Criar Serviço</button>
                        <a href="my_services.php" class="btn btn-secondary">Cancelar</a>
                    </div>

                </form>

            <?php endif; ?>

        </div>

    </div>

</body>

</html>

