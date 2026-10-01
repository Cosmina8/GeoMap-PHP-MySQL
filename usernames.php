<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');

$q = $_GET['q'] ?? '';
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE username LIKE ? ORDER BY username LIMIT 10");
    $stmt->execute([$q . '%']);
} else {
    $stmt = $pdo->query("SELECT username FROM users ORDER BY username LIMIT 10");
}

$rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($rows);
