<?php
session_start();
require_once "utils/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php?msg=Zaloguj+si%C4%99+aby+zmieni%C4%87+has%C5%82o");
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? null;

$error = null;
$info  = null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if(!$current || !$new || !$confirm){
        $error = "Uzupełnij wszystkie pola.";
    } elseif($new !== $confirm){
        $error = "Nowe hasło i potwierdzenie nie są zgodne.";
    } elseif(strlen($new) < 6){
        $error = "Hasło musi mieć przynajmniej 6 znaków.";
    } else {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if(!$row || !password_verify($current, $row['password_hash'])){
            $error = "Niepoprawne obecne hasło.";
        } else {
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $q = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $q->execute([$newHash, $userId]);

            $log = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
            $log->execute([$userId, "Zmiana hasła", $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);

            $info = "Hasło zostało zmienione.";
        }
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zmiana hasła — Okręcona Panewka</title>
    <link rel="stylesheet" href="assets/style.css">
    <script defer src="assets/app.js"></script>
</head>
<body class="page-fade-in">

<header class="site-header">
    <div class="container">
        <a class="brand" href="index.php">Okręcona Panewka</a>
        <nav class="main-nav">
            <a href="profile.php" class="nav-link">Profil</a>
        </nav>
        <div class="header-actions">
            <span class="welcome">Witaj, <?php echo htmlspecialchars($userName); ?></span>
            <a class="button" href="logout.php">Wyloguj</a>
        </div>
    </div>
</header>

<main class="container">
    <h1>Zmiana hasła</h1>

    <form method="post" class="form" style="max-width:450px">
        <?php if($error): ?>
            <p style="color:#ff8e8e"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if($info): ?>
            <p style="color:#9ae6b4"><?php echo htmlspecialchars($info); ?></p>
        <?php endif; ?>

        <div class="row">
            <div style="flex:1">
                <label>Obecne hasło</label>
                <input class="input" type="password" name="current_password" required>
            </div>
        </div>

        <div class="row">
            <div style="flex:1">
                <label>Nowe hasło</label>
                <input class="input" type="password" name="new_password" required>
            </div>
        </div>

        <div class="row">
            <div style="flex:1">
                <label>Potwierdź nowe hasło</label>
                <input class="input" type="password" name="confirm_password" required>
            </div>
        </div>

        <div class="actions">
            <button class="button-small" data-loading="Zapisywanie...">Zmień hasło</button>
        </div>
    </form>
</main>

</body>
</html>