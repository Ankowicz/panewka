<?php
session_start();
require_once "utils/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php?msg=Zaloguj+si%C4%99+aby+zarezerwowa%C4%87+termin");
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? null;
$isAdmin  = !empty($_SESSION['is_admin']);

$error = null;
$info  = null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $slot        = $_POST['slot'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if($slot === '' || $description === ''){
        $error = "Uzupełnij wszystkie pola.";
    } else {
        try{
            $check = $pdo->prepare("SELECT id FROM appointments WHERE slot = ? LIMIT 1");
            $check->execute([$slot]);

            if($check->fetch()){
                $error = "Ten termin jest już zajęty.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO appointments (user_id, slot, description) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $slot, $description]);

                $log = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
                $log->execute([$userId, "Rezerwacja terminu: $slot", $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);

                $info = "Twoja prośba została wysłana. Poczekaj na potwierdzenie.";
            }
        } catch(Exception $e){
            $error = "Wystąpił błąd przy rezerwacji.";
        }
    }
}

$slots = [];
$startTime = new DateTime('now');
$endTime   = new DateTime('+14 days');
$hourStart = 9;
$hourEnd   = 17;

$interval = new DateInterval('PT30M');

$current = clone $startTime;
$current->setTime($hourStart, 0);

while($current <= $endTime){
    $h = (int)$current->format('H');
    if($h >= $hourStart && $h < $hourEnd){
        $slots[] = $current->format('Y-m-d H:i:s');
    }
    $current->add($interval);
}

$busy = $pdo->query("SELECT slot FROM appointments")->fetchAll(PDO::FETCH_COLUMN);
$busy = $busy ? $busy : [];
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Terminy — Okręcona Panewka</title>
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
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="container">
    <h1>Rezerwacja terminu</h1>

    <form method="post" class="form">
        <?php if($error): ?>
            <p style="color:#ff8e8e"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if($info): ?>
            <p style="color:#9ae6b4"><?php echo htmlspecialchars($info); ?></p>
        <?php endif; ?>

        <div class="row">
            <div style="flex:1">
                <label>Wybierz termin</label>
                <select class="input" name="slot" required>
                    <option value="">-- wybierz --</option>
                    <?php foreach($slots as $s): ?>
                        <?php if(!in_array($s, $busy)): ?>
                            <option value="<?php echo $s; ?>">
                                <?php echo date("d.m.Y H:i", strtotime($s)); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div style="flex:1">
                <label>Opisz problem</label>
                <textarea class="input textarea" name="description" required></textarea>
            </div>
        </div>

        <div class="actions">
            <button class="button-small" type="submit" data-loading="Wysyłanie...">Wyślij prośbę</button>
        </div>
    </form>
</main>

</body>
</html>