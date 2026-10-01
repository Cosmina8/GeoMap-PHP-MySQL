<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT id, lat, lng, description FROM points ORDER BY id");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Eroare la citirea din baza de date: ' . $e->getMessage()]);
}
