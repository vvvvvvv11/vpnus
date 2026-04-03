<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '25M');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не разрешён']);
    exit;
}

$telegram = trim($_POST['telegram'] ?? '');
$plan     = $_POST['plan'] ?? '';
$price    = $_POST['price'] ?? '';
$days     = $_POST['days'] ?? '';
$os       = $_POST['os'] ?? 'ios';
$file     = $_FILES['screenshot'] ?? null;

// Диагностика
if (!$telegram) {
    echo json_encode(['success' => false, 'error' => 'Введите Telegram ID']);
    exit;
}

if (!$file) {
    echo json_encode(['success' => false, 'error' => 'Файл не получен']);
    exit;
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => 'Файл слишком большой (лимит сервера)',
        UPLOAD_ERR_FORM_SIZE  => 'Файл слишком большой (лимит формы)',
        UPLOAD_ERR_PARTIAL    => 'Файл загружен частично',
        UPLOAD_ERR_NO_FILE    => 'Файл не выбран',
        UPLOAD_ERR_NO_TMP_DIR => 'Нет временной папки',
        UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла',
        UPLOAD_ERR_EXTENSION  => 'Файл заблокирован расширением',
    ];
    $msg = $errors[$file['error']] ?? 'Ошибка загрузки файла (код ' . $file['error'] . ')';
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Определяем расширение по MIME-типу
$mime = mime_content_type($file['tmp_name']);
$ext  = match($mime) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    default      => 'jpg',
};

$filename = uniqid() . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
    echo json_encode(['success' => false, 'error' => 'Не удалось сохранить файл']);
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
