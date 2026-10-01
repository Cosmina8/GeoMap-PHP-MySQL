<?php
// add_point.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

/* =========================================================
   1. Verificare autentificare
========================================================= */
if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Nu ești autentificat.']);
    exit;
}

/* =========================================================
   2. Control ADMIN (doar tu poți adăuga)
========================================================= */
$ADMIN_ID = 9;

if ($_SESSION['user_id'] !== $ADMIN_ID) {
    http_response_code(403);
    echo json_encode(['error' => 'Nu ai dreptul să adaugi puncte.']);
    exit;
}

/* =========================================================
   3. Validare date primite
========================================================= */
$lat  = $_POST['lat'] ?? null;
$lng  = $_POST['lng'] ?? null;
$desc = trim($_POST['description'] ?? '');

if ($lat === null || $lng === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Coordonate lipsă.']);
    exit;
}

if (!is_numeric($lat) || !is_numeric($lng)) {
    http_response_code(400);
    echo json_encode(['error' => 'Coordonatele trebuie să fie numerice.']);
    exit;
}

/* =========================================================
   4. Inserare în baza de date
========================================================= */
try {
    $stmt = $pdo->prepare(
        "INSERT INTO points (lat, lng, description, user_id)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$lat, $lng, $desc, $_SESSION['user_id']]);

    echo json_encode(['message' => 'Punct adăugat cu succes!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Eroare la baza de date.']);
}
