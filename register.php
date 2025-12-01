<?php
session_start();
require_once "utils/db.php"; 

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if($name === '' || $email === '' || $pass === ''){
        $error = "Uzupełnij wszystkie wymagane pola.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Adres e-mail jest nieprawidłowy.";
    } else {
        try{
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if($stmt->fetch()){
                $error = "Konto z tym adresem już istnieje.";
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,phone) VALUES (?,?,?,?)");
                $stmt->execute([$name, $email, $hash, $phone]);

                $userId = $pdo->lastInsertId();
                $log = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
                $log->execute([$userId, "Rejestracja konta", $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);

                header("Location: login.php?msg=Konto+zostało+utworzone");
                exit;
            }
        } catch(Exception $e){
            $error = "Wystąpił błąd po stronie serwera.";
        }
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Rejestracja — Okręcona Panewka</title>
    <link rel="stylesheet" href="assets/style.css">
    <script defer src="assets/app.js"></script>
</head>
<body class="page-fade-in">
<header class="site-header">
    <div class="container">
        <a class="brand" href="index.php">Okręcona Panewka</a>
    </div>
</header>

<main class="container">
    <h1>Załóż konto</h1>

    <form method="post" class="form">
        <?php if(!empty($error)): ?>
            <p class="muted" style="color:#ff8e8e"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <div class="row">
            <div style="flex:1">
                <label>Imię i nazwisko</label>
                <input type="text" class="input" name="name" required>
            </div>
        </div>

        <div class="row">
            <div style="flex:1">
                <label>E-mail</label>
                <input type="email" class="input" name="email" required>
            </div>
        </div>

        <div class="row">
            <div style="flex:1">
                <label>Hasło</label>
                <input type="password" class="input" name="password" required>
            </div>
        </div>

        <div class="row">
            <div style="flex:1">
                <label>Telefon (opcjonalnie)</label>
                <input type="text" class="input" name="phone">
            </div>
        </div>

        <div class="actions">
            <button type="submit" data-loading="Tworzenie konta...">Zarejestruj</button>
            <a href="login.php" class="button secondary">Mam konto</a>
        </div>
    </form>
</main>
</body>
</html>