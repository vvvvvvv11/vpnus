<?php
header('Content-Type: application/json');

define('SUBSCRIPTION_URL', 'https://gist.githubusercontent.com/vvvvvvv11/4398d3e9c0fecb3fc7f81d13f5e48044/raw/f2c5f12b4a877b98eeb96cf0fcfe817140914462/gistfile1.txt');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не разрешён']);
    exit;
}

$telegram = $_POST['telegram'] ?? '';
$plan = $_POST['plan'] ?? '';
$price = $_POST['price'] ?? '';
$days = $_POST['days'] ?? '';
$os = $_POST['os'] ?? 'ios';
$file = $_FILES['screenshot'] ?? null;

if (!$telegram || !$file || $file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Не все данные заполнены']);
    exit;
}

// Сохраняем скриншот
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
$filename = uniqid() . '.jpg';
$filepath = $uploadDir . $filename;
move_uploaded_file($file['tmp_name'], $filepath);

// Сохраняем заказ в JSON-файл
$ordersDir = __DIR__ . '/orders/';
if (!is_dir($ordersDir)) mkdir($ordersDir, 0777, true);

$order = [
    'id' => uniqid(),
    'date' => date('Y-m-d H:i:s'),
    'telegram' => $telegram,
    'plan' => $plan,
    'price' => $price,
    'days' => $days,
    'os' => $os,
    'screenshot' => $filename,
    'status' => 'pending'
];

file_put_contents($ordersDir . $order['id'] . '.json', json_encode($order, JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true, 'order_id' => $order['id']]);
?>