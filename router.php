<?php
// Роутер для встроенного PHP-сервера
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Отдаём статику как есть (картинки, uploads и т.д.)
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// Роутим PHP-файлы
$file = ltrim($uri, '/') ?: 'index.html';
if (file_exists(__DIR__ . '/' . $file)) {
    // Если это PHP — включаем
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require __DIR__ . '/' . $file;
        exit;
    }
    return false;
}

http_response_code(404);
echo '404 Not Found';
