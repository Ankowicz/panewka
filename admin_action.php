<?php
session_start();
require_once "utils/db.php";

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    header("Location: index.php");
    exit;
}

$action = $_GET['action'] ?? null;
$id      = $_GET['id'] ?? null;

if(!$action || !$id){
    header("Location: admin.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$id]);
$appointment = $stmt->fetch();

if(!$appointment){
    header("Location: admin.php");
    exit;
}

if($action === "confirm"){
    $q = $pdo->prepare("UPDATE appointments SET confirmed_by = 1 WHERE id = ?");
    $q->execute([$id]);

    $log = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
    $log->execute([
        $_SESSION['user_id'],
        "Admin potwierdził termin ID $id",
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

if($action === "delete"){
    $q = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $q->execute([$id]);

    $log = $pdo->prepare("INSERT INTO logs (user_id, action, ip, user_agent) VALUES (?, ?, ?, ?)");
    $log->execute([
        $_SESSION['user_id'],
        "Admin usunął termin ID $id",
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

header("Location: admin.php");
exit;