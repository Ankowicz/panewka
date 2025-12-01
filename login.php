<?php
session_start();
require_once "utils/db.php";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if($email === '' || $pass === ''){
        $error = "Wpisz dane logowania.";
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password_hash, is_admin FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($pass, $user['password_hash'])){
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin']  = (bool)$user['is_admin'];

            $log = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
            $log->execute([$user['id'], "Logowanie", $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);

            header("Location: index.php?msg=Zalogowano");
            exit;
        } else {
            $error = "Niepoprawny e-mail lub hasło.";
        }
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Logowanie — Okręcona Panewka</title>
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
    <h1>Logowanie</h1>

    <form method="post" class="form">
        <?php if(!empty($error)): ?>
            <p class="muted" style="color:#ff8e8e"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

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

        <div class="actions">
            <button type="submit" class="button" data-loading="Logowanie...">Zaloguj się</button>
            <a href="register.php" class="button secondary">Załóż konto</a>
        </div>
    </form>
</main>
</body>
</html>