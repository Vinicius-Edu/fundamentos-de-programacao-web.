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
$username = $_SESSION["username"] ?? "Usuário";

$sql = "
    SELECT s.id, s.title, s.description, s.price,
           p.full_name AS professional_name,
           c.name AS category_name
    FROM services s
    JOIN professionals p ON s.professional_id = p.id
    JOIN service_categories c ON s.category_id = c.id
    ORDER BY s.title ASC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Serviços Disponíveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container">

        <h3 class="mb-4">Serviços Disponíveis</h3>

        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-warning">
                Nenhum serviço encontrado no momento.
            </div>
        <?php else: ?>

            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row["title"]) ?></h5>

                                <h6 class="text-muted">
                                    Categoria: <?= htmlspecialchars($row["category_name"]) ?>
                                </h6>

                                <p class="mt-2 small">
                                    <?= nl2br(htmlspecialchars(substr($row["description"], 0, 120))) ?>...
                                </p>

                                <p><strong>Profissional:</strong> <?= htmlspecialchars($row["professional_name"]) ?></p>
                                <p><strong>Preço:</strong> R$ <?= number_format($row["price"], 2, ',', '.') ?></p>

                                <a href="view.php?id=<?= $row["id"] ?>" class="btn btn-primary btn-sm w-100">
                                    Ver detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php endif; ?>

    </div>

</body>

</html>