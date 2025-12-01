<?php
session_start();

$userId   = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? null;
$isAdmin  = $_SESSION['is_admin'] ?? 0;
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Okręcona Panewka — Twój warsztat samochodowy</title>
    <link rel="stylesheet" href="assets/style.css">
    <script defer src="assets/app.js"></script>
</head>

<body class="page-fade-in">

<header class="site-header">
    <div class="container">
        <a class="brand" href="index.php">Okręcona Panewka</a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link active">Strona główna</a>
            <a href="about.php" class="nav-link">O firmie</a>
            <a href="appointments.php" class="nav-link">Terminy</a>

            <?php if($userId): ?>
                <a href="confirmed.php" class="nav-link">Potwierdzone</a>
                <a href="profile.php" class="nav-link">Profil</a>
            <?php endif; ?>

            <?php if($isAdmin): ?>
                <a href="admin.php" class="nav-link">Panel administracyjny</a>
            <?php endif; ?>
        </nav>

        <div class="header-actions">
            <?php if($userId): ?>
                <span class="welcome">Witaj, <?php echo htmlspecialchars($userName); ?></span>
                <a class="button" href="logout.php">Wyloguj</a>
            <?php else: ?>
                <a class="button" href="login.php">Zaloguj</a>
                <a class="button secondary" href="register.php">Załóż konto</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
    <section class="hero">
        <div class="container hero-content">
            <h1>Okręcona Panewka</h1>
            <p>
                Warsztat, w którym dbamy o Twoje auto tak, jakby było naszym własnym.
                Mechanika, diagnostyka, przygotowanie do przeglądów i naprawy poważniejszych usterek.
            </p>

            <div class="hero-buttons">
                <a href="appointments.php" class="button large">Umów termin</a>
                <a href="about.php" class="button large secondary">Poznaj nas</a>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">

            <h2>Co możemy dla Ciebie zrobić</h2>

            <div class="features-grid">

                <div class="feature-card">
                    <h3>Diagnostyka komputerowa</h3>
                    <p>
                        Precyzyjna analiza elektroniki i wykrywanie usterek, które nie są widoczne na pierwszy rzut oka.
                    </p>
                </div>

                <div class="feature-card">
                    <h3>Naprawy mechaniczne</h3>
                    <p>
                        Silniki, zawieszenie, hamulce — zajmujemy się wszystkim, co wpływa na bezpieczeństwo i komfort jazdy.
                    </p>
                </div>

                <div class="feature-card">
                    <h3>Przeglądy i przygotowanie pojazdu</h3>
                    <p>
                        Sprawdzimy auto przed sprzedażą, dłuższą trasą albo corocznym badaniem technicznym.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Chcesz zarezerwować wizytę?</h2>
            <p>Wybierz termin, opisz problem i gotowe. Zajmiemy się resztą.</p>
            <a href="appointments.php" class="button large">Rezerwuj</a>
        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="container">
        <p>© <?php echo date("Y"); ?> Okręcona Panewka — wszystkie prawa zastrzeżone</p>
    </div>
</footer>

</body>
</html>