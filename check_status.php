<?php
header('Content-Type: application/json');

$id = $_GET['id'] ?? '';
if (!$id) {
    echo json_encode(['status' => 'not_found']);
    exit;
}

$db = new SQLite3(__DIR__ . '/orders.db');
$db->exec('CREATE TABLE IF NOT EXISTS orders (
    id TEXT PRIMARY KEY,
    date TEXT,
    telegram TEXT,
    plan TEXT,
    price TEXT,
    days TEXT,
    os TEXT,
    screenshot TEXT,
    status TEXT DEFAULT "pending"
)');

$stmt = $db->prepare('SELECT status, os FROM orders WHERE id = :id');
$stmt->bindValue(':id', $id);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if (!$row) {
    echo json_encode(['status' => 'not_found']);
    exit;
}

echo json_encode(['status' => $row['status'], 'os' => $row['os']]);
?>
