<?php
header('Content-Type: application/json');

$input  = json_decode(file_get_contents('php://input'), true);
$id     = $input['id'] ?? '';
$status = $input['status'] ?? '';

if (!$id || !$status) {
    echo json_encode(['success' => false]);
    exit;
}

$db = new SQLite3(__DIR__ . '/orders.db');
$stmt = $db->prepare('UPDATE orders SET status = :status WHERE id = :id');
$stmt->bindValue(':status', $status);
$stmt->bindValue(':id',     $id);
$stmt->execute();

echo json_encode(['success' => true]);
?>
