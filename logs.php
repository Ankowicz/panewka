<?php
session_start();
require_once "utils/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$userId  = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? null;
$isAdmin  = !empty($_SESSION['is_admin']);

if($isAdmin){
    $stmt = $pdo->query("SELECT l.*, u.name AS user_name FROM logs l JOIN users u ON u.id = l.user_id ORDER BY l.created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM logs WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
}

$logs = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Historia logowań — Okręcona Panewka</title>
    <link rel="stylesheet" href="assets/style.css">
    <script defer src="assets/app.js"></script>
</head>
<body class="page-fade-in">

<header class="site-header">
    <div class="container">
        <a class="brand" href="index.php">Okręcona Panewka</a>
        <nav class="main-nav">
            <a href="profile.php" class="nav-link">Profil</a>
            <?php if($isAdmin): ?>
                <a href="admin.php" class="nav-link">Panel administracyjny</a>
            <?php endif; ?>
        </nav>
        <div class="header-actions">
            <span class="welcome">Witaj, <?php echo htmlspecialchars($userName); ?></span>
            <a class="button" href="logout.php">Wyloguj</a>
        </div>
    </div>
</header>

<main class="container">
    <h1>Historia działań</h1>

    <?php if(empty($logs)): ?>
        <p>Brak zapisanych działań.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="nice-table">
                <thead>
                    <tr>
                        <?php if($isAdmin): ?><th>Użytkownik</th><?php endif; ?>
                        <th>Akcja</th>
                        <th>IP</th>
                        <th>User Agent</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($logs as $l): ?>
                    <tr>
                        <?php if($isAdmin): ?><td><?php echo htmlspecialchars($l['user_name']); ?></td><?php endif; ?>
                        <td><?php echo htmlspecialchars($l['action']); ?></td>
                        <td><?php echo htmlspecialchars($l['ip']); ?></td>
                        <td><?php echo htmlspecialchars($l['user_agent']); ?></td>
                        <td><?php echo date("d.m.Y H:i", strtotime($l['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

</body>
</html>