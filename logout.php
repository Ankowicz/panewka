<?php
session_start();
require_once "utils/db.php";

if(isset($_SESSION['user_id'])){
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        "Wylogowanie",
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

session_unset();
session_destroy();

header("Location: index.php?msg=Wylogowano");
exit;