<?php
session_start();
require_once "utils/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php?msg=Zaloguj+si%C4%99+aby+zobaczy%C4%87+swoje+terminy");
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? null;
$isAdmin  = !empty($_SESSION['is_admin']);

$stmt = $pdo->prepare("
    SELECT slot, description, confirmed_by
    FROM appointments
    WHERE user_id = ?
    ORDER BY slot ASC
");
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Twoje terminy — Okręcona Panewka</title>
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
            <a href="appointments.php" class="nav-link">Rezerwuj termin</a>
            <a href="your_appointments.php" class="nav-link active">Twoje terminy</a>
            <a href="confirmed.php" class="nav-link">Potwierdzone</a>
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
    <h1>Twoje terminy</h1>

    <?php if(empty($appointments)): ?>
        <p>Nie masz żadnych zarezerwowanych terminów.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="nice-table">
                <thead>
                    <tr>
                        <th>Termin</th>
                        <th>Opis problemu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($appointments as $a): ?>
                        <tr>
                            <td><?php echo date("d.m.Y H:i", strtotime($a['slot'])); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($a['description'])); ?></td>
                            <td><?php echo $a['confirmed'] ? "Potwierdzony" : "Oczekuje"; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

</body>
</html>