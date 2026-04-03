<?php
header('Content-Type: application/json');

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

$result = $db->query('SELECT * FROM orders ORDER BY date DESC');
$orders = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
