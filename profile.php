<?php
session_start();
require_once "utils/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php?msg=Zaloguj+si%C4%99+aby+edytowa%C4%87+profil");
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$isAdmin  = !empty($_SESSION['is_admin']);

$error = null;
$info  = null;

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$data = $stmt->fetch();

if(!$data){
    die("Błąd: użytkownik nie istnieje.");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if($name === '' || $email === ''){
        $error = "Uzupełnij wszystkie pola.";
    } else {
        try{
            $q = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $q->execute([$name, $email, $userId]);

            $_SESSION['user_name'] = $name;

            $log = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
            $log->execute([
                $userId,
                "Edycja profilu",
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            $info = "Dane zostały zapisane.";
        } catch(Exception $e){
            $error = "Wystąpił błąd przy zapisie.";
        }
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Profil — Okręcona Panewka</title>
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
            <a href="confirmed.php" class="nav-link">Potwierdzone</a>
            <a href="profile.php" class="nav-link active">Profil</a>
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
    <h1>Twój profil</h1>

    <form method="post" class="form" style="max-width:500px">
        <?php if($error): ?>
            <p style="color:#ff8e8e"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if($info): ?>
            <p style="color:#9ae6b4"><?php echo htmlspecialchars($info); ?></p>
        <?php endif; ?>

        <div class="row">
            <div style="flex:1">
                <label>Imię / nazwa wyświetlana</label>
                <input class="input" type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>">
            </div>
        </div>

        <div class="row">
            <div style="flex:1">
                <label>Email</label>
                <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>">
            </div>
        </div>

        <a href="change_password.php" class="button-small">Zmień hasło</a>

        <div class="actions">
            <button class="button-small" data-loading="Zapisywanie...">Zapisz</button>
        </div>
    </form>
</main>

</body>
</html>