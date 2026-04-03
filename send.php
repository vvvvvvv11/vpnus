<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не разрешён']);
    exit;
}

$telegram = $_POST['telegram'] ?? '';
$plan     = $_POST['plan'] ?? '';
$price    = $_POST['price'] ?? '';
$days     = $_POST['days'] ?? '';
$os       = $_POST['os'] ?? 'ios';
$file     = $_FILES['screenshot'] ?? null;

if (!$telegram || !$file || $file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Не все данные заполнены']);
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
$filename = uniqid() . '.jpg';
move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

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

$id   = uniqid();
$date = date('Y-m-d H:i:s');

$stmt = $db->prepare('INSERT INTO orders VALUES (:id,:date,:telegram,:plan,:price,:days,:os,:screenshot,:status)');
$stmt->bindValue(':id',         $id);
$stmt->bindValue(':date',       $date);
$stmt->bindValue(':telegram',   $telegram);
$stmt->bindValue(':plan',       $plan);
$stmt->bindValue(':price',      $price);
$stmt->bindValue(':days',       $days);
$stmt->bindValue(':os',         $os);
$stmt->bindValue(':screenshot', $filename);
$stmt->bindValue(':status',     'pending');
$stmt->execute();

echo json_encode(['success' => true, 'order_id' => $id]);
?>
