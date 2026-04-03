<?php
header('Content-Type: application/json');
$orders = [];
foreach (glob(__DIR__ . '/orders/*.json') as $file) {
    $orders[] = json_decode(file_get_contents($file), true);
}
usort($orders, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
echo json_encode($orders);
?>