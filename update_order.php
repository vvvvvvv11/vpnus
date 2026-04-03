<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'];
$status = $input['status'];

$file = __DIR__ . "/orders/$id.json";
if (file_exists($file)) {
    $order = json_decode(file_get_contents($file), true);
    $order['status'] = $status;
    file_put_contents($file, json_encode($order, JSON_UNESCAPED_UNICODE));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>