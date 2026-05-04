<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$message = '';
$message_type = '';

if (isset($_GET['delete_news']) && is_numeric($_GET['delete_news'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
        $stmt->execute(['id' => $_GET['delete_news']]);
        $message = 'Новость удалена';
        $message_type = 'success';
    } catch (Exception $e) {
        $message = 'Ошибка удаления: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Изменение статуса заявки
if (isset($_POST['update_status'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE meter_readings SET status = :status WHERE id = :id");
        $stmt->execute([
            'status' => $_POST['status'],
            'id' => $_POST['id']
        ]);
        $message = 'Статус обновлён';
        $message_type = 'success';
    } catch (Exception $e) {
        $message = 'Ошибка обновления: ' . $e->getMessage();
        $message_type = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO news (news_name, news_date, news_description) 
            VALUES (:name, :date, :description)
        ");
        $stmt->execute([
            'name' => $_POST['news_name'],
            'date' => $_POST['news_date'],
            'description' => $_POST['news_description']
        ]);
        $message = 'Новость добавлена';
        $message_type = 'success';
    } catch (Exception $e) {
        $message = 'Ошибка добавления: ' . $e->getMessage();
        $message_type = 'error';
    }
}

try {
    $pdo = getDBConnection();

    $news_stmt = $pdo->query("SELECT * FROM news ORDER BY news_date DESC");
    $news_list = $news_stmt->fetchAll();

    $readings_stmt = $pdo->query("SELECT * FROM meter_readings ORDER BY created_at DESC");
    $readings_list = $readings_stmt->fetchAll();

    $stats = [
        'news_count' => count($news_list),
        'readings_count' => count($readings_list),
        'new_readings' => count(array_filter($readings_list, fn($r) => $r['status'] === 'new'))
    ];
} catch (Exception $e) {
    $message = 'Ошибка загрузки данных: ' . $e->getMessage();
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - ПАО "Ставропольэнергосбыт"</title>
    <link rel="stylesheet" href="style.css">
   
</head>
<body>
    <div class="admin-panel">
        <header class="admin-header">
            <h1>Панель администратора</h1>
            <div class="admin-info">
                <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="admin-logout.php" class="logout-btn">Выйти</a>
            </div>
        </header>
        
        <div class="admin-content">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <!-- Статистика -->
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['news_count']; ?></div>
                    <div class="stat-label">Всего новостей</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['readings_count']; ?></div>
                    <div class="stat-label">Всего заявок</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['new_readings']; ?></div>
                    <div class="stat-label">Новых заявок</div>
                </div>
            </div>
            
            <!-- Вкладки -->
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('news')">📰 Новости</button>
                <button class="tab-btn" onclick="showTab('readings')">📋 Заявки</button>
                <button class="tab-btn" onclick="showTab('add')">➕ Добавить новость</button>
            </div>
            
            <!-- Вкладка: Новости -->
            <div id="news-tab" class="tab-content active">
                <div class="admin-section">
                    <h2>Список новостей</h2>
                    <?php if (empty($news_list)): ?>
                        <p>Новостей пока нет</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Заголовок</th>
                                    <th>Дата</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($news_list as $news): ?>
                                <tr>
                                    <td><?php echo $news['id']; ?></td>
                                    <td><?php echo htmlspecialchars(mb_substr($news['news_name'], 0, 50)); ?>...</td>
                                    <td><?php echo date('d.m.Y', strtotime($news['news_date'])); ?></td>
                                    <td>
                                        <a href="news.php" class="action-btn view">Просмотр</a>
                                        <a href="?delete_news=<?php echo $news['id']; ?>" 
                                           class="action-btn delete"
                                           onclick="return confirm('Удалить новость?')">Удалить</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Вкладка: Заявки -->
            <div id="readings-tab" class="tab-content">
                <div class="admin-section">
                    <h2>Заявки на передачу показаний</h2>
                    <?php if (empty($readings_list)): ?>
                        <p>Заявок пока нет</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ФИО</th>
                                    <th>Лицевой счёт</th>
                                    <th>Счётчик</th>
                                    <th>Причина</th>
                                    <th>Телефон</th>
                                    <th>Дата</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($readings_list as $reading): ?>
                                <tr>
                                    <td><?php echo $reading['id']; ?></td>
                                    <td><?php echo htmlspecialchars($reading['fio']); ?></td>
                                    <td><?php echo htmlspecialchars($reading['account_number']); ?></td>
                                    <td><?php echo htmlspecialchars($reading['meter_number']); ?></td>
                                    <td><?php echo htmlspecialchars($reading['reason']); ?></td>
                                    <td><?php echo htmlspecialchars($reading['phone']); ?></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($reading['created_at'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $reading['status']; ?>">
                                            <?php 
                                            $status_labels = [
                                                'new' => 'Новая',
                                                'processed' => 'Обработана',
                                                'rejected' => 'Отклонена'
                                            ];
                                            echo $status_labels[$reading['status']] ?? $reading['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $reading['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="padding: 0.25rem; border-radius: 4px;">
                                                <option value="new" <?php echo $reading['status'] === 'new' ? 'selected' : ''; ?>>Новая</option>
                                                <option value="processed" <?php echo $reading['status'] === 'processed' ? 'selected' : ''; ?>>Обработана</option>
                                                <option value="rejected" <?php echo $reading['status'] === 'rejected' ? 'selected' : ''; ?>>Отклонена</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Вкладка: Добавить новость -->
            <div id="add-tab" class="tab-content">
                <div class="admin-section">
                    <h2>Добавить новую новость</h2>
                    <form method="POST" class="add-news-form">
                        <input type="hidden" name="add_news" value="1">
                        
                        <div class="form-group">
                            <label for="news_name">Заголовок *</label>
                            <input type="text" id="news_name" name="news_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="news_date">Дата *</label>
                            <input type="date" id="news_date" name="news_date" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="news_description">Текст новости (HTML разрешён) *</label>
                            <textarea id="news_description" name="news_description" required></textarea>
                        </div>
                        
                        <button type="submit">Добавить новость</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // Скрываем все вкладки
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Показываем нужную
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>