<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Не указан ID новости'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = (int)$_GET['id'];

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT id, news_name, news_date, news_description FROM news WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $news = $stmt->fetch();
    
    if ($news) {
        $date = new DateTime($news['news_date']);
        $news['news_date_formatted'] = $date->format('d M Y');
        echo json_encode(['success' => true, 'data' => $news], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Новость не найдена'], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>