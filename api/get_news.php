<?php
header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 0); // Не выводим в браузер, только в лог

require_once '../config.php';

try {
    $pdo = getDBConnection();
    
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
    $order = ($sort === 'oldest') ? 'ASC' : 'DESC';
    
    $stmt = $pdo->prepare("SELECT id, news_name, news_date, news_description FROM news ORDER BY news_date $order");
    $stmt->execute();
    $news = $stmt->fetchAll();
    
    foreach ($news as &$item) {
        $date = new DateTime($item['news_date']);
        $item['news_date_formatted'] = $date->format('d M Y');
        $item['news_date_sort'] = $date->format('Y-m-d');
    }
    
    echo json_encode(['success' => true, 'data' => $news], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>