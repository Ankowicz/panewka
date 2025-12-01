<?php
session_start();
$userName = $_SESSION['user_name'] ?? null;
$isAdmin  = !empty($_SESSION['is_admin']);
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>O firmie — Okręcona Panewka</title>
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
            <?php if($isAdmin): ?>
                <a href="admin.php" class="nav-link">Panel administracyjny</a>
            <?php endif; ?>
        </nav>
        <div class="header-actions">
            <?php if($userName): ?>
                <span class="welcome">Witaj, <?php echo htmlspecialchars($userName); ?></span>
                <a class="button" href="logout.php">Wyloguj</a>
            <?php else: ?>
                <a href="register.php" class="button">Zarejestruj</a>
                <a href="login.php" class="button secondary">Zaloguj</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="container">
    <h1>O firmie</h1>

    <div class="form" style="max-width:900px">
        <p>
            Okręcona Panewka to warsztat, który działa od ponad dziesięciu lat.  
            Zajmujemy się diagnostyką i naprawą samochodów osobowych.  
            Stawiamy na dokładność i jasną komunikację.  
        </p>
        <p>
            Nasza ekipa to mechanicy z doświadczeniem.  
            Mamy sprzęt do analizy silników benzynowych i diesla.  
            Wykonujemy przeglądy, wymiany i większe remonty.
        </p>
        <p>
            Warsztat mieści się przy ulicy Zakręconej 12 w Poznaniu.  
            Telefon: 600 800 900  
            Email: kontakt@okreconapanewka.pl
        </p>
        <p class="muted small">
            Jeśli chcesz zarezerwować termin, przejdź do zakładki “Terminy”.
        </p>
    </div>
</main>

</body>
</html>