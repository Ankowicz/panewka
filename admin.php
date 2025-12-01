<?php
session_start();
require_once "utils/db.php";

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    header("Location: index.php");
    exit;
}

$userName = $_SESSION['user_name'] ?? null;

$stmt = $pdo->query("
    SELECT a.id, a.slot, a.description, a.confirmed_by,
           u.name AS user_name, u.email AS user_email
    FROM appointments a
    JOIN users u ON u.id = a.user_id
    ORDER BY a.slot ASC
");
$appointments = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Panel administracyjny — Okręcona Panewka</title>
    <link rel="stylesheet" href="assets/style.css">
    <script defer src="assets/app.js"></script>
</head>
<body class="page-fade-in">

<header class="site-header">
    <div class="container">
        <a class="brand" href="index.php">Okręcona Panewka</a>
        <nav class="main-nav">
            <a href="index.php" class="nav-link">Strona główna</a>
            <a href="about.php" class="nav-link">O firmie</a>
            <a href="appointments.php" class="nav-link">Terminy</a>
            <a href="admin.php" class="nav-link active">Panel administracyjny</a>
        </nav>
        <div class="header-actions">
            <span class="welcome">Witaj, <?php echo htmlspecialchars($userName); ?></span>
            <a class="button" href="logout.php">Wyloguj</a>
        </div>
    </div>
</header>

<main class="container">
    <h1>Panel administracyjny</h1>

    <?php if(empty($appointments)): ?>
        <p>Brak próśb o terminy.</p>
    <?php else: ?>

    <div class="table-wrapper">
        <table class="nice-table">
            <thead>
                <tr>
                    <th>Użytkownik</th>
                    <th>Email</th>
                    <th>Termin</th>
                    <th>Opis</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($appointments as $a): ?>
                <tr>
                    <td><?php echo htmlspecialchars($a['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($a['user_email']); ?></td>
                    <td><?php echo date("d.m.Y H:i", strtotime($a['slot'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($a['description'])); ?></td>
                    <td>
                        <?php echo $a['confirmed_by'] ? "Potwierdzony" : "Oczekuje"; ?>
                    </td>
                    <td>
                        <?php if(!$a['confirmed_by']): ?>
                            <a class="button small" href="admin_action.php?action=confirm&id=<?php echo $a['id']; ?>">
                                Potwierdź
                            </a>
                        <?php endif; ?>

                        <a class="button small danger" 
                           href="admin_action.php?action=delete&id=<?php echo $a['id']; ?>"
                           onclick="return confirm('Na pewno chcesz usunąć?');">
                            Usuń
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</main>

</body>
</html>