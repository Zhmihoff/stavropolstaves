<?php
require_once 'config.php';

$success = false;
$error = '';
$form_data = [
    'fio' => '',
    'meter_number' => '',
    'reason' => '',
    'description' => '',
    'phone' => '',
    'account_number' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['fio'] = trim($_POST['fio'] ?? '');
    $form_data['meter_number'] = trim($_POST['meter_number'] ?? '');
    $form_data['reason'] = trim($_POST['reason'] ?? '');
    $form_data['description'] = trim($_POST['description'] ?? '');
    $form_data['phone'] = trim($_POST['phone'] ?? '');
    $form_data['account_number'] = trim($_POST['account_number'] ?? '');
    
    $errors = [];
    
    if (empty($form_data['fio'])) $errors[] = 'Введите ФИО';
    if (empty($form_data['meter_number'])) $errors[] = 'Введите номер счётчика';
    if (empty($form_data['reason'])) $errors[] = 'Выберите причину';
    if (empty($form_data['phone'])) $errors[] = 'Введите номер телефона';
    if (empty($form_data['account_number'])) $errors[] = 'Введите номер лицевого счёта';
    
    if (!empty($form_data['phone']) && !preg_match('/^[\d\s\-\+\(\)]{10,}$/', $form_data['phone'])) {
        $errors[] = 'Неверный формат телефона';
    }
    
    if (!empty($form_data['account_number']) && !preg_match('/^[\d\-]{6,20}$/', $form_data['account_number'])) {
        $errors[] = 'Неверный формат лицевого счёта';
    }
    
    if (empty($errors)) {
        
        try {
            $pdo = getDBConnection();
            
            $stmt = $pdo->prepare("
                INSERT INTO meter_readings 
                (fio, account_number, meter_number, reason, phone, description, created_at) 
                VALUES (:fio, :account, :meter, :reason, :phone, :desc, NOW())
            ");
            
            $stmt->execute([
                ':fio' => $form_data['fio'],
                ':account' => $form_data['account_number'],
                ':meter' => $form_data['meter_number'],
                ':reason' => $form_data['reason'],
                ':phone' => $form_data['phone'],
                ':desc' => $form_data['description']
            ]);
            
            $success = true;
            
            $form_data = [
                'fio' => '',
                'meter_number' => '',
                'reason' => '',
                'description' => '',
                'phone' => '',
                'account_number' => ''
            ];
            
        } catch (PDOException $e) {
            $error = 'Ошибка сохранения в базу данных: ' . $e->getMessage();
        } catch (Exception $e) {
            $error = 'Ошибка: ' . $e->getMessage();
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/logo_small.png">
    <title>Передача показаний - ПАО "Ставропольэнергосбыт"</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="index.html" class="logo">
                <img src="logo.svg" alt="Ставропольэнергосбыт" class="logo-icon">
            </a>
            <nav>
                <ul>
                    <li><a href="about.html">О нас</a></li>
                    <li><a href="news.php">Новости</a></li>
                    <li><a href="contacts.html">Контакты</a></li>
                    <li><a href="meter.php">Передача показаний</a></li>
                </ul>
            </nav>
            <div class="phone-link">
                <svg class="phone-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span>+7(962)494-44-92</span>
            </div>
        </div>
    </header>

    <section class="meter-page-section">
        <div class="meter-page-container">
            <h1>Форма подачи обращений</h1>
            <p class="page-description">Заполните форму для подачи обращения. Все поля, отмеченные *, обязательны для заполнения.</p>
            
            <?php if ($success): ?>
                <div class="success-message">
                    обращение успешно зарегистрировано! В скором времени с вами свяжется наш оператор.
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message">
                     <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
            <form class="meter-form" method="POST" action="meter.php" id="meterForm">
                <div class="form-group">
                    <label for="fio">ФИО *</label>
                    <input type="text" id="fio" name="fio" value="<?php echo htmlspecialchars($form_data['fio']); ?>" 
                           placeholder="Иванов Иван Иванович" required>
                </div>
                
                <div class="form-group">
                    <label for="account_number">Номер лицевого счёта *</label>
                    <input type="text" id="account_number" name="account_number" 
                           value="<?php echo htmlspecialchars($form_data['account_number']); ?>" 
                           placeholder="123456" 
                           pattern="^\d{6}$" 
                           maxlength="6"
                           inputmode="numeric"
                           required>
                    <small>Только цифры, 6 символов</small>
                </div>
                
                <div class="form-group">
                    <label for="meter_number">Номер счётчика *</label>
                    <input type="text" id="meter_number" name="meter_number" 
                           value="<?php echo htmlspecialchars($form_data['meter_number']); ?>" 
                           placeholder="АБ123456789" required>
                </div>
                
                <div class="form-group">
                    <label for="reason">Причина обращения *</label>
                    <select id="reason" name="reason" required>
                        <option value="">Выберите причину</option>
                        <option value="monthly" <?php echo $form_data['reason'] === 'Ежемесячная' ? 'selected' : ''; ?>>Ежемесячная передача</option>
                        <option value="replacement" <?php echo $form_data['reason'] === 'Замена' ? 'selected' : ''; ?>>Замена счётчика</option>
                        <option value="correction" <?php echo $form_data['reason'] === 'Корректировка' ? 'selected' : ''; ?>>Корректировка показаний</option>
                        <option value="initial" <?php echo $form_data['reason'] === 'Первоначальная' ? 'selected' : ''; ?>>Первоначальная передача</option>
                        <option value="other" <?php echo $form_data['reason'] === 'Другое' ? 'selected' : ''; ?>>Другое</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="phone">Номер телефона *</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($form_data['phone']); ?>" 
                           placeholder="89624946692" 
                           pattern="^[\d\s\-\+\(\)]{10,}$" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание (необязательно)</label>
                    <textarea id="description" name="description" rows="4" 
                              placeholder="Дополнительная информация..."><?php echo htmlspecialchars($form_data['description']); ?></textarea>
                </div>
                
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="consent" required>
                        <span>Согласие на обработку персональных данных</span>
                    </label>
                </div>
                
                <button type="submit" class="submit-btn">Отправить показания</button>
            </form>
            <?php endif; ?>
            
           
        </div>
    </section>

    <footer id="contacts">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Навигация</h3>
                <ul>
                    <li><a href="index.html">Главная</a></li>
                    <li><a href="about.html">О компании</a></li>
                    <li><a href="news.php">Новости</a></li>
                    <li><a href="contacts.html">Контакты</a></li>
                    <li><a href="meter.php">Передача показаний</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Акционерам и инвесторам</h3>
                <ul>
                    <li>Лабунов Данила Алексеевич</li>
                    <li>23ИСИП-9-2</li>
                    <li>+7 (962) 494-66-92</li>
                    <li><a href="https://vk.com/zhmih26" target="_blank">Вконтакте</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 ПАО "Ставропольэнергосбыт"<br>Все права защищены</p>
        </div>
    </footer>

    <script>
        const phoneInput = document.getElementById('phone');

        function formatPhone(value) {
            let digits = value.replace(/\D/g, '');

            if (digits.length > 11) digits = digits.slice(0, 11);

            if (digits.length === 0) return '';
            if (digits.length <= 1) return '+7 (' + digits;
            if (digits.length <= 4) return '+7 (' + digits;
            if (digits.length <= 7) return '+7 (' + digits.slice(0, 3) + ') ' + digits.slice(3);
            if (digits.length <= 9) return '+7 (' + digits.slice(0, 3) + ') ' + digits.slice(3, 6) + '-' + digits.slice(6);
            if (digits.length <= 11) return '+7 (' + digits.slice(0, 3) + ') ' + digits.slice(3, 6) + '-' + digits.slice(6, 8) + '-' + digits.slice(8);

            return '+7 (' + digits.slice(0, 3) + ') ' + digits.slice(3, 6) + '-' + digits.slice(6, 8) + '-' + digits.slice(8, 10);
        }

        phoneInput.addEventListener('input', function(e) {
            const formatted = formatPhone(e.target.value);
            e.target.value = formatted;
        });

        phoneInput.addEventListener('focus', function(e) {
            if (e.target.value === '') {
                e.target.value = '+7 (';
            }
        });

        phoneInput.addEventListener('blur', function(e) {
            const digits = e.target.value.replace(/\D/g, '');
            if (digits.length < 11) {
                e.target.value = '';
            }
        });

        phoneInput.addEventListener('keydown', function(e) {
            if ([8, 9, 13, 27, 46].includes(e.keyCode) ||
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }

            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && 
                (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });        
        
        const accountInput = document.getElementById('account_number');

        accountInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^\d\-]/g, '').slice(0, 20);
        });

        document.getElementById('meterForm').addEventListener('submit', function(e) {
            const phone = phoneInput.value.replace(/\D/g, '');
            const account = accountInput.value.replace(/\D/g, '');

            if (phone.length < 11) {
                e.preventDefault();
                alert('Пожалуйста, введите корректный номер телефона (11 цифр)');
                phoneInput.focus();
                return false;
            }

            if (account.length < 6) {
                e.preventDefault();
                alert('Номер лицевого счёта должен содержать минимум 6 цифр');
                accountInput.focus();
                return false;
        	}
    	});
	</script>
</body>
</html>