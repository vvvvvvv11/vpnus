<?php
header('Content-Type: application/json');
$id = $_GET['id'] ?? '';
if (!$id) { echo json_encode(['status' => 'not_found']); exit; }
$file = __DIR__ . "/orders/$id.json";
if (!file_exists($file)) { echo json_encode(['status' => 'not_found']); exit; }
$order = json_decode(file_get_contents($file), true);
echo json_encode([
    'status' => $order['status'],
    'os' => $order['os'] ?? 'ios'
]);
?>
