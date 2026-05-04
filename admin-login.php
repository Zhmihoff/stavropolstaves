<?php
session_start();
require_once 'config.php';

$error = '';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin-panel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Введите логин и пароль';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, username, password, status FROM admins WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch();
            
            if ($admin && $password === $admin['password']) {
                if ($admin['status'] === 'active') {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    
                    $update = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
                    $update->execute(['id' => $admin['id']]);
                    
                    header('Location: admin-panel.php');
                    exit;
                } else {
                    $error = 'Аккаунт заблокирован';
                }
            } else {
                $error = 'Неверный логин или пароль';
            }
        } catch (Exception $e) {
            $error = 'Ошибка подключения к базе данных';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Вход администратора - ПАО "Ставропольэнергосбыт"</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-login-page">
        <div class="admin-login-container">
            <h1>Вход администратора</h1>
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="admin-login.php">
                <div class="form-group">
                    <label for="username">Логин</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">Войти</button>
            </form>
            <a href="index.html" class="back-link">Вернуться на сайт</a>
        </div>
    </div>
</body>
</html>